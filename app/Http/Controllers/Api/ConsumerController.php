<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consumer;
use Illuminate\Http\Request;

class ConsumerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $consumers = Consumer::select('id', 'name', 'account_no', 'address')
            ->orderBy('name')
            ->get();

        return response()->json($consumers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'account_no' => 'required|string|max:255|unique:consumers',
            'address' => 'required|string|max:500',
            'nic' => 'required|string|max:12|unique:consumers',
        ]);

        $consumer = Consumer::create($validated);

        return response()->json($consumer, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Consumer $consumer)
    {
        return response()->json($consumer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Consumer $consumer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'account_no' => 'required|string|max:255|unique:consumers,account_no,' . $consumer->id,
            'address' => 'required|string|max:500',
            'nic' => 'required|string|max:12|unique:consumers,nic,' . $consumer->id,
        ]);

        $consumer->update($validated);

        return response()->json($consumer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Consumer $consumer)
    {
        $consumer->delete();

        return response()->json(['message' => 'Consumer deleted successfully']);
    }
}
