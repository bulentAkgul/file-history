<?php

namespace Bakgul\FileHistory;

use Bakgul\Kernel\Concerns\HasConfig;
use Illuminate\Support\ServiceProvider;

class FileHistoryServiceProvider extends ServiceProvider
{
    use HasConfig;
    
    public function boot()
    {
        $this->commands([
            \Bakgul\FileHistory\Commands\DeleteLogsCommand::class,
            \Bakgul\FileHistory\Commands\RedoCommand::class,
            \Bakgul\FileHistory\Commands\UndoCommand::class,
        ]);
    }

    public function register()
    {
        $this->registerConfigs(__DIR__ . DIRECTORY_SEPARATOR . '..');
    }
}
