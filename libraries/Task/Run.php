<?php

namespace packages\cronjob\Task;

use packages\base\DB\DBObject;
use packages\base\Process;
use packages\cronjob\Task;

class Run extends DBObject
{
    protected $dbTable = 'cronjob_runs';
    protected $primaryKey = 'id';
    protected $dbFields = [
        'task' => ['type' => 'int', 'required' => true],
        'process' => ['type' => 'int', 'required' => true],
    ];
    protected $relations = [
        'task' => ['hasOne', Task::class, 'task'],
        'process' => ['hasOne', Process::class, 'process'],
    ];
}
