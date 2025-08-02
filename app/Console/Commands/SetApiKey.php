<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SetApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:set-api-key 
                            {--show : Показать ключ без сохранения}
                            {--length=40 : Длина ключа}
                            {--force : Перезаписать существующий ключ}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Установить статический API ключ в .env файл';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $key = Str::random($this->option('length'));

        if ($this->option('show')) {
            $this->info('Сгенерированный API ключ: ' . $key);
            return;
        }

        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            $this->error('.env файл не найден!');
            return;
        }

        $envContent = File::get($envPath);

        // Если ключ уже существует
        if (str_contains($envContent, 'API_KEY=') && !$this->option('force')) {
            $this->error('API_KEY уже установлен. Используйте --force для перезаписи.');
            return;
        }

        // Обновляем или добавляем ключ
        if (str_contains($envContent, 'API_KEY=')) {
            $envContent = preg_replace(
                '/API_KEY=.*/',
                'API_KEY=' . $key,
                $envContent
            );
        } else {
            $envContent .= PHP_EOL . "API_KEY={$key}" . PHP_EOL;
        }

        File::put($envPath, $envContent);

        $this->info('API ключ успешно установлен в .env файл!');
        $this->line('Ключ: ' . $key);
    }
}
