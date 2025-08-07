<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;
    /**
     * تسجيل الدخول لعامل النظافة
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        // البحث عن عامل النظافة بالهاتف
        $cleaner = Cleaner::where('phone', $request->phone)->first();

        if (!$cleaner) {
            return $this->errorResponse('بيانات تسجيل الدخول غير صحيحة');
        }

        // التحقق من كلمة المرور (إذا كانت موجودة في جدول Cleaner)
        if ($cleaner->password && !Hash::check($request->password, $cleaner->password)) {
            return $this->errorResponse('بيانات تسجيل الدخول غير صحيحة');
        }

        // التحقق من حالة عامل النظافة
        if ($cleaner->status !== 'active') {
            return $this->forbiddenResponse('حسابك معطل. يرجى التواصل مع الإدارة.');
        }

        // إنشاء token للـ Cleaner
        $token = $cleaner->createToken('cleaner-app')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'token' => $token,
        ]);
    }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'تم تسجيل الخروج بنجاح');
    }

    /**
     * عرض بيانات الملف الشخصي
     */
    public function profile(Request $request)
    {
        $cleaner = $request->user()->only(['id', 'name', 'email', 'phone', 'address']);

        return $this->successResponse($cleaner);
    }

    /**
     * تحديث بيانات الملف الشخصي
     */
    public function updateProfile(Request $request)
    {
        $cleaner = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:cleaners,email,' . $cleaner->id,
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $cleaner->update($request->only(['name', 'email', 'phone', 'address']));

        return $this->updatedResponse(null, 'تم تحديث البيانات بنجاح');
    }

    /**
     * تحديث كلمة المرور
     */
    public function updatePassword(Request $request)
    {
        $cleaner = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
            'new_password_confirmation' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        // التحقق من كلمة المرور الحالية
        if (!Hash::check($request->current_password, $cleaner->password)) {
            return $this->errorResponse('كلمة المرور الحالية غير صحيحة', 400);
        }

        // تحديث كلمة المرور
        $cleaner->update([
            'password' => Hash::make($request->new_password)
        ]);

        return $this->successResponse(null, 'تم تحديث كلمة المرور بنجاح');
    }
}
