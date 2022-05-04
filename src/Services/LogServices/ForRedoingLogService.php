<?php

namespace Bakgul\FileHistory\Services\LogServices;

use Bakgul\FileHistory\Functions\SetFile;
use Bakgul\FileHistory\Services\LogService;
use Bakgul\Kernel\Helpers\Settings;

class ForRedoingLogService extends LogService
{
    private static $undo;

    public static function set()
    {
        self::getUndo();

        SetFile::_(['undo', 'redo']);

        self::setLog();

        SetFile::_(['redo', 'undo']);
    }

    private static function getUndo()
    {
        self::$undo = [
            'file' => $f = Settings::logs('file'),
            'logs' => parent::getLogs($f)
        ];
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
