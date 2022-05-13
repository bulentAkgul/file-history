<?php

namespace Bakgul\FileHistory\Commands;

use Bakgul\Kernel\Helpers\Settings;
use Bakgul\FileHistory\Helpers\Log;
use Bakgul\FileHistory\Services\HistoryServices\UndoHistoryService;
use Illuminate\Console\Command;

class UndoCommand extends Command
{
    protected $signature = 'undo-log';
    protected $description = '';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        if (Log::isLogsMissing('undo')) return $this->inform('nothing');
        if (Log::isNoLogLeft('undo')) return $this->inform('nomore');

        $results = UndoHistoryService::run();

        $this->displayResults($results);
    }

    private function inform(string $key)
    {
        $this->warn(Settings::messages("history.{$key}"));
    }

    private function displayResults(array $results)
    {
        foreach ($results as $result) {
            if ($result[1] == 'failed') {
                $this->error($result[0]);
            } else {
                $this->info($result[0]);
            }
        }
    }
}
