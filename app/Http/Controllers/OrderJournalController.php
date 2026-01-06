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
        $query = OrderJournal::with('organization')->latest();

        // ДҮТ хэрэглэгч биш бол зөвхөн өөрийн байгууллагын захиалгууд
        $userOrgCode = (string) ($user->organization?->org_code ?? '');
        if ($userOrgCode !== '102') {
            $query->where('organization_id', $user->organization_id);
        }
        // order_number filter
        if ($orderNumber = request('order_number')) {
            $query->where('order_number', $orderNumber);
        }

        // organization_name filter
        if ($orgName = request('organization_name')) {
            $query->whereHas('organization', function ($q) use ($orgName) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($orgName) . '%']);
            });
        }

        // status filter
        $allowedStatuses = [
            OrderJournal::STATUS_NEW,
            OrderJournal::STATUS_APPROVED,
            OrderJournal::STATUS_CANCELLED,
            OrderJournal::STATUS_OPEN,
            OrderJournal::STATUS_CLOSED,
            OrderJournal::STATUS_POSTPONED,
            OrderJournal::STATUS_IN_REVIEW,
        ];
        if (!is_null(request('status')) && request('status') !== '') {
            $status = (int) request('status');
            if (in_array($status, $allowedStatuses)) {
                $query->where('status', $status);
            }
        }

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

    // Бусад алба руу санал авахаар илгээх
    public function forward(Request $request, OrderJournal $orderJournal)
    {
        $request->validate([
            'approvers' => 'required|array',
            'approvers.*' => 'exists:users,id',
        ]);

        $oldStatus = $orderJournal->status;
        $newStatus = OrderJournal::STATUS_FORWARDED;

        // Илгээгдсэн хэрэглэгчдийн мэдээлэл авах
        $approverUsers = User::whereIn('id', $request->approvers)
            ->get()
            ->map(function ($user) {
                return $user->name . ' (' . ($user->division?->Div_name ?? 'Алба тодорхойгүй') . ')';
            })
            ->toArray();

        $comment = $request->comment . ' ' . count($request->approvers) . ' хэрэглэгчид рүү санал авахаар илгээв: ' . PHP_EOL;
        $comment .= implode(', ', $approverUsers);

        // Захиалгыг Хянагдаж байгаа төлөвт оруулах
        $orderJournal->status = OrderJournal::STATUS_IN_REVIEW;
        $orderJournal->dut_dispatcher_id = auth()->id();
        $orderJournal->save();

        // Түүх хадгалах
        \App\Models\OrderJournalStatusHistory::create([
            'order_journal_id' => $orderJournal->id,
            'user_id' => auth()->id(),
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'comment' => $comment,
        ]);

        // Approvals үүсгэх
        foreach ($request->approvers as $userId) {
            $orderJournal->approvals()->create([
                'user_id' => $userId,
                'approved' => null,
            ]);
        }

        return redirect()->route('order-journals.show', $orderJournal)
            ->with('success', 'Захиалгыг forward хийлээ, санал авах хүсэлт илгээгдлээ.');
    }

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
            'attachment' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
        ]);

        $data = [
            'approved' => $request->approved,
            'comment' => $request->comment,
        ];

        // 📎 Файл байвал хадгалах
        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')
                ->store('order-journal-approvals', 'public');
        }

        $approval->update($data);

        $message = $request->approved ? 'Таны зөвшөөрсөн санал амжилттай хадгалагдлаа.' : 'Таны татгалзсан санал амжилттай хадгалагдлаа.';

        return redirect()->back()->with('success', $message);
    }

    // Захиалгыг батлах/цацлах
    public function approve(Request $request, OrderJournal $orderJournal)
    {
        $user = auth()->user();
        $oldStatus = $orderJournal->status; // Хуучин төлөв хадгалах

        $request->validate([
            'approved' => 'nullable|boolean',
            'comment' => 'nullable|string|max:1000',
            'action' => 'nullable|string|in:approve,reject',
        ]);

        // Диспетчер - аваарын захиалгыг батлах эсвэл цуцлах
        if ($user->permissionLevel?->code === 'DISP' && $orderJournal->order_type === 'Аваарын') {
            $approved = $request->input('action') === 'approve';
            $newStatus = $approved ? OrderJournal::STATUS_APPROVED : OrderJournal::STATUS_CANCELLED;

            // Төлөв солих
            $orderJournal->status = $newStatus;
            $orderJournal->dut_dispatcher_id = $user->id;
            $orderJournal->save();

            // Түүх хадгалах
            \App\Models\OrderJournalStatusHistory::create([
                'order_journal_id' => $orderJournal->id,
                'user_id' => $user->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'comment' => $request->comment,
            ]);

            $message = $approved ? 'Аваарын захиалга амжилттай батлагдлаа.' : 'Аваарын захиалга цуцлагдсан.';
            return redirect()->back()->with('success', $message);
        }

        // Диспетчерийн албаны дарга - зөвшөөрөх эсвэл татгалзах
        elseif ($user->permissionLevel?->code === 'DISP_LEAD') {
            $approved = $request->input('action') === 'approve';
            $newStatus = $approved ? OrderJournal::STATUS_ACCEPTED : OrderJournal::STATUS_CANCELLED;

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

            // Төлөв солих
            $orderJournal->update(['status' => $newStatus]);

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

    // Батлагдсан захиалгыг нээх
    public function open(Request $request, $id)
    {
        $journal = OrderJournal::findOrFail($id);

        // Батлагдсан байх ёстой
        if ($journal->status !== OrderJournal::STATUS_APPROVED) {
            return back()->with('error', 'Захиалга нээх боломжгүй байна.');
        }

        $journal->real_start_date = $request->real_start_date;
        $journal->status = OrderJournal::STATUS_OPEN; // Нээлттэй төлөв
        $journal->save();

        // Түүх үүсгэх (сонголт)
        $journal->statusHistories()->create([
            'user_id' => auth()->id(),
            'old_status' => OrderJournal::STATUS_APPROVED,
            'new_status' => OrderJournal::STATUS_OPEN,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Захиалгыг нээлээ.');
    }

    // Нээлттэй захиалгыг хаах
    public function close(Request $request, $id)
    {
        $journal = OrderJournal::findOrFail($id);

        // Нээлттэй байх ёстой
        if ($journal->status !== OrderJournal::STATUS_OPEN) {
            return back()->with('error', 'Захиалга хаах боломжгүй байна.');
        }

        $journal->real_end_date = $request->real_end_date;
        $journal->status = OrderJournal::STATUS_CLOSED; // Хаалттай төлөв
        $journal->save();

        // Түүх үүсгэх
        $journal->statusHistories()->create([
            'user_id' => auth()->id(),
            'old_status' => OrderJournal::STATUS_OPEN,
            'new_status' => OrderJournal::STATUS_CLOSED,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Захиалгыг хаалаа.');
    }
}
