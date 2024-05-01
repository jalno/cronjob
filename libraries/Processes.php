<?php

namespace packages\cronjob;

use packages\base\Events;
use packages\cronjob\Events\CronjobProcessesList;

class Processes
{
    protected static $processes = [];

    public static function add($process)
    {
        if (!in_array($process, self::$processes)) {
            self::$processes[] = $process;
        }
    }

    public static function get()
    {
        Events::trigger(new CronjobProcessesList());

        return self::$processes;
    }

    public static function has($process)
    {
        return in_array($process, self::$processes);
    }
}
