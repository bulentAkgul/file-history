<?php

namespace Bakgul\FileHistory\Services\HistoryServices;

use Bakgul\FileHistory\Functions\SetFile;
use Bakgul\FileHistory\Services\FileHistoryService;
use Bakgul\FileHistory\Tasks\SetLogs;
use Bakgul\Kernel\Helpers\Settings;

class RedoHistoryService extends FileHistoryService
{
    public static function run(): array
    {
        parent::prepare('redo');

        self::getFile();

        self::redo();

        SetFile::_(['redo', 'undo']);

        parent::rename(false);

        return parent::result();
    }

    private static function getFile()
    {
        SetFile::_(
            [['undo', parent::$prefix], ['redo', '']],
            parent::$logService->getFirstPrefixedFile(parent::$undoPath)
        );
    }

    protected static function swap($map, $string = '')
    {
        return str_replace($map[0], $map[1], $string ?: Settings::logs('file'));
    }

    private static function redo()
    {
        array_map(
            fn ($x) => parent::retrieve($x, 'redo'),
            SetLogs::_(parent::$action, parent::$logService)
        );
    }
}