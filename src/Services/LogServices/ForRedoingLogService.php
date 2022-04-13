<?php

namespace Bakgul\FileHistory\Services\LogServices;

use Bakgul\Kernel\Helpers\Path;
use Bakgul\FileHistory\Helpers\Log;
use Bakgul\FileHistory\Services\LogService;

class ForRedoingLogService extends LogService
{
    private static $undo;

    public static function set(string $file = '')
    {
        self::getUndo($file);

        self::prepare();
        
        self::setLog();
    }

    private static function getUndo(string $file)
    {
        $path = Log::path('undo');
        $name = $file ?: parent::getFirstPrefixedFile($path);

        self::$undo = [
            'name' => $name,
            'logs' => parent::getLogs(Path::glue([$path, $name]))
        ];
    }

    private static function prepare()
    {
        parent::$path = Log::path('redo');

        parent::$file = self::$undo['name'] ?: parent::file();
    }

    private static function setLog()
    {
        self::append();

        parent::write();
    }

    private static function append()
    {
        parent::$content = array_map(fn ($x) => self::setRedo($x), self::$undo['logs']);
    }

    private static function setRedo(array $undo): array
    {
        return [
            'path' => $undo['path'],
            'create' => $undo['delete'],
            'isDir' => $undo['isDir'],
            'content' => $undo['isDir'] ? [] : file($undo['path'])
        ];
    }
}
