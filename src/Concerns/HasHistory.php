<?php

namespace Bakgul\FileHistory\Concerns;

use Bakgul\FileHistory\Helpers\Log;
use Bakgul\Kernel\Helpers\Path;
use Bakgul\Kernel\Helpers\Settings;
use Bakgul\Kernel\Tasks\CompleteFolders;
use Carbon\Carbon;

trait HasHistory
{
    public function logFile()
    {
        $name = (int) Carbon::now()->timestamp . '.json';
        $path = Path::glue([Settings::logs('path'), 'undo']);

        CompleteFolders::_($path, false);

        $file = Path::glue([$path, $name]);

        file_put_contents($file, '{}');

        Settings::set('logs.file', $file);
    }
}
