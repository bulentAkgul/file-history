<?php

namespace Bakgul\FileHistory\Services;

use Bakgul\Kernel\Helpers\Text;
use Bakgul\Kernel\Tasks\CompleteFolders;
use Bakgul\FileHistory\Helpers\Log;
use Bakgul\Kernel\Helpers\Settings;

class FileHistoryService
{
    protected static $logService;
    protected static $undoPath;
    protected static $prefix;
    protected static $action;
    protected static $steps = [];

    public static function prepare(string $action)
    {
        self::$logService = new LogService;
        self::$undoPath = Log::path('undo');
        self::$prefix = Log::prefix();
        self::$action = $action;
    }

    protected static function isFileMissing(array $map): bool
    {
        return !file_exists(str_replace($map[0], $map[1], Settings::logs('file')));
    }

    protected static function isFileExist(): bool
    {
        return file_exists(Settings::logs('file'));
    }

    protected static function retrieve(array $log, string $action): void
    {
        self::completeFolders($log, $action);

        self::prepareFile($log);

        self::fillFile($log);
    }

    private static function completeFolders(array $log, string $action)
    {
        if ($action != 'redo') return;

        $folders = CompleteFolders::_(
            $log['isDir'] ? $log['path'] : Text::dropTail($log['path'])
        );

        self::appendSteps($folders, 'A folder created:');
    }

    protected static function appendSteps(array $entries, string $message, string $status = 'succeed')
    {
        array_map(fn ($x) => self::appendStep($x, $message, $status), $entries);
    }

    protected static function appendStep(string $entry, string $message, string $status = 'succeed')
    {
        self::$steps[] = ["{$message} {$entry}", $status];
    }

    private static function prepareFile(array $log)
    {
        if ($log['isDir']) return;

        if (!file_exists($log['path'])) {
            self::appendStep($log['path'], 'A file created:  ');
        }

        file_put_contents($log['path'], '');
    }

    public static function fillFile(array $log)
    {
        if ($log['isDir']) return;

        foreach ($log['content'] as $line) {
            file_put_contents($log['path'], $line, FILE_APPEND);
        }

        self::appendStep($log['path'], "A file updated:  ");
    }

    protected static function rename(bool $isUndo)
    {
        rename(
            self::file($isUndo ? '' : self::$prefix),
            self::file($isUndo ? self::$prefix : '')
        );
    }

    protected static function file(string $prefix)
    {
        return Text::changeTail(Settings::logs('file'), $prefix . Text::getTail(Settings::logs('file')));
    }

    protected static function result(): array
    {
        return self::$steps;
    }
}
