<?php

namespace Bakgul\FileHistory\Services\HistoryServices;

use Bakgul\FileHistory\Services\FileHistoryService;

class RedoHistoryService extends FileHistoryService
{
    public static function run(): array
    {
        parent::prepare('redo');

        self::getFileName();

        self::redo();

        parent::rename(false);

        return parent::result();
    }

    private static function getFileName()
    {
        parent::setFileName(parent::$logService->getFirstPrefixedFile(parent::$undoPath));
    }

    private static function redo()
    {
        array_map(fn ($x) => parent::retrieve($x, 'redo'), parent::setLogs());
    }
}