<?php

namespace App\Services;

use App\Models\Device;

class DeviceService
{
    public static function getAllDevices()
    {
        $devices = Device::orderBy('name')
        ->get();

        return $devices;
    }

    public static function getAllDevicesPaginated($perPage)
    {
        $devices = Device::orderBy('name')
            ->paginate();

        return $devices;
    }
}
