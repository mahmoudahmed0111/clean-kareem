<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * استجابة نجاح
     */
    public function successResponse($data = null, $message = 'تم بنجاح', $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => $status,
        ], $status);
    }

    /**
     * استجابة خطأ
     */
    public function errorResponse($message = 'حدث خطأ', $status = 400, $errors = null): JsonResponse
    {
        $response = [
            'data' => null,
            'message' => $message,
            'status' => $status,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    /**
     * استجابة خطأ في التحقق من البيانات
     */
    public function validationErrorResponse($errors, $message = 'بيانات غير صحيحة'): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors);
    }

    /**
     * استجابة خطأ في المصادقة
     */
    public function unauthorizedResponse($message = 'غير مصرح لك بالوصول'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }

    /**
     * استجابة خطأ في الصلاحيات
     */
    public function forbiddenResponse($message = 'ليس لديك صلاحية للوصول لهذا المورد'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }

    /**
     * استجابة مورد غير موجود
     */
    public function notFoundResponse($message = 'المورد غير موجود'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * استجابة تم إنشاء بنجاح
     */
    public function createdResponse($data = null, $message = 'تم الإنشاء بنجاح'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * استجابة تم التحديث بنجاح
     */
    public function updatedResponse($data = null, $message = 'تم التحديث بنجاح'): JsonResponse
    {
        return $this->successResponse($data, $message, 200);
    }

    /**
     * استجابة تم الحذف بنجاح
     */
    public function deletedResponse($message = 'تم الحذف بنجاح'): JsonResponse
    {
        return $this->successResponse(null, $message, 200);
    }
}
