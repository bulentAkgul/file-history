<?php

namespace Bakgul\FileHistory\Tasks;

class SetLogs
{
    public static function _($action, $logger): array
    {
        return self::combine($action, self::group($logger->getLogs()));
    }

    public static function group(array $logs)
    {
        $groups = ['folders' => [], 'files' => []];

        foreach ($logs as $log) {
            $groups[$log['isDir'] ? 'folders' : 'files'][] = $log;
        }

        return $groups;
    }

    public static function combine(string $action, array $groups)
    {
        $order = self::getOrder($action);

        $groups['folders'] = self::order($action, $groups['folders']);

        return array_merge($groups[$order[0]], $groups[$order[1]]);
    }

    private static function getOrder($action)
    {
        return [
            'undo' => ['files', 'folders'],
            'redo' => ['folders', 'files']
        ][$action];
    }

    private static function order($action, $folders)
    {
        array_multisort($folders);

        return $action == 'undo' ? array_reverse($folders) : $folders;
    }
}