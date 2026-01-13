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
        // check permission
        if (!auth()->user()->hasPermission('order_journals.view')) {
            abort(403);
        }

        $user = Auth::user();
        $userOrgCode = (string) ($user->organization?->org_code ?? '');
        $userPermissionCode = $user->permissionLevel?->code ?? '';

        // Query эхлүүлэх
        $query = OrderJournal::with('organization')->latest();

        // ДҮТ хэрэглэгч биш бол зөвхөн өөрийн байгууллагын захиалгууд
        if ($userOrgCode !== '102') {
            $query->where('organization_id', $user->organization_id);
        } else {
            // ДҮТ-ийн хэрэглэгчдийн хандалтын эрх
            if ($userPermissionCode === 'DISP') {
                // Диспетчер инженер - бүх захиалгууд (STATUS_NEW болон бусад бүх төлөв)
                // Нэмэлт шүүлт хэрэггүй
            } elseif ($userPermissionCode === 'DISP_LEAD' || $userPermissionCode === 'GEN_DISP' || $userPermissionCode === 'THA_LEAD') {
                // Диспетчерийн албаны дарга болон Ерөнхий диспетчер
                // STATUS_NEW-с бусад бүх төлөвтэй захиалгууд
                $query->where('status', '!=', OrderJournal::STATUS_NEW);
            } else {
                // ДҮТ-ийн бусад хэрэглэгчид - зөвхөн санал авахаар сонгогдсон захиалгууд
                $query->whereHas('approvals', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }
        }

        // order_number filter
        if ($orderNumber = request('order_number')) {
            $query->where('order_number', $orderNumber);
        }

        // organization_name filter
        if ($organization_id = request('organization_id')) {
            $query->where('organization_id', $organization_id);
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

        if ($order_type = request('order_type')) {
            $query->where('order_type', $order_type);
        }

        if ($plannedDate = request('planned_start_date')) {
            $query->whereDate('planned_start_date', $plannedDate);
        }


        $journals = $query->paginate(25)->withQueryString();

        // Өөрийн байгууллагын хэрэглэгчид авах
        $excludedPermissions = ['DISP_LEAD', 'GEN_DISP', 'DISP'];

        $users = User::where('organization_id', $user->organization_id)
            ->whereHas('permissionLevel', function ($query) use ($excludedPermissions) {
                $query->whereNotIn('code', $excludedPermissions);
            })
            ->get();

        $organizations = Organization::orderBy('name')->get();

        return view('order_journals.index', compact('journals', 'users', 'organizations'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->hasPermission('order_journals.create')) {
            return redirect()->back()->with('error', 'Танд энэ үйлдлийг хийх эрх байхгүй байна!');
        }

        $user = Auth::user();

        // Хэрэв хэрэглэгч админ бол бүх байгууллагыг харуулна
        if ($user->organization->org_code === 102) {
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
            'tze_dis_name' => 'nullable|string|max:255',
        ]);

        OrderJournal::create($request->all());

        return redirect()->route('order-journals.index')
            ->with('success', 'Захиалгын журнал амжилттай үүслээ.');
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderJournal $orderJournal)
    {
        $user = Auth::user();
        $excludedPermissions = ['DISP_LEAD', 'GEN_DISP', 'DISP'];

        $users = User::where('organization_id', $user->organization_id)
            ->whereHas('permissionLevel', function ($query) use ($excludedPermissions) {
                $query->whereNotIn('code', $excludedPermissions);
            })
            ->get();

        return view('order_journals.show', compact('orderJournal', 'users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrderJournal $orderJournal)
    {
        if (!auth()->user()->hasPermission('order_journals.edit')) {
            return redirect()->back()->with('error', 'Танд энэ үйлдлийг хийх эрх байхгүй байна!');
        }

        $user = Auth::user();

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
            'organization_id' => 'required|exists:organizations,id',
            'order_type' => 'required|string|max:255',
            'content' => 'required|string',
            'planned_start_date' => 'required|date',
            'planned_end_date' => 'required|date',
            'approver_name' => 'nullable|string|max:255',
            'approver_position' => 'nullable|string|max:255',
            'tze_dis_name' => 'nullable|string|max:255',
        ]);

        $orderJournal->update($input);

        return redirect()->route('order-journals.index')->with('success', 'Захиалгын журнал амжилттай шинэчлэгдлээ.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderJournal $orderJournal)
    {
        if (!auth()->user()->hasPermission('order_journals.delete')) {
            return redirect()->back()->with('error', 'Танд энэ үйлдлийг хийх эрх байхгүй байна!');
        }

        $orderJournal->delete();

        return redirect()->route('order-journals.index')->with('success', 'Захиалгын журнал амжилттай устгагдлаа.');
    }

    // Бусад алба руу санал авахаар илгээх
    // Бусад алба руу санал авахаар илгээх
    public function forward(Request $request, OrderJournal $orderJournal)
    {
        $request->validate([
            'approvers' => 'nullable|array',
            'approvers.*' => 'exists:users,id',
        ]);

        $oldStatus = $orderJournal->status;
        $newStatus = OrderJournal::STATUS_FORWARDED;

        $comment = $request->comment ?? '';

        // Санал авах хэрэглэгчид байгаа эсэхийг шалгах
        if (!empty($request->approvers)) {
            // Илгээгдсэн хэрэглэгчдийн мэдээлэл авах
            $approverUsers = User::whereIn('id', $request->approvers)
                ->get()
                ->map(function ($user) {
                    return $user->name . ' (' . ($user->division?->Div_name ?? 'Алба тодорхойгүй') . ')';
                })
                ->toArray();

            $comment .= ' ' . count($request->approvers) . ' хэрэглэгчид рүү санал авахаар илгээв: ' . PHP_EOL;
            $comment .= implode(', ', $approverUsers);

            // Approvals үүсгэх
            foreach ($request->approvers as $userId) {
                $orderJournal->approvals()->create([
                    'user_id' => $userId,
                    'approved' => null,
                ]);
            }
        } else {
            $comment .= ' Санал авахгүйгээр шууд илгээв.';
        }

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

        return redirect()->route('order-journals.show', $orderJournal)
            ->with('success', 'Захиалгыг forward хийлээ, санал авах хүсэлт илгээгдлээ.');
    }

    // Санал өгөх хэрэглэгчид шинэчлэх
    public function updateApprovers(Request $request, OrderJournal $orderJournal)
    {
        $request->validate([
            'approvers' => 'required|array',
            'approvers.*' => 'exists:users,id',
            'comment' => 'nullable|string|max:1000',
        ]);

        $newApprovers = collect($request->approvers);

        /**
         * 🔒 1️⃣ Санал өгсөн хэрэглэгчийг хасах гэж байгаа эсэхийг шалгах
         */
        $approvedUserIds = $orderJournal->approvals()
            ->whereNotNull('approved')
            ->pluck('user_id');

        // approved хэрэглэгч newApprovers дотор байхгүй бол = хасах гэж байна
        if ($approvedUserIds->diff($newApprovers)->isNotEmpty()) {
            return back()->with(
                'error',
                'Санал өгсөн хэрэглэгчийг хасах боломжгүй'
            );
        }

        /**
         * 2️⃣ Одоогийн approval-ууд
         */
        $existingUserIds = $orderJournal->approvals()
            ->pluck('user_id');

        /**
         * 3️⃣ ХАСАГДСАН хэрэглэгчдийг устгах
         */
        $orderJournal->approvals()
            ->whereNotIn('user_id', $newApprovers)
            ->delete();

        /**
         * 4️⃣ ШИНЭЭР нэмэгдсэн хэрэглэгчдэд approval үүсгэх
         */
        $newApprovers
            ->diff($existingUserIds)
            ->each(function ($userId) use ($orderJournal) {
                $orderJournal->approvals()->create([
                    'user_id' => $userId,
                    'approved' => null,
                ]);
            });

        /**
         * 5️⃣ Forward тайлбар хадгалах
         */
        if ($request->filled('comment')) {
            $orderJournal->forward_comment = $request->comment;
            $orderJournal->save();
        }

        return redirect()->back()
            ->with('success', 'Санал өгөх хэрэглэгчид амжилттай шинэчлэгдлээ');
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
