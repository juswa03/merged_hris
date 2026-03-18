<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Device;
use App\Models\Department;
class DeviceController extends Controller
{
    //

       /**
     * Display a listing of devices.
     */
    public function index(Request $request)
    {
        $type = $request->get('type'); // 'generic', 'biometric', or null for all

        $query = Device::with('department');

        if ($type === 'generic') {
            $query->generic();
        } elseif ($type === 'biometric') {
            $query->biometric();
        }

        $devices = $query->get();
        return response()->json($devices);
    }

    /**
     * Show the form for creating a new device.
     */
    public function create()
    {
        $departments = Department::all();
        // return view('devices.create', compact('departments'));
    }

    /**
     * Store a newly created device.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_type' => 'required|in:generic,biometric',
            'device_uid' => 'required|unique:devices,device_uid',

            // Generic device fields
            'device_id' => 'required_if:device_type,generic',
            'building' => 'required_if:device_type,generic|string',
            'floor' => 'required_if:device_type,generic|string',
            'department_id' => 'required_if:device_type,generic|exists:tbl_departments,id',
            'room' => 'required_if:device_type,generic|string',

            // Biometric device fields
            'device_name' => 'required_if:device_type,biometric|string',
            'device_model' => 'nullable|string',
            'location' => 'nullable|string',
            'ip_address' => 'nullable|ip',
            'status' => 'nullable|in:active,inactive,maintenance,error',
        ]);

        $device = Device::create($validated);

        return response()->json([
            'message' => 'Device created successfully',
            'data' => $device
        ], 201);
    }

    /**
     * Display the specified device.
     */
    public function show(Device $device)
    {
        $device->load('department');
        return response()->json($device);
    }

    /**
     * Show the form for editing a device.
     */
    public function edit(Device $device)
    {
        $departments = Department::all();
        // return view('devices.edit', compact('device', 'departments'));
    }

    /**
     * Update the specified device.
     */
    public function update(Request $request, Device $device)
    {
        $rules = [
            'device_uid' => 'sometimes|unique:devices,device_uid,' . $device->id,
        ];

        // Add validation rules based on device type
        if ($device->isGeneric()) {
            $rules = array_merge($rules, [
                'device_id' => 'sometimes|string',
                'building' => 'sometimes|string',
                'floor' => 'sometimes|string',
                'department_id' => 'sometimes|exists:tbl_departments,id',
                'room' => 'sometimes|string',
            ]);
        } else {
            $rules = array_merge($rules, [
                'device_name' => 'sometimes|string',
                'device_model' => 'sometimes|string',
                'location' => 'sometimes|string',
                'ip_address' => 'sometimes|ip',
                'status' => 'sometimes|in:active,inactive,maintenance,error',
            ]);
        }

        $validated = $request->validate($rules);
        $device->update($validated);

        return response()->json([
            'message' => 'Device updated successfully',
            'data' => $device
        ]);
    }

    /**
     * Remove the specified device.
     */
    public function destroy(Device $device)
    {
        $device->delete();

        return response()->json(['message' => 'Device deleted successfully']);
    }
}
