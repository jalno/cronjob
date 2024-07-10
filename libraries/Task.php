<?php

namespace packages\cronjob;

use packages\base\Date;
use packages\base\DB;
use packages\base\DB\DBObject;
use packages\base\DB\Parenthesis;
use packages\cronjob\Task\Schedule;

/**
 * @property int        $id
 * @property string     $name
 * @property string     $process
 * @property array|null $parameters
 * @property int        $status
 * @property Schedule[] $schedules
 */
class Task extends DBObject
{
    public const active = 1;
    public const deactive = 2;
    protected $dbTable = 'cronjob_tasks';
    protected $primaryKey = 'id';
    protected $dbFields = [
        'name' => ['type' => 'text', 'unique' => true, 'required' => true],
        'process' => ['type' => 'text', 'required' => true],
        'parameters' => ['type' => 'text'],
        'status' => ['type' => 'int', 'required' => true],
    ];
    protected $relations = [
        'schedules' => ['hasMany', Schedule::class, 'task'],
    ];
    protected $serializeFields = ['parameters'];

    protected function getScheduled()
    {
        $min = Date::format('i');
        $starthismin = Date::mktime(null, $min, 0);
        $year = new Parenthesis();
        $year->where('cronjob_schedules.year', null, 'is');
        $year->where('cronjob_schedules.year', Date::format('Y'), '=', 'or');

        $month = new Parenthesis();
        $month->where('cronjob_schedules.month', null, 'is');
        $month->where('cronjob_schedules.month', Date::format('m'), '=', 'or');

        $day = new Parenthesis();
        $day->where('cronjob_schedules.day', null, 'is');
        $day->where('cronjob_schedules.day', Date::format('d'), '=', 'or');

        $hour = new Parenthesis();
        $hour->where('cronjob_schedules.hour', null, 'is');
        $hour->where('cronjob_schedules.hour', Date::format('H'), '=', 'or');

        $minute = new Parenthesis();
        $minute->where('cronjob_schedules.minute', null, 'is');
        $minute->where('cronjob_schedules.minute', $min, '=', 'or');

        DB::join('cronjob_schedules', 'cronjob_schedules.task=cronjob_tasks.id', 'INNER');
        DB::where($year);
        DB::where($month);
        DB::where($day);
        DB::where($hour);
        DB::where($minute);
        DB::where('cronjob_tasks.status', self::active);
        DB::setQueryOption('DISTINCT');
        $datas = DB::get('cronjob_tasks', null, ['cronjob_tasks.*']);
        foreach ($datas as $key => $data) {
            DB::join('base_processes', 'cronjob_runs.process=base_processes.id', 'INNER');
            DB::where('cronjob_runs.task', $data['id']);
            DB::where('start', $starthismin, '>=');
            if (DB::has('cronjob_runs')) {
                unset($datas[$key]);
            }
        }
        $tasks = [];
        foreach ($datas as $data) {
            $tasks[] = new self($data);
        }

        return $tasks;
    }

    public function hasSchedule(Schedule $newSchedule)
    {
        foreach ($this->schedules as $schedule) {
            if (
                ($newSchedule->month === $schedule->month)
                and ($newSchedule->day === $schedule->day)
                and ($newSchedule->hour === $schedule->hour)
                and ($newSchedule->minute === $schedule->minute)
            ) {
                return $schedule;
            }
        }

        return false;
    }
}
