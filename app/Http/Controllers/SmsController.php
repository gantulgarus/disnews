<?php

namespace App\Http\Controllers;

use App\Models\SmsGroup;
use App\Models\SmsMessage;
use App\Models\SmsRecipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SmsController extends Controller
{
    public function index()
    {
        $groups = SmsGroup::withCount('recipients')->get();
        return view('sms.index', compact('groups'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'group_ids' => 'required|array|min:1',
            'group_ids.*' => 'exists:sms_groups,id',
            'message' => 'required|string|max:500',
        ]);

        $messageText = $request->message;
        $groupIds = $request->group_ids;

        // 1️⃣ Сонгосон бүх бүлгийн хэрэглэгчдийг авна
        $recipients = SmsRecipient::whereIn('sms_group_id', $groupIds)->get();

        if ($recipients->isEmpty()) {
            return back()->with('error', 'Сонгосон бүлгүүдэд хүлээн авагч байхгүй байна.');
        }

        // 2️⃣ MessageOut хүснэгтэд оруулна (sms_db connection ашиглана)
        foreach ($recipients as $recipient) {
            DB::connection('sms_db')->table('MessageOut')->insert([
                'MessageTo' => $recipient->phone,
                'MessageText' => $messageText,
            ]);
        }

        // 3️⃣ Илгээсэн мессеж бүртгэх (олон бүлэг → нэг мессеж бүртгэл)
        SmsMessage::create([
            'sms_group_id' => null, // олон бүлэг тул нэг бүлэгт хамааруулахгүй
            'group_ids' => $groupIds, // сонгосон бүх group_id-г JSON байдлаар хадгална
            'message' => $messageText,
            'recipients_count' => $recipients->count(),
            'sent_at' => now(),
        ]);

        // 4️⃣ Амжилттай илгээгдсэн тухай буцаах
        return back()->with('success', 'SMS амжилттай илгээгдлээ (' . $recipients->count() . ' хүнд илгээгдсэн).');
    }



    // public function send(Request $request)
    // {
    //     $request->validate([
    //         'group_id' => 'required|exists:sms_groups,id',
    //         'message' => 'required|string|max:500',
    //     ]);

    //     // 1️⃣ Сонгосон бүлгийг холбогдох хэрэглэгчидтэй нь авна
    //     $group = SmsGroup::with('recipients')->findOrFail($request->group_id);
    //     $recipients = $group->recipients;

    //     // 2️⃣ MessageOut хүснэгтэд оруулна (модем автоматаар илгээдэг)
    //     foreach ($recipients as $recipient) {
    //         DB::connection('sms_db')->table('MessageOut')->insert([
    //             'MessageTo' => $recipient->phone, // илгээх хүний дугаар
    //             'MessageText' => $request->message, // мессежийн агуулга
    //         ]);
    //     }

    //     // 3️⃣ Илгээсэн мессежийг бүртгэх
    //     SmsMessage::create([
    //         'sms_group_id' => $group->id,
    //         'message' => $request->message,
    //         'recipients_count' => $recipients->count(),
    //         'sent_at' => now(),
    //     ]);

    //     // 4️⃣ Амжилттай илгээгдсэн тухай буцаах
    //     return back()->with('success', 'SMS амжилттай илгээгдлээ!');
    // }

    public function messages()
    {
        $messages = SmsMessage::with('group')
            ->orderByDesc('sent_at')
            ->paginate(10); // хуудаслалт

        $allGroups = SmsGroup::pluck('name', 'id')->toArray();

        return view('sms.messages', compact('messages', 'allGroups'));
    }
}