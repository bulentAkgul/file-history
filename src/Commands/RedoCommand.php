<?php

namespace Bakgul\FileHistory\Commands;

use Bakgul\Kernel\Helpers\Settings;
use Bakgul\FileHistory\Helpers\Log;
use Bakgul\FileHistory\Services\HistoryServices\RedoHistoryService;
use Illuminate\Console\Command;

class RedoCommand extends Command
{
    protected $signature = 'redo:file';
    protected $description = '';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        if (Log::isLogsMissing('redo')) return $this->inform('nothing');
        if (Log::isPairMissing()) return $this->inform('pairless');
        if (Log::isNoLogLeft('redo')) return $this->inform('nomore');

        $results = RedoHistoryService::run();

        $this->displayResults($results);
    }

    private function inform(string $key)
    {
        $this->warn(Settings::messages("command.{$key}"));
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
