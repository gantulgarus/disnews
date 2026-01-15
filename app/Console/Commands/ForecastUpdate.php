<?php
// ==============================================================
// app/Console/Commands/ForecastUpdate.php
// ==============================================================

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ForecastUpdate extends Command
{
    /**
     * Командын нэр (php artisan forecast:update гэж дуудна)
     */
    protected $signature = 'forecast:update';

    /**
     * Тайлбар
     */
    protected $description = 'Python forecast скриптийг ажиллуулж, датабэйс шинэчлэх';

    /**
     * Командыг ажиллуулах
     */
    public function handle()
    {
        Log::info('ForecastUpdate command cron-аас дуудагдлаа');

        $this->info('═══════════════════════════════════════════════════════');
        $this->info('🔄 Forecast системийг шинэчилж байна...');
        $this->info('═══════════════════════════════════════════════════════');

        // Python скриптийн зам
        $pythonPath = '/home/ndc-user/system_total/venv/bin/python';
        $scriptPath = '/home/ndc-user/system_total/main.py';

        // Эсвэл:
        // $scriptPath = '/full/path/to/your/main.py';

        // Файл байгаа эсэхийг шалгах
        if (!file_exists($scriptPath)) {
            $this->error("❌ Python скрипт олдсонгүй: {$scriptPath}");
            $this->error("   Зөв зам оруулна уу!");
            return 1;
        }

        $this->line("📂 Скрипт: {$scriptPath}");
        $this->line("🐍 Python: {$pythonPath}");
        $this->newLine();

        try {
            // Python скрипт ажиллуулах
            $process = new Process([$pythonPath, $scriptPath]);
            $process->setTimeout(600); // 10 минут timeout
            $process->setWorkingDirectory(dirname($scriptPath));

            $this->info("⏳ Python скрипт ажиллуулж байна... (энэ нь хэдэн минут үргэлжилж болно)");
            $this->newLine();

            // Ажиллуулах (output харуулах)
            $process->run(function ($type, $buffer) {
                if (Process::ERR === $type) {
                    $this->error($buffer);
                } else {
                    $this->line($buffer);
                }
            });

            // Амжилттай эсэхийг шалгах
            if ($process->isSuccessful()) {
                $this->newLine();
                $this->info('═══════════════════════════════════════════════════════');
                $this->info('✅ Forecast амжилттай шинэчлэгдлээ!');
                $this->info('═══════════════════════════════════════════════════════');
                Log::info('ForecastUpdate амжилттай шинэчлэгдлээ');

                // Статистик харуулах
                $this->showStats();

                return 0;
            } else {
                throw new ProcessFailedException($process);
            }
        } catch (ProcessFailedException $exception) {
            $this->error('═══════════════════════════════════════════════════════');
            $this->error('❌ Python скрипт алдаа гарлаа!');
            $this->error('═══════════════════════════════════════════════════════');
            $this->error($exception->getMessage());
            Log::error('Python скрипт алдаа гарлаа');
            return 1;
        } catch (\Exception $e) {
            $this->error('═══════════════════════════════════════════════════════');
            $this->error('❌ Алдаа гарлаа!');
            $this->error('═══════════════════════════════════════════════════════');
            $this->error($e->getMessage());
            return 1;
        }
    }

    /**
     * Database статистик харуулах
     */
    private function showStats()
    {
        try {
            // ForecastData model ашиглах
            $total = \App\Models\ForecastData::count();
            $today = \App\Models\ForecastData::whereDate('time', today())->count();
            $latest = \App\Models\ForecastData::latest('created_at')->first();

            $this->newLine();
            $this->table(
                ['Мэдээлэл', 'Утга'],
                [
                    ['Нийт бичлэг', number_format($total)],
                    ['Өнөөдрийн дата', number_format($today)],
                    ['Сүүлийн шинэчлэл', $latest ? $latest->created_at->format('Y-m-d H:i:s') : 'Байхгүй'],
                ]
            );
        } catch (\Exception $e) {
            $this->warn("⚠️ Статистик авахад алдаа: " . $e->getMessage());
        }
    }
}
