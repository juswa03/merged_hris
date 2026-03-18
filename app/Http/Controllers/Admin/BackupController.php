<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupController extends Controller
{
    private string $backupDisk = 'local';
    private string $backupDir  = 'backups';

    public function index()
    {
        $backups = Backup::latest()->paginate(20);

        $stats = [
            'total'     => Backup::count(),
            'completed' => Backup::where('status', 'completed')->count(),
            'failed'    => Backup::where('status', 'failed')->count(),
            'total_size'=> Backup::where('status', 'completed')->sum('size_bytes'),
        ];

        // Disk space on the storage path
        $storagePath  = storage_path();
        $diskFree     = disk_free_space($storagePath);
        $diskTotal    = disk_total_space($storagePath);

        return view('admin.backups.index', compact('backups', 'stats', 'diskFree', 'diskTotal'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'type' => 'required|in:database,storage,full',
        ]);

        $type      = $request->type;
        $timestamp = now()->format('Y-m-d_His');
        $filename  = "backup_{$type}_{$timestamp}.zip";
        $dir       = storage_path("app/{$this->backupDir}");

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $zipPath = "{$dir}/{$filename}";

        $backup = Backup::create([
            'filename'   => $filename,
            'type'       => $type,
            'path'       => $zipPath,
            'status'     => 'pending',
            'created_by' => auth()->id(),
        ]);

        try {
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException("Cannot create zip file at {$zipPath}");
            }

            if (in_array($type, ['database', 'full'])) {
                $sqlFile = $this->dumpDatabase();
                $zip->addFile($sqlFile, 'database.sql');
            }

            if (in_array($type, ['storage', 'full'])) {
                $this->addDirectoryToZip($zip, storage_path('app/public'), 'storage/public');
            }

            $zip->close();

            // Clean up temp SQL file
            if (isset($sqlFile) && file_exists($sqlFile)) {
                unlink($sqlFile);
            }

            $backup->update([
                'status'     => 'completed',
                'size_bytes' => file_exists($zipPath) ? filesize($zipPath) : 0,
            ]);

            return back()->with('success', "Backup created: {$filename}");

        } catch (\Exception $e) {
            $backup->update(['status' => 'failed', 'notes' => $e->getMessage()]);
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function download(Backup $backup)
    {
        if (! $backup->fileExists()) {
            return back()->with('error', 'Backup file no longer exists on disk.');
        }

        return response()->download($backup->path, $backup->filename);
    }

    public function destroy(Backup $backup)
    {
        if ($backup->fileExists()) {
            unlink($backup->path);
        }

        $backup->delete();

        return back()->with('success', 'Backup record deleted.');
    }

    // -- Private helpers --

    private function dumpDatabase(): string
    {
        $config = config('database.connections.' . config('database.default'));

        $host     = $config['host']     ?? '127.0.0.1';
        $port     = $config['port']     ?? '3306';
        $database = $config['database'] ?? '';
        $username = $config['username'] ?? '';
        $password = $config['password'] ?? '';

        $tmpFile  = storage_path('app/backups/_tmp_dump_' . time() . '.sql');

        // Use --column-statistics=0 for MySQL 8+ compatibility; password via env var for security
        $env     = "MYSQL_PWD=" . escapeshellarg($password);
        $command = sprintf(
            '%s mysqldump --host=%s --port=%s --user=%s --single-transaction --no-tablespaces --column-statistics=0 %s > %s 2>&1',
            PHP_OS_FAMILY === 'Windows' ? '' : $env,
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($tmpFile)
        );

        if (PHP_OS_FAMILY === 'Windows') {
            // On Windows, pass password via env
            putenv("MYSQL_PWD={$password}");
        }

        exec($command, $output, $returnCode);

        if (PHP_OS_FAMILY === 'Windows') {
            putenv('MYSQL_PWD=');
        }

        if ($returnCode !== 0 || ! file_exists($tmpFile) || filesize($tmpFile) < 100) {
            // Fallback: PHP-based dump for small databases
            $tmpFile = $this->phpDumpDatabase($config, $tmpFile);
        }

        return $tmpFile;
    }

    private function phpDumpDatabase(array $config, string $outFile): string
    {
        $pdo = DB::connection()->getPdo();
        $dbName = $config['database'];

        $sql = "-- HRIS Database Dump\n-- Generated: " . now() . "\n\n";
        $sql .= "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n\n";

        $tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            // Table structure
            $createResult = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_NUM);
            $sql .= "\nDROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $createResult[1] . ";\n\n";

            // Table data (batch 500 rows at a time)
            $stmt = $pdo->query("SELECT * FROM `{$table}`");
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $cols = array_keys($rows[0] ?? []);

            foreach (array_chunk($rows, 500) as $chunk) {
                if (empty($cols)) break;
                $colList = '`' . implode('`, `', $cols) . '`';
                $values  = array_map(function ($row) use ($pdo) {
                    return '(' . implode(', ', array_map(fn($v) => $v === null ? 'NULL' : $pdo->quote($v), $row)) . ')';
                }, $chunk);
                $sql .= "INSERT INTO `{$table}` ({$colList}) VALUES\n" . implode(",\n", $values) . ";\n";
            }
            $sql .= "\n";
        }

        $sql .= "\nSET FOREIGN_KEY_CHECKS=1;\n";
        file_put_contents($outFile, $sql);

        return $outFile;
    }

    private function addDirectoryToZip(ZipArchive $zip, string $sourceDir, string $zipBase): void
    {
        if (! is_dir($sourceDir)) return;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceDir, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $relativePath = $zipBase . '/' . ltrim(str_replace($sourceDir, '', $file->getPathname()), '/\\');
                $zip->addFile($file->getPathname(), $relativePath);
            }
        }
    }
}
