<?php

namespace Bakgul\FileHistory\Commands;

use Illuminate\Console\Command;
use Bakgul\Kernel\Helpers\Folder;
use Bakgul\Kernel\Helpers\Path;
use Bakgul\Kernel\Helpers\Settings;

class DeleteLogsCommand extends Command
{
    protected $signature = 'delete-logs';
    protected $description = '';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $path = Settings::logs('path');

        foreach (Folder::content($path) as $folder) {
            foreach (Folder::content(Path::glue([$path, $folder])) as $file) {
                unlink(Path::glue([$path, $folder, $file]));
                $this->info("{$folder}/{$file} has been deleted.");
            }
        }
    }
}
