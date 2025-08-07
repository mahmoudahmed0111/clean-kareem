<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    use ApiResponse;
    /**
     * عرض قائمة المخزون
     */
    public function index(Request $request)
    {
        $inventory = Inventory::when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->category, function ($query, $category) {
                $query->where('category', $category);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->min_quantity, function ($query, $minQuantity) {
                $query->where('quantity', '>=', $minQuantity);
            })
            ->when($request->max_quantity, function ($query, $maxQuantity) {
                $query->where('quantity', '<=', $maxQuantity);
            })
            ->orderBy('name')
            ->paginate($request->per_page ?? 15);

        return $this->successResponse($inventory);
    }

    /**
     * عرض بيانات عنصر مخزون محدد
     */
    public function show(Inventory $inventory)
    {
        return $this->successResponse($inventory);
    }
}
