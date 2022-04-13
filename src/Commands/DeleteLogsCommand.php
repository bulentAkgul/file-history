<?php

namespace Bakgul\FileHistory\Commands;

use Illuminate\Console\Command;
use Bakgul\Kernel\Helpers\Folder;
use Bakgul\Kernel\Helpers\Path;

class DeleteLogsCommand extends Command
{
    protected $signature = 'delete:logs {name}';
    protected $description = '';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $path = storage_path(Path::glue(['logs', "{$this->argument('name')}Logs"]));

        foreach (Folder::content($path) as $folder) {
            foreach (Folder::content(Path::glue([$path, $folder])) as $file) {
                unlink(Path::glue([$path, $folder, $file]));
                $this->info("{$folder}/{$file} has been deleted.");
            }
        }
    }
}
