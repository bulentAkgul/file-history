<?php

namespace Bakgul\FileHistory\Services\HistoryServices;

use Bakgul\FileHistory\Services\FileHistoryService;
use Bakgul\FileHistory\Services\LogServices\ForRedoingLogService;
use Bakgul\Kernel\Helpers\Folder;
use Bakgul\Kernel\Helpers\Path;

class UndoHistoryService extends FileHistoryService
{
    public static function run(): array
    {
        parent::prepare('undo');

        self::getFileName();
        
        if (!self::$file) return parent::result();
        
        self::generateRedo();

        self::undo();

        parent::rename(true);

        return parent::result();
    }

    private static function getFileName()
    {
        parent::setFileName(parent::$logService->getLastUnprefixedFile(self::$undoPath));
    }

    private static function generateRedo()
    {
        if (!file_exists(Path::glue([parent::$redoPath, self::$file . ".json"]))) {
            ForRedoingLogService::set(self::$file);
        }
    }

    private static function undo()
    {
        array_map(
            fn ($x) => $x['delete'] ? self::delete($x['path']) : parent::retrieve($x, 'undo'),
            parent::setLogs()
        );
    }

    private static function delete(string $path)
    {
        if (!file_exists($path)) {
            return parent::appendStep($path, 'Unable to delete a missing item:', 'failed');
        }
        
        if (is_file($path)) {
            parent::appendStep($path, 'A file deleted:  ');
            return unlink($path);
        }

        if (empty(Folder::content($path))) {
            parent::appendStep($path, 'A folder deleted:');
            return rmdir($path);
        }

        return parent::appendStep($path, 'Unable to delete an unempty folder:', 'failed');
    }
}