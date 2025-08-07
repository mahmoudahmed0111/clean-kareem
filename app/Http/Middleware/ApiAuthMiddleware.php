<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseTrait;

class ApiAuthMiddleware
{
    use ResponseTrait;

    public function handle(Request $request, Closure $next) {
        if (!auth('api')->check()) {
            return $this->apiResponse(null, 'رمز الجلسة غير صالح', 401);
        }
        $user = auth('api')->user();
        // if ($user && $user->is_active == 0) {
        //     return $this->apiResponse(null, 'تم تعطيل الحساب. يرجى التواصل مع الإدارة.', 403);
        // }

        // فحص إذا كان العميل ليس لديه باقة
        if (!$user->package_id) {
            return $this->apiResponse(null, 'يرجى الاشتراك في باقة للمتابعة.', 403);
        }

        // فحص انتهاء الباقة أو التجربة المجانية
        if (isset($user->end_date) && now()->greaterThan($user->end_date)) {
            return $this->apiResponse(null, 'انتهت الباقة أو التجربة المجانية. يرجى الاشتراك للمتابعة.', 403);
        }

        return $next($request); 
    }
}
