<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    use ApiResponse;

    /**
     * جلب الإعدادات
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $settings = Setting::select(['name', 'phone', 'email', 'address', 'logo'])->first();

        if (!$settings) {
            return $this->errorResponse('لا توجد إعدادات متاحة', 404);
        }

        return $this->successResponse($settings, 'تم جلب الإعدادات بنجاح');
    }
}
