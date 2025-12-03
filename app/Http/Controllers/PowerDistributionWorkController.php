<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\PowerDistributionWork;

class PowerDistributionWorkController extends Controller
{
    public function index(Request $request)
    {
        $query = PowerDistributionWork::query();

        if ($request->tze) {
            $query->where('tze', 'like', "%{$request->tze}%");
        }

        if ($request->repair_work) {
            $query->where('repair_work', 'like', "%{$request->repair_work}%");
        }

        if ($request->date) {
            $query->whereDate('date', $request->date);
        }

        $works = $query->latest()->paginate(10)->withQueryString();

        return view('power_distribution_works.index', compact('works'));
    }


    public function create()
    {
        return view('power_distribution_works.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tze' => 'required|string|max:255',
            'repair_work' => 'required|string|max:255',
            'description' => 'nullable|string',
            'restricted_energy' => 'nullable|numeric',
            'date' => 'required|date',
        ]);

        $work = PowerDistributionWork::create([
            'tze' => $request->tze,
            'repair_work' => $request->repair_work,
            'description' => $request->description,
            'restricted_energy' => $request->restricted_energy,
            'date' => $request->date,
            'user_id' => Auth::id(),
            'send_telegram' => $request->has('send_telegram'),
        ]);

        // Хэрэв checkbox чагталсан бол Telegram руу илгээх
        if ($request->has('send_telegram')) {
            $isSent = $this->sendTelegramMessage($work);

            // Амжилттай илгээгдсэн бол send_telegram утгыг true болгох
            if ($isSent) {
                $work->update(['send_telegram' => true]);
            }
        }

        return redirect()->route('power-distribution-works.index')
            ->with('success', 'Захиалгат ажил амжилттай нэмэгдлээ.');
    }

    public function show(PowerDistributionWork $powerDistributionWork)
    {
        return view('power_distribution_works.show', compact('powerDistributionWork'));
    }

    public function edit(PowerDistributionWork $powerDistributionWork)
    {
        return view('power_distribution_works.edit', compact('powerDistributionWork'));
    }

    public function update(Request $request, PowerDistributionWork $powerDistributionWork)
    {
        $request->validate([
            'tze' => 'required|string|max:255',
            'repair_work' => 'required|string|max:255',
            'description' => 'nullable|string',
            'restricted_energy' => 'nullable|numeric',
            'date' => 'required|date',
        ]);

        $powerDistributionWork->update($request->all());

        // Хэрэв checkbox чагталсан бол Telegram руу илгээх
        if ($request->has('send_telegram')) {
            $this->sendTelegramMessage($powerDistributionWork);
        }

        return redirect()->route('power-distribution-works.index')
            ->with('success', 'Захиалгат ажил амжилттай шинэчлэгдлээ.');
    }

    public function destroy(PowerDistributionWork $powerDistributionWork)
    {
        $powerDistributionWork->delete();
        return redirect()->route('power-distribution-works.index')
            ->with('success', 'Захиалгат ажил устгагдлаа.');
    }

    private function sendTelegramMessage($work)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chat_id = env('TELEGRAM_CHAT_ID');

        // MarkdownV2-д зориулж тусгай тэмдэгтүүдийг escape хийх функц
        $escape = fn($text) => str_replace(
            ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'],
            ['\_', '\*', '\[', '\]', '\(', '\)', '\~', '\`', '\>', '\#', '\+', '\-', '\=', '\|', '\{', '\}', '\.', '\!'],
            $text
        );

        $message = "⚡️ *Хийгдсэн захиалгат ажлууд:*\n\n"
            . "*🏢 ТЗЭ:* " . $escape($work->tze) . "\n"
            . "*🛠 Засварын ажлын утга:* " . $escape($work->repair_work) . "\n"
            . "*🛠 Тайлбар:* " . $escape($work->description ?: '—') . "\n"
            . "*📅 Огноо:* " . $escape($work->date) . "\n";

        try {
            Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chat_id,
                'text' => $message,
                'parse_mode' => 'MarkdownV2',
            ]);
        } catch (\Exception $e) {
            Log::error('Telegram message failed: ' . $e->getMessage());
        }
    }
}
