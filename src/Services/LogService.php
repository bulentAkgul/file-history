<?php

namespace Bakgul\FileHistory\Services;

use Bakgul\FileHistory\Helpers\Log;
use Bakgul\Kernel\Helpers\Folder;
use Bakgul\Kernel\Helpers\Path;
use Bakgul\FileContent\Helpers\Content;
use Bakgul\Kernel\Helpers\Arry;
use Bakgul\Kernel\Helpers\Settings;

class LogService
{
    protected static $path;
    protected static $file;
    protected static $content;

    public static function getLogs(): array
    {
        return json_decode(file_get_contents(Settings::logs('file')), true);
    }

    public static function getLastUnprefixedFile(string $path): string
    {
        return self::path($path, Arry::get(self::getUnprefixedFiles($path), 'L') ?: '');
    }

    public static function getUnprefixedFiles(string $path): array
    {
        return self::getFiles($path, fn ($x) => !str_contains($x, Log::prefix()));
    }

    public static function getFirstPrefixedFile(string $path): string
    {
        return self::path($path, Arry::get(self::getPrefixedFiles($path), 0) ?: '');
    }

    public static function getPrefixedFiles(string $path): array
    {
        return self::getFiles($path, fn ($x) => str_contains($x, Log::prefix()));
    }

    private static function path($path, $file)
    {
        return $file ? Path::glue([$path, $file]) : '';
    }

    public static function getFiles(string $path, callable $callback = null)
    {
        return array_filter(Folder::content($path), $callback);
    }

    public static function write()
    {
        Content::writeJson(Settings::logs('file'), self::$content);
    }
}
