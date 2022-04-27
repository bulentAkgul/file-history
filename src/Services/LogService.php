<?php

namespace Bakgul\FileHistory\Services;

use Bakgul\FileHistory\Helpers\Log;
use Bakgul\Kernel\Helpers\Folder;
use Bakgul\Kernel\Helpers\Path;
use Bakgul\FileContent\Helpers\Content;
use Bakgul\Kernel\Tasks\CompleteFolders;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class LogService
{
    protected static $path;
    protected static $file;
    protected static $content;

    const SECONDS_TO_CREATE_NEW_LOG_FILE = 5;

    public static function file(): string
    {
        return self::handleFile([
            'current' => (int) Carbon::now()->timestamp,
            'previous' => self::$file ?: self::getLastUnprefixedFile()
        ]);
    }

    private static function handleFile(array $names): int
    {
        if (self::isNewFileNotRequired($names)) return (int) $names['previous'];

        CompleteFolders::_(self::$path, false);

        self::makeFile($names['current']);
        
        return (int) $names['current'];
    }

    private static function isNewFileNotRequired($names): bool
    {
        return $names['current'] - $names['previous'] <= self::SECONDS_TO_CREATE_NEW_LOG_FILE;
    }

    private static function makeFile($name): void
    {
        file_put_contents(Path::glue([self::$path, "{$name}.json"]), '{}');
    }

    public static function getLogs(string $file = ''): array
    {
        return json_decode(file_get_contents(self::setFilePath($file)), true);
    }

    public static function setFilePath(string $file = ''): string
    {
        return ($file ?: Path::glue([self::$path, self::$file])) . '.json';
    }
    
    public static function clean()
    {
        [$undo, $redo] = self::getPaths();

        foreach (self::getPrefixedFiles($undo) as $file) {
            self::delete(Path::glue([$undo, $file]));

            self::delete(Path::glue([$redo, str_replace(Log::prefix(), '', $file)]));
        }
    }

    private static function delete(string $file)
    {
        if (file_exists($file)) unlink($file);
    }

    public static function getLastUnprefixedFile(string $path = ''): int
    {
        return (int) self::purifyName(
            Arr::last(self::getUnprefixedFiles($path ?: self::$path)) ?: ''
        );
    }

    public static function getFirstPrefixedFile(string $path = ''): string|int
    {
        return self::purifyName(
            Arr::first(self::getPrefixedFiles($path ?: self::$path)) ?: ''
        );
    }

    public static function getPrefixedFiles(string $path): array
    {
        return self::getFiles($path, fn ($x) => str_contains($x, Log::prefix()));
    }

    public static function getUnprefixedFiles(string $path): array
    {
        return self::getFiles($path, fn ($x) => !str_contains($x, Log::prefix()));
    }

    public static function getFiles(string $path, callable $callback = null)
    {
        return array_filter(Folder::content($path), $callback);
    }

    public static function purifyName(string $name)
    {
        return str_replace('.json', '', $name);
    }

    private static function getPaths()
    {
        return array_map(fn ($x) => Log::path($x), ['undo', 'redo']);
    }

    public static function write()
    {
        Content::writeJson(self::setFilePath(), self::$content);
    }
}
