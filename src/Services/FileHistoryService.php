<?php

namespace Bakgul\FileHistory\Services;

use Bakgul\Kernel\Helpers\Text;
use Bakgul\Kernel\Tasks\CompleteFolders;
use Bakgul\FileHistory\Helpers\Log;

class FileHistoryService
{
    protected static $logService;
    protected static $redoPath;
    protected static $undoPath;
    protected static $prefix;
    protected static $file;
    protected static $action;
    protected static $steps = [];

    public static function prepare(string $action)
    {
        self::setLogService();
        self::setPaths();
        self::getPrefix();
        self::$action = $action;
    }

    private static function setLogService()
    {
        self::$logService = new LogService;
    }

    private static function setPaths()
    {
        self::$undoPath = Log::path('undo');
        self::$redoPath = Log::path('redo');
    }

    private static function getPrefix()
    {
        self::$prefix = Log::prefix();
    }

    protected static function setFileName(string $name)
    {
        self::$file = (int) str_replace(self::$prefix, '', $name);
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
            self::path('undoPath', $isUndo ? '' : self::$prefix) . ".json",
            self::path('undoPath', $isUndo ? self::$prefix : '') . ".json"
        );
    }

    protected static function path(string $path, string $prefix = '')
    {
        return self::$$path . Text::append($prefix . self::$file);
    }

    public static function setLogs(): array
    {
        return self::combine(self::group(self::getLogs()));
    }

    private static function getLogs(): array
    {
        return self::$logService->getLogs(self::path(self::$action . 'Path'));
    }

    public static function group(array $logs)
    {
        $groups = ['folders' => [], 'files' => []];

        foreach ($logs as $log) {
            $groups[$log['isDir'] ? 'folders' : 'files'][] = $log;
        }

        return $groups;
    }

    public static function combine(array $groups)
    {
        $order = self::getOrder();

        $groups['folders'] = self::order($groups['folders']);

        return array_merge($groups[$order[0]], $groups[$order[1]]);
    }

    private static function getOrder()
    {
        return [
            'undo' => ['files', 'folders'],
            'redo' => ['folders', 'files']
        ][self::$action];
    }

    private static function order($folders)
    {
        array_multisort($folders);

        return self::$action == 'undo' ? array_reverse($folders): $folders;
    }
    
    protected static function result(): array
    {
        return self::$steps;
    }
}
