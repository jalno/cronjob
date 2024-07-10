<?php

namespace packages\cronjob\Task;

use packages\base\DB\DBObject;
use packages\cronjob\Task;

class Schedule extends DBObject
{
    public const saturday = 1;
    public const sunday = 2;
    public const monday = 3;
    public const tuesday = 4;
    public const wednesday = 5;
    public const thursday = 6;
    public const friday = 7;
    protected $dbTable = 'cronjob_schedules';
    protected $primaryKey = 'id';
    protected $dbFields = [
        'task' => ['type' => 'int', 'required' => true],
        'year' => ['type' => 'int'],
        'month' => ['type' => 'int'],
        'day' => ['type' => 'int'],
        'hour' => ['type' => 'int'],
        'minute' => ['type' => 'int'],
    ];
    protected $relations = [
        'task' => ['hasOne', Task::class, 'task'],
    ];
}
