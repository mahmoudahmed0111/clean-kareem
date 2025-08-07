<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use App\Models\RegularCleaning;
use App\Models\DeepCleaning;
use App\Models\Maintenance;
use App\Models\PestControl;
use App\Models\Damage;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CleanerController extends Controller
{
    use ApiResponse;
    /**
     * عرض قائمة المنظفين
     */
    public function index(Request $request)
    {
        $cleaners = Cleaner::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->paginate($request->per_page ?? 15);

        return $this->successResponse($cleaners);
    }

    /**
     * عرض بيانات منظف محدد
     */
    public function show(Cleaner $cleaner)
    {
        return $this->successResponse($cleaner);
    }

    /**
     * تحديث بيانات المنظف
     */
    public function update(Request $request, Cleaner $cleaner)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'email' => 'sometimes|email|max:255',
            'national_id' => 'sometimes|string|max:50',
            'address' => 'sometimes|string|max:255',
            'hire_date' => 'sometimes|date',
            'status' => 'sometimes|string|in:active,inactive',
            'image' => 'sometimes|string',
        ]);

        $cleaner->update($request->only([
            'name', 'phone', 'email', 'national_id', 'address', 'hire_date', 'status', 'image'
        ]));

        return $this->updatedResponse($cleaner, 'تم تحديث بيانات المنظف بنجاح');
    }

    /**
     * عرض مهام المنظف
     */
    public function tasks(Cleaner $cleaner)
    {
        $tasks = collect();

        // مهام التنظيف العادي
        $regularCleanings = RegularCleaning::where('cleaner_id', $cleaner->id)
            ->with(['chalet'])
            ->get()
            ->map(function ($task) {
                $task->type = 'regular_cleaning';
                return $task;
            });

        // مهام التنظيف العميق
        $deepCleanings = DeepCleaning::where('cleaner_id', $cleaner->id)
            ->with(['chalet'])
            ->get()
            ->map(function ($task) {
                $task->type = 'deep_cleaning';
                return $task;
            });

        // مهام الصيانة
        $maintenance = Maintenance::where('cleaner_id', $cleaner->id)
            ->with(['chalet'])
            ->get()
            ->map(function ($task) {
                $task->type = 'maintenance';
                return $task;
            });

        // مهام مكافحة الآفات
        $pestControls = PestControl::where('cleaner_id', $cleaner->id)
            ->with(['chalet'])
            ->get()
            ->map(function ($task) {
                $task->type = 'pest_control';
                return $task;
            });

        // مهام الأضرار
        $damages = Damage::where('cleaner_id', $cleaner->id)
            ->with(['chalet'])
            ->get()
            ->map(function ($task) {
                $task->type = 'damage';
                return $task;
            });

        $tasks = $regularCleanings
            ->concat($deepCleanings)
            ->concat($maintenance)
            ->concat($pestControls)
            ->concat($damages)
            ->sortBy('created_at');

        return $this->successResponse([
            'cleaner' => $cleaner,
            'tasks' => $tasks,
            'stats' => [
                'total_tasks' => $tasks->count(),
                'regular_cleanings' => $regularCleanings->count(),
                'deep_cleanings' => $deepCleanings->count(),
                'maintenance' => $maintenance->count(),
                'pest_controls' => $pestControls->count(),
                'damages' => $damages->count(),
            ]
        ]);
    }
}
