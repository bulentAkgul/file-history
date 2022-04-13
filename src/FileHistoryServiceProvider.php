<?php

namespace Bakgul\FileHistory;

use Illuminate\Support\ServiceProvider;
use Bakgul\Kernel\Helpers\Folder;
use Bakgul\Kernel\Helpers\Path;

class FileHistoryServiceProvider extends ServiceProvider
{
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
        $this->registerConfig();
    }

    private function registerConfig()
    {
        foreach ($this->getConfigFiles() as $key => $file) {
            config()->set("packagify.{$key}", require $file);
        }
    }

    private function getConfigFiles()
    {
        $path = Path::glue([__DIR__, '..', 'config']);

        if (!file_exists($path)) return [];

        $files = [];

        foreach (Folder::content($path) as $file) {
            $files[str_replace('.php', '', $file)] = Path::glue([$path, $file]);
        }

        return $files;
    }
}
