<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemSetting;

class SystemSettingController extends Controller
{
    /**
     * 获取系统设置
     */
    public function index()
    {
        $settings = SystemSetting::pluck('value', 'key');

        return response()->json(['data' => $settings]);
    }

    /**
     * 更新系统设置
     */
    public function update(Request $request)
    {
        $data = $request->all();

        foreach ($data as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return response()->json(['message' => '设置保存成功']);
    }
}
