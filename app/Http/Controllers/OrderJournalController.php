<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OrderJournal;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\OrderJournalApproval;
use Illuminate\Support\Facades\Auth;

class OrderJournalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $journals = OrderJournal::latest()->paginate(10);

        $users = User::where('organization_id', $user->organization_id)->get();

        return view('order_journals.index', compact('journals', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        // Хэрэв хэрэглэгч админ бол бүх байгууллагыг харуулна
        if ($user->permissionLevel?->code === 'ADM') {
            $organizations = Organization::all();
        } else {
            // Админ биш бол зөвхөн хэрэглэгчийн байгууллага
            $organizations = Organization::where('id', $user->organization_id)->get();
        }


        return view('order_journals.create', compact('organizations'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_type' => 'required|string|max:255',
            'content' => 'required|string',
            'planned_start_date' => 'required|date',
            'planned_end_date' => 'required|date',
            'approver_name' => 'nullable|string|max:255',
            'approver_position' => 'nullable|string|max:255',
        ]);

        OrderJournal::create($request->all());

        return redirect()->route('order-journals.index')
            ->with('success', 'Order Journal created successfully.');
    }

    // Бусад алба руу илгээх
    public function forward(Request $request, OrderJournal $orderJournal)
    {
        $request->validate([
            'approvers' => 'required|array',
            'approvers.*' => 'exists:users,id',
        ]);

        $orderJournal->status = OrderJournal::STATUS_FORWARDED;
        $orderJournal->save();

        foreach ($request->approvers as $userId) {
            $orderJournal->approvals()->create([
                'user_id' => $userId,
                'approved' => null,
            ]);
        }

        return redirect()->route('order-journals.show', $orderJournal)
            ->with('success', 'Захиалгыг forward хийлээ, санал авах хүсэлт илгээгдлээ.');
    }

    // Санал өгөх
    public function approve(Request $request, OrderJournalApproval $approval)
    {
        $request->validate([
            'approved' => 'required|boolean',
            'comment' => 'nullable|string|max:1000',
        ]);

        $approval->update([
            'approved' => $request->approved,
            'comment' => $request->comment,
        ]);

        $orderJournal = $approval->orderJournal;

        if ($orderJournal->approvals()->where('approved', false)->count() > 0) {
            $orderJournal->status = OrderJournal::STATUS_CANCELLED;
        } elseif ($orderJournal->approvals()->whereNull('approved')->count() === 0) {
            $orderJournal->status = OrderJournal::STATUS_APPROVED;
        }

        $orderJournal->save();

        return redirect()->back()->with('success', 'Таны санал амжилттай хадгалагдлаа.');
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
        $user = Auth::user();

        // Хэрэв хэрэглэгч админ бол бүх байгууллагыг харуулна
        if ($user->permissionLevel?->code === 'ADM') {
            $organizations = Organization::all();
        } else {
            // Админ биш бол зөвхөн хэрэглэгчийн байгууллага
            $organizations = Organization::where('id', $user->organization_id)->get();
        }

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
