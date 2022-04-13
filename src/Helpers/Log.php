<?php

namespace Bakgul\FileHistory\Helpers;

use Bakgul\Kernel\Helpers\Folder;
use Bakgul\Kernel\Helpers\Path;
use Bakgul\Kernel\Helpers\Text;
use Bakgul\FileHistory\Services\LogService;

class Log
{
    public static function prefix()
    {
        return 'x_';
    }

    public static function folder(string $action)
    {
        return "{$action}Logs";
    }

    public static function path(string $action, string $type = 'file')
    {
        return Path::realBase(Path::glue(['storage', 'logs', "{$type}Logs", self::folder($action)]));
    }

    public static function files(string $action, string $type = 'file')
    {
        return Folder::content(self::path($action, $type));
    }

    public static function isLogsMissing(string $action, string $type = 'file'): bool
    {
        return empty(self::files($action, $type));
    }

    public static function isNoLogLeft(string $action, string $type = 'file'): bool
    {
        return empty(array_filter(
            self::files($action, $type),
            fn ($x) => !str_contains($x, self::prefix())
        ));
    }

    public static function isPairMissing()
    {
        return !file_exists(
            Text::prepend(self::path('redo')) . str_replace(
                self::prefix(),
                '',
                LogService::getFirstPrefixedFile(self::path('undo'))
            ) . '.json'
        );
    }
}
