<?php

namespace App\Http\Controllers;

use App\Models\OrderJournal;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderJournalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $journals = OrderJournal::latest()->paginate(10);
        return view('order_journals.index', compact('journals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $organizations = Organization::all();
        return view('order_journals.create', compact('organizations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $request->validate([
            'order_number' => 'required|string|max:255',
            'status' => 'required|integer',
            'organization_id' => 'required|exists:organizations,id',
            'order_type' => 'required|string|max:255',
            'content' => 'required|string',
            'planned_start_date' => 'required|date',
            'planned_end_date' => 'required|date',
            'approver_name' => 'nullable|string|max:255',
            'approver_position' => 'nullable|string|max:255',
        ]);

        // created_user_id-г нэвтэрсэн хэрэглэгчийн ID-аар нэмэх
        $input['created_user_id'] = Auth::id();

        OrderJournal::create($input);

        return redirect()->route('order-journals.index')->with('success', 'Order Journal created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderJournal $orderJournal)
    {
        return view('order_journals.show', compact('orderJournal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrderJournal $orderJournal)
    {
        $organizations = Organization::all();
        return view('order_journals.edit', compact('orderJournal', 'organizations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrderJournal $orderJournal)
    {
        $input = $request->all();

        $request->validate([
            'order_number' => 'required|string|max:255',
            'status' => 'required|integer',
            'organization_id' => 'required|exists:organizations,id',
            'order_type' => 'required|string|max:255',
            'content' => 'required|string',
            'planned_start_date' => 'required|date',
            'planned_end_date' => 'required|date',
            'approver_name' => 'nullable|string|max:255',
            'approver_position' => 'nullable|string|max:255',
        ]);

        $orderJournal->update($input);

        return redirect()->route('order-journals.index')->with('success', 'Order Journal updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderJournal $orderJournal)
    {
        $orderJournal->delete();

        return redirect()->route('order-journals.index')->with('success', 'Order Journal deleted successfully.');
    }
}