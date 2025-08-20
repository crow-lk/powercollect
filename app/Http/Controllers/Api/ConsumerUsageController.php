<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConsumerUsage;
use Illuminate\Http\Request;

class ConsumerUsageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ConsumerUsage::with(['consumer:id,name,account_no', 'equipment:id,type,brand,model'])
            ->select('id', 'consumer_id', 'equipment_id', 'kVA', 'start_time', 'end_time', 'date', 'created_at');

        // Filter by date range if provided
        if ($request->has('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Filter by consumer if provided
        if ($request->has('consumer_id')) {
            $query->where('consumer_id', $request->consumer_id);
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
            'consumer_id' => 'required|exists:consumers,id',
            'equipment_id' => 'required|exists:equipments,id',
            'kVA' => 'required|numeric|min:0.001',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'date' => 'required|date|before_or_equal:today',
        ]);

        $usage = ConsumerUsage::create($validated);
        $usage->load(['consumer:id,name,account_no', 'equipment:id,type,brand,model']);

        return response()->json($usage, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ConsumerUsage $consumerUsage)
    {
        $consumerUsage->load(['consumer:id,name,account_no,address', 'equipment:id,type,brand,model']);
        return response()->json($consumerUsage);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ConsumerUsage $consumerUsage)
    {
        $validated = $request->validate([
            'consumer_id' => 'required|exists:consumers,id',
            'equipment_id' => 'required|exists:equipments,id',
            'kVA' => 'required|numeric|min:0.001',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'date' => 'required|date|before_or_equal:today',
        ]);

        $consumerUsage->update($validated);
        $consumerUsage->load(['consumer:id,name,account_no', 'equipment:id,type,brand,model']);

        return response()->json($consumerUsage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ConsumerUsage $consumerUsage)
    {
        $consumerUsage->delete();

        return response()->json(['message' => 'Usage record deleted successfully']);
    }

    /**
     * Get summary statistics
     */
    public function statistics()
    {
        $totalRecords = ConsumerUsage::count();
        $totalConsumers = ConsumerUsage::distinct('consumer_id')->count();
        $totalEquipment = ConsumerUsage::distinct('equipment_id')->count();
        $totalKva = ConsumerUsage::sum('kVA');

        return response()->json([
            'total_records' => $totalRecords,
            'total_consumers' => $totalConsumers,
            'total_equipment' => $totalEquipment,
            'total_kva' => round($totalKva, 2),
        ]);
    }
}
