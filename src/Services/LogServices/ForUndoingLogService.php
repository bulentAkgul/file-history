<?php

namespace Bakgul\FileHistory\Services\LogServices;

use Bakgul\FileHistory\Helpers\Log;
use Bakgul\FileHistory\Services\LogService;

class ForUndoingLogService extends LogService
{
    private static $filePath;
    private static $isCreated;
    private static $isDir;

    public static function set(string $filePath, bool $isDir, bool $isCreated)
    {
        self::$filePath = $filePath;
        self::$isCreated = $isCreated;
        self::$isDir = $isDir;

        self::prepare();
        self::setlog();
    }

    private static function prepare()
    {
        parent::$path = Log::path('undo');

        parent::$file = parent::file();

        parent::$content = parent::getLogs();
    }

    private static function setlog()
    {
        self::append();

        parent::write();
    }

    private static function append()
    {
        if (self::isLogged()) return;

        parent::$content[] = [
            'path' => self::$filePath,
            'delete' => self::$isCreated,
            'isDir' => self::$isDir, 
            'content' => self::getContent()
        ];
    }

    private static function isLogged(): bool
    {
        return !empty(array_filter(
            parent::$content,
            fn ($x) => $x['path'] == self::$filePath
        ));
    }

    private static function getContent(): array
    {
        return self::$isCreated || self::$isDir ? [] : file(self::$filePath);
    }
}
