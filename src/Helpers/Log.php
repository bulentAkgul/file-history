<?php

namespace Bakgul\FileHistory\Helpers;

use Bakgul\Kernel\Helpers\Folder;
use Bakgul\Kernel\Helpers\Path;
use Bakgul\FileHistory\Services\LogService;
use Bakgul\Kernel\Helpers\Settings;

class Log
{
    public static function prefix()
    {
        return 'x_';
    }

    public static function path(string $action)
    {
        return Path::glue([Settings::logs('path'), $action]);
    }

    public static function files(string $action)
    {
        return Folder::content(self::path($action));
    }

    public static function isLogsMissing(string $action): bool
    {
        return empty(self::files($action));
    }

    public static function isNoLogLeft(string $action): bool
    {
        return empty(array_filter(
            self::files($action),
            fn ($x) => !str_contains($x, self::prefix())
        ));
    }

    public static function isPairMissing()
    {
        return !file_exists(str_replace(
            ['undo', self::prefix()],
            ['redo', ''],
            LogService::getFirstPrefixedFile(self::path('undo'))
        ));
    }
}
