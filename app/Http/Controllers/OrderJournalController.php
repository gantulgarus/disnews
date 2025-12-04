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

        // Query эхлүүлэх
        $query = OrderJournal::query()->latest();

        // Хэрэглэгч админ эсэхийг шалгах
        if ($user->organization_id != 5) {
            // Админ биш бол зөвхөн өөрийн байгууллагын захиалгууд
            $query->where('organization_id', $user->organization_id);
        }

        // Pagination + withQueryString, хайлт зэрэг хадгалах боломжтой
        $journals = $query->paginate(25)->withQueryString();

        // Өөрийн байгууллагын хэрэглэгчид авах
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
    // public function approve(Request $request, OrderJournalApproval $approval)
    // {
    //     $request->validate([
    //         'approved' => 'required|boolean',
    //         'comment' => 'nullable|string|max:1000',
    //     ]);

    //     $approval->update([
    //         'approved' => $request->approved,
    //         'comment' => $request->comment,
    //     ]);

    //     $orderJournal = $approval->orderJournal;

    //     if ($orderJournal->approvals()->where('approved', false)->count() > 0) {
    //         $orderJournal->status = OrderJournal::STATUS_CANCELLED;
    //     } elseif ($orderJournal->approvals()->whereNull('approved')->count() === 0) {
    //         $orderJournal->status = OrderJournal::STATUS_APPROVED;
    //     }

    //     $orderJournal->save();

    //     return redirect()->back()->with('success', 'Таны санал амжилттай хадгалагдлаа.');
    // }

    // Санал өгөх (approval дээр санал өгөх)
    public function approveOpinion(Request $request, OrderJournalApproval $approval)
    {
        // Зөвхөн өөрийн санал өгөх эрхтэй
        if ($approval->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Та энэ саналыг өгөх эрхгүй байна.');
        }

        // Аль хэдийн санал өгсөн бол дахин өгөх боломжгүй
        if (!is_null($approval->approved)) {
            return redirect()->back()->with('error', 'Та аль хэдийн санал өгсөн байна.');
        }

        $request->validate([
            'approved' => 'required|boolean',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Санал өгөх
        $approval->update([
            'approved' => $request->approved,
            'comment' => $request->comment,
        ]);

        $message = $request->approved ? 'Таны зөвшөөрсөн санал амжилттай хадгалагдлаа.' : 'Таны татгалзсан санал амжилттай хадгалагдлаа.';

        return redirect()->back()->with('success', $message);
    }

    public function approve(Request $request, OrderJournal $orderJournal)
    {
        $user = auth()->user();
        $oldStatus = $orderJournal->status; // Хуучин төлөв хадгалах

        $request->validate([
            'approved' => 'nullable|boolean',
            'comment' => 'nullable|string|max:1000',
            'action' => 'nullable|string|in:approve,reject',
        ]);

        // Default approval үүсгэх буюу update
        $approval = $orderJournal->approvals()->updateOrCreate(
            [
                'order_journal_id' => $orderJournal->id,
                'user_id' => $user->id,
            ],
            [
                'approved' => null,
                'comment' => $request->comment,
            ]
        );

        // Диспетчер - аваарын захиалгыг зөвшөөрөх эсвэл татгалзах
        if ($user->permissionLevel?->code === 'DISP' && $orderJournal->order_type === 'Аваарын') {
            $approved = $request->input('action') === 'approve';
            $newStatus = $approved ? OrderJournal::STATUS_APPROVED : OrderJournal::STATUS_CANCELLED;

            $approval->update([
                'approved' => $approved,
                'comment' => $request->comment,
            ]);

            // Төлөв солих
            $orderJournal->status = $newStatus;
            $orderJournal->save();

            // Түүх хадгалах
            \App\Models\OrderJournalStatusHistory::create([
                'order_journal_id' => $orderJournal->id,
                'user_id' => $user->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'comment' => $request->comment,
            ]);

            $message = $approved ? 'Аваарын захиалга амжилттай батлагдлаа.' : 'Аваарын захиалга татгалзагдлаа.';
            return redirect()->back()->with('success', $message);
        }

        // Диспетчерийн албаны дарга - зөвшөөрөх эсвэл татгалзах
        elseif ($user->permissionLevel?->code === 'DISP_LEAD') {
            $approved = $request->input('action') === 'approve';
            $newStatus = $approved ? OrderJournal::STATUS_ACCEPTED : OrderJournal::STATUS_CANCELLED;

            $approval->update([
                'approved' => $approved,
                'comment' => $request->comment,
            ]);

            // Төлөв солих
            $orderJournal->status = $newStatus;
            $orderJournal->save();

            // Түүх хадгалах
            \App\Models\OrderJournalStatusHistory::create([
                'order_journal_id' => $orderJournal->id,
                'user_id' => $user->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'comment' => $request->comment,
            ]);

            $message = $approved ? 'Захиалга зөвшөөрөгдлөө.' : 'Захиалга татгалзагдлаа.';
            return redirect()->back()->with('success', $message);
        }

        // Ерөнхий диспетчер - батлах эсвэл цуцлах
        elseif ($user->permissionLevel?->code === 'GEN_DISP') {
            $approved = $request->input('action') === 'approve';
            $newStatus = $approved ? OrderJournal::STATUS_APPROVED : OrderJournal::STATUS_CANCELLED;

            $approval->update([
                'approved' => $approved,
                'comment' => $request->comment,
            ]);

            // Төлөв солих
            $orderJournal->status = $newStatus;
            $orderJournal->save();

            // Түүх хадгалах
            \App\Models\OrderJournalStatusHistory::create([
                'order_journal_id' => $orderJournal->id,
                'user_id' => $user->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'comment' => $request->comment,
            ]);

            $message = $approved ? 'Захиалга ерөнхий диспетчерээр батлагдлаа.' : 'Захиалга ерөнхий диспетчерээр цуцлагдлаа.';
            return redirect()->back()->with('success', $message);
        }

        return redirect()->back()->with('error', 'Батлах эрх байхгүй байна.');
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
