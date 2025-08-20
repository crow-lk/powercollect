<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerUsage;
use Illuminate\Http\Request;

class CustomerUsageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CustomerUsage::with(['customer:id,name,account_no', 'equipment:id,type,brand,model'])
            ->select('id', 'customer_id', 'equipment_id', 'kVA', 'start_time', 'end_time', 'date', 'created_at');

        // Filter by date range if provided
        if ($request->has('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Filter by customer if provided
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by equipment if provided
        if ($request->has('equipment_id')) {
            $query->where('equipment_id', $request->equipment_id);
        }

        $usages = $query->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(20);

        return response()->json($usages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'equipment_id' => 'required|exists:equipments,id',
            'kVA' => 'required|numeric|min:0.001',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'date' => 'required|date|before_or_equal:today',
        ]);

        $usage = CustomerUsage::create($validated);
        $usage->load(['customer:id,name,account_no', 'equipment:id,type,brand,model']);

        return response()->json($usage, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomerUsage $customerUsage)
    {
        $customerUsage->load(['customer:id,name,account_no,address', 'equipment:id,type,brand,model']);
        return response()->json($customerUsage);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomerUsage $customerUsage)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'equipment_id' => 'required|exists:equipments,id',
            'kVA' => 'required|numeric|min:0.001',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'date' => 'required|date|before_or_equal:today',
        ]);

        $customerUsage->update($validated);
        $customerUsage->load(['customer:id,name,account_no', 'equipment:id,type,brand,model']);

        return response()->json($customerUsage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerUsage $customerUsage)
    {
        $customerUsage->delete();

        return response()->json(['message' => 'Usage record deleted successfully']);
    }

    /**
     * Get summary statistics
     */
    public function statistics()
    {
        $totalRecords = CustomerUsage::count();
        $totalCustomers = CustomerUsage::distinct('customer_id')->count();
        $totalEquipment = CustomerUsage::distinct('equipment_id')->count();
        $totalKva = CustomerUsage::sum('kVA');

        return response()->json([
            'total_records' => $totalRecords,
            'total_customers' => $totalCustomers,
            'total_equipment' => $totalEquipment,
            'total_kva' => round($totalKva, 2),
        ]);
    }
}
