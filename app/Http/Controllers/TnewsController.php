<?php

namespace App\Http\Controllers;

use App\Models\Tnews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;



class TnewsController extends Controller

{
    public function index()
    {
        $Tnews = Tnews::all();
        return view('tnews.index', compact('Tnews'));
    }

    public function create()

    {
        return view('tnews.create');
    }


    public function edit($id)
    {
        $tnews = Tnews::findOrFail($id);
        return view('tnews.edit', compact('tnews'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required',
            'TZE' => 'required|string|max:255',
            'tasralt' => 'required|string',
            'ArgaHemjee' => 'nullable|string',
            'HyzErchim' => 'nullable|string',
            'send_telegram' => 'nullable|boolean',
        ]);

        $tnews = Tnews::create([
            'date' => $request->date,
            'time' => $request->time,
            'TZE' => $request->TZE,
            'tasralt' => $request->tasralt,
            'ArgaHemjee' => $request->ArgaHemjee,
            'HyzErchim' => $request->HyzErchim,
            'send_telegram' => $request->has('send_telegram'),
        ]);

        // ✅ Хэрэв Telegram руу илгээхийг сонгосон бол энд мессеж илгээнэ
        if ($request->has('send_telegram')) {
            $isSent = $this->sendTelegramMessage($tnews);

            // Амжилттай илгээгдсэн бол send_telegram утгыг true болгох
            if ($isSent) {
                $tnews->update(['send_telegram' => true]);
            }
        }

        return redirect()->route('tnews.index')->with('success', 'Тасралтын мэдээ амжилттай хадгалагдлаа.');
    }



    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'required',
            'TZE' => 'required|string',
            'tasralt' => 'required|string',
            'ArgaHemjee' => 'nullable|string',
            'HyzErchim' => 'nullable|string',
            'send_telegram' => 'nullable|boolean',
        ]);

        $tnews = Tnews::findOrFail($id);
        $tnews->update([
            'date' => $validated['date'],
            'time' => $validated['time'],
            'TZE' => $validated['TZE'],
            'tasralt' => $validated['tasralt'],
            'ArgaHemjee' => $validated['ArgaHemjee'] ?? null,
            'HyzErchim' => $validated['HyzErchim'] ?? null,
            'send_telegram' => $request->has('send_telegram'),
        ]);

        // ✅ Хэрэв Telegram руу илгээхийг сонгосон бол энд мессеж илгээнэ
        if ($request->has('send_telegram')) {
            $isSent = $this->sendTelegramMessage($tnews);

            // Амжилттай илгээгдсэн бол send_telegram утгыг true болгох
            if ($isSent) {
                $tnews->update(['send_telegram' => true]);
            }
        }

        return redirect()->route('tnews.index')->with('success', 'Амжилттай шинэчлэгдлээ.');
    }

    public function show($id)
    {
        $tnews = Tnews::findOrFail($id);
        return view('tnews.show', compact('tnews'));
    }


    public function destroy($id)
    {
        $tnews = Tnews::findOrFail($id);
        $tnews->delete();

        return redirect()->route('tnews.index')->with('success', 'Амжилттай устгагдлаа');
    }

    private function escapeMarkdownV2($text)
    {
        $text = (string) $text;

        $replace_pairs = [
            '_' => '\_',
            '*' => '\*',
            '[' => '\[',
            ']' => '\]',
            '(' => '\(',
            ')' => '\)',
            '~' => '\~',
            '`' => '\`',
            '>' => '\>',
            '#' => '\#',
            '+' => '\+',
            '-' => '\-',  // ⚠️ Гол засвар
            '=' => '\=',
            '|' => '\|',
            '{' => '\{',
            '}' => '\}',
            '.' => '\.',
            '!' => '\!',
            // ":" тэмдэгтээ ч шаардлагатай бол escape хийх боломжтой
            ':' => '\:',
        ];

        return strtr($text, $replace_pairs);
    }

    private function sendTelegramMessage($tnews)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chat_id = env('TELEGRAM_CHAT_ID');

        $message = "⚡️ *Тасралтын мэдээ:*\n\n"
            . "*🏢 ТЗЭ:* " . $this->escapeMarkdownV2($tnews->TZE) . "\n"
            . "*🛠 Тасралт:* " . $this->escapeMarkdownV2($tnews->tasralt) . "\n"
            . "*🛠 Тайлбар:* " . $this->escapeMarkdownV2($tnews->ArgaHemjee ?? '') . "\n"
            . "*📅 Огноо:* " . $this->escapeMarkdownV2($tnews->date) . "\n";

        try {
            $response = Http::withOptions(['allow_redirects' => true])->post(
                "https://api.telegram.org/bot{$token}/sendMessage",
                [
                    'chat_id' => $chat_id,
                    'text' => $message,
                    'parse_mode' => 'MarkdownV2',
                ]
            );

            if ($response->failed()) {
                Log::error('Telegram message failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Telegram message exception: ' . $e->getMessage());
        }
    }
}
