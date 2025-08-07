<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;

class CheckActivation
{
    public function handle(Request $request, Closure $next)
    {
        $setting = Setting::first();

        if (!$setting || !$setting->is_active) {
            if (!in_array($request->route()->getName(), ['activation.form', 'activation.submit'])) {
                return redirect()->route('activation.form');
            }
        }

        return $next($request);
    }
}
