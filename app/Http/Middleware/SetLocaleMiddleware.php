<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // الحصول على اللغة من الهيدر
        $locale = $request->header('Accept-Language', 'ar'); // إذا لم يُرسل، الافتراضي "ar"

        // التحقق مما إذا كانت اللغة مدعومة في التطبيق
        $availableLocales = ['ar', 'en'];

        if (!in_array($locale, $availableLocales)) {
            $locale = 'ar'; // إذا لم تكن اللغة مدعومة، اجعلها "ar"
        }

        // تعيين اللغة للتطبيق
        App::setLocale($locale);

        return $next($request);
    }
}
