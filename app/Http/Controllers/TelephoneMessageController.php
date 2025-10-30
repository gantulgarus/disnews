<?php

namespace App\Http\Controllers;

use App\Models\TelephoneMessage;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TelephoneMessageController extends Controller
{
    /**
     * Мэдээний жагсаалт
     */
    public function index()
    {
        $orgId = (string) auth()->user()->organization_id;


        // Ирсэн мэдээ (тухайн байгууллага хүлээн авагчид орсон)
        $received = TelephoneMessage::whereHas('receivers', function ($q) use ($orgId) {
            $q->where('organization_id', $orgId);
        })->with(['receivers'])->latest()->get();

        // dd($received);

        // Явсан мэдээ (тухайн байгууллага илгээгч)
        $sent = TelephoneMessage::where('sender_org_id', $orgId)
            ->latest()->get();

        return view('telephone_messages.index', compact('received', 'sent'));
    }

    /**
     * Шинэ мэдээ үүсгэх form
     */
    public function create()
    {
        $organizations = Organization::all();
        return view('telephone_messages.create', compact('organizations'));
    }

    /**
     * Шинэ мэдээ хадгалах
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_org_ids' => 'required|array',
            'receiver_org_ids.*' => 'exists:organizations,id',
            'content' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $validated['sender_org_id'] = auth()->user()->organization_id;
        $validated['created_user_id'] = auth()->id();

        // Хавсралт хадгалах
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('telephone_messages', 'public');
            $validated['attachment'] = $path;
        }

        // TelephoneMessage::create($data);
        // Телефон мэдээ үүсгэх
        $telephoneMessage = TelephoneMessage::create([
            'status' => 'Шинээр ирсэн', // анхдагч төлөв
            'sender_org_id' => auth()->user()->organization_id,
            'receiver_org_ids' => $validated['receiver_org_ids'],
            'content' => $validated['content'],
            'attachment' => $path,
            'created_user_id' => auth()->id(),
        ]);
        // Хүлээн авагч pivot-д нэмэх
        $syncData = [];
        foreach ($validated['receiver_org_ids'] as $orgId) {
            $syncData[$orgId] = ['status' => 'Шинээр ирсэн'];
        }
        $telephoneMessage->receivers()->sync($syncData);

        return redirect()->route('telephone_messages.index')
            ->with('success', 'Телефон мэдээ амжилттай илгээгдлээ.');
    }

    /**
     * Нэг мэдээний дэлгэрэнгүй
     */
    public function show(TelephoneMessage $telephoneMessage)
    {
        $userOrgId = auth()->user()->organization_id;

        // Pivot table-д тухайн байгууллагын статусыг update
        $receiver = $telephoneMessage->receivers()->where('organization_id', $userOrgId)->first();

        if ($receiver) {
            $pivotStatus = $receiver->pivot->status ?? 'Шинээр ирсэн';
            if ($pivotStatus === 'Шинээр ирсэн') {
                $telephoneMessage->receivers()->updateExistingPivot($userOrgId, ['status' => 'Хүлээн авсан']);
            }
        }

        // Үүний дараа view рүү илгээх
        $telephoneMessage->load('receivers'); // updated pivot мэдээллийг авахын тулд

        return view('telephone_messages.show', compact('telephoneMessage'));
    }

    /**
     * Засварлах form
     */
    public function edit(TelephoneMessage $telephoneMessage)
    {
        $organizations = Organization::all();
        return view('telephone_messages.edit', compact('telephoneMessage', 'organizations'));
    }

    /**
     * Засвар хадгалах
     */
    public function update(Request $request, TelephoneMessage $telephoneMessage)
    {
        $data = $request->validate([
            'status' => 'required|string',
            'receiver_org_ids' => 'required|array',
            'receiver_org_ids.*' => 'exists:organizations,id',
            'content' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        // Хавсралт шинэчилэх
        if ($request->hasFile('attachment')) {
            if ($telephoneMessage->attachment) {
                Storage::disk('public')->delete($telephoneMessage->attachment);
            }
            $file = $request->file('attachment');
            $data['attachment'] = $file->store('telephone_messages', 'public');
        }

        $telephoneMessage->update($data);

        return redirect()->route('telephone_messages.index')
            ->with('success', 'Телефон мэдээ шинэчлэгдлээ.');
    }

    /**
     * Устгах
     */
    public function destroy(TelephoneMessage $telephoneMessage)
    {
        if ($telephoneMessage->attachment) {
            Storage::disk('public')->delete($telephoneMessage->attachment);
        }
        $telephoneMessage->delete();

        return redirect()->route('telephone_messages.index')
            ->with('success', 'Мэдээ устгагдлаа.');
    }
}
