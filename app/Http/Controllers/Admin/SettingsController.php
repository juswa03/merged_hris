<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::all()->keyBy('key');

        // Real system info
        $dbSizeBytes = $this->getDatabaseSize();
        $dbSizeMb    = $dbSizeBytes ? round($dbSizeBytes / 1024 / 1024, 2) : null;

        return view('admin.settings.settings', compact('settings', 'dbSizeMb'));
    }

    public function update(Request $request)
    {
        $group = $request->input('group', 'general');

        $data = $request->except(['_token', '_method', 'group']);

        foreach ($data as $key => $value) {
            SystemSetting::where('key', $key)->update(['value' => $value]);
        }

        // Handle boolean fields that won't be in POST when unchecked
        $booleanKeys = SystemSetting::where('group', $group)
            ->where('type', 'boolean')
            ->pluck('key');

        foreach ($booleanKeys as $k) {
            if (!array_key_exists($k, $data)) {
                SystemSetting::where('key', $k)->update(['value' => '0']);
            }
        }

        return redirect()->route('admin.settings.index', ['tab' => $group])
            ->with('success', ucfirst($group) . ' settings updated successfully.');
    }

    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Application cache cleared successfully.');
    }

    private function getDatabaseSize(): ?int
    {
        try {
            $dbName = DB::connection()->getDatabaseName();
            $result = DB::select("
                SELECT SUM(data_length + index_length) AS size
                FROM information_schema.TABLES
                WHERE table_schema = ?
            ", [$dbName]);

            return $result[0]->size ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
