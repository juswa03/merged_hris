<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all devices from biometric_devices table
        $biometricDevices = DB::table('biometric_devices')->get();

        foreach ($biometricDevices as $bioDevice) {
            // Check if device already exists in devices table
            $existingDevice = DB::table('devices')
                ->where('device_uid', $bioDevice->device_uid)
                ->first();

            if ($existingDevice) {
                // Update existing device to be biometric type with correct data
                DB::table('devices')
                    ->where('device_uid', $bioDevice->device_uid)
                    ->update([
                        'device_type' => 'biometric',
                        'device_name' => $bioDevice->device_name,
                        'device_model' => $bioDevice->device_model,
                        'serial_number' => $bioDevice->serial_number,
                        'firmware_version' => $bioDevice->firmware_version,
                        'ip_address' => $bioDevice->ip_address,
                        'port' => $bioDevice->port,
                        'location' => $bioDevice->location,
                        'status' => $bioDevice->status,
                        'last_sync_at' => $bioDevice->last_sync_at,
                        'last_heartbeat_at' => $bioDevice->last_heartbeat_at,
                        'total_capacity' => $bioDevice->total_capacity,
                        'templates_count' => $bioDevice->templates_count,
                        'settings' => $bioDevice->settings,
                        'notes' => $bioDevice->notes,
                        'updated_at' => now(),
                    ]);

                echo "✅ Updated existing device: {$bioDevice->device_uid}\n";
            } else {
                // Insert as new biometric device
                DB::table('devices')->insert([
                    'device_uid' => $bioDevice->device_uid,
                    'device_type' => 'biometric',
                    'device_name' => $bioDevice->device_name,
                    'device_model' => $bioDevice->device_model,
                    'serial_number' => $bioDevice->serial_number,
                    'firmware_version' => $bioDevice->firmware_version,
                    'ip_address' => $bioDevice->ip_address,
                    'port' => $bioDevice->port,
                    'location' => $bioDevice->location,
                    'status' => $bioDevice->status,
                    'last_sync_at' => $bioDevice->last_sync_at,
                    'last_heartbeat_at' => $bioDevice->last_heartbeat_at,
                    'total_capacity' => $bioDevice->total_capacity,
                    'templates_count' => $bioDevice->templates_count,
                    'settings' => $bioDevice->settings,
                    'notes' => $bioDevice->notes,
                    'created_at' => $bioDevice->created_at,
                    'updated_at' => $bioDevice->updated_at,
                    // Set generic device fields to null
                    'device_id' => null,
                    'building' => null,
                    'floor' => null,
                    'room' => null,
                    'department_id' => null,
                ]);

                echo "✅ Inserted new device: {$bioDevice->device_uid}\n";
            }
        }

        echo "🔄 Migration completed. Migrated " . count($biometricDevices) . " device(s).\n";

        // Drop the old biometric_devices table
        Schema::dropIfExists('biometric_devices');

        echo "🗑️  Dropped biometric_devices table.\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate biometric_devices table
        Schema::create('biometric_devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_uid')->unique();
            $table->string('device_name');
            $table->string('device_model')->default('ZKTeco Live10R');
            $table->string('serial_number')->nullable();
            $table->string('firmware_version')->nullable();
            $table->string('ip_address')->nullable();
            $table->integer('port')->default(4370);
            $table->string('location')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance', 'error'])->default('active');
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamp('last_heartbeat_at')->nullable();
            $table->integer('total_capacity')->default(3000);
            $table->integer('templates_count')->default(0);
            $table->json('settings')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Copy biometric devices back
        $biometricDevices = DB::table('devices')
            ->where('device_type', 'biometric')
            ->get();

        foreach ($biometricDevices as $device) {
            DB::table('biometric_devices')->insert([
                'device_uid' => $device->device_uid,
                'device_name' => $device->device_name,
                'device_model' => $device->device_model,
                'serial_number' => $device->serial_number,
                'firmware_version' => $device->firmware_version,
                'ip_address' => $device->ip_address,
                'port' => $device->port,
                'location' => $device->location,
                'status' => $device->status,
                'last_sync_at' => $device->last_sync_at,
                'last_heartbeat_at' => $device->last_heartbeat_at,
                'total_capacity' => $device->total_capacity,
                'templates_count' => $device->templates_count,
                'settings' => $device->settings,
                'notes' => $device->notes,
                'created_at' => $device->created_at,
                'updated_at' => $device->updated_at,
            ]);
        }

        echo "🔄 Rollback completed. Restored biometric_devices table.\n";
    }
};
