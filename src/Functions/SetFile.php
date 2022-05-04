<?php

namespace Bakgul\FileHistory\Functions;

use Bakgul\Kernel\Helpers\Settings;

class SetFile
{
    public static function _($map, $file = '')
    {
        Settings::set('logs.file', str_replace($map[0], $map[1], $file ?: Settings::logs('file')));
    }
}