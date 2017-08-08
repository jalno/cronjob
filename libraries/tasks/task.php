<?php
namespace packages\cronjob;
use \packages\base\db;
use \packages\base\date;
use \packages\base\db\parenthesis;
use \packages\base\db\dbObject;
use \packages\cronjob\task\schedule;
class task extends dbObject{
	const active = 1;
	const deactive = 2;
	protected $dbTable = "cronjob_tasks";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'name' => array('type' => 'text','unique' => true, 'required' => true),
		'process' => array('type' => 'text', 'required' => true),
        'parameters' => array('type' => 'text'),
		'status' => array('type' => 'int', 'required' => true),
    );
	protected $relations = array(
		'schedules' => array('hasMany', 'packages\\cronjob\\task\\schedule', 'task')
	);
	protected $serializeFields = array('parameters');
	protected function getScheduled(){
		$min = date::format("i");
		$starthismin = date::mktime(null, $min, 0);
		$year = new parenthesis();
		$year->where("cronjob_schedules.year", null,'is');
		$year->where("cronjob_schedules.year", date::format("Y"),'=','or');

		$month = new parenthesis();
		$month->where("cronjob_schedules.month", null,'is');
		$month->where("cronjob_schedules.month", date::format("m"),'=','or');

		$day = new parenthesis();
		$day->where("cronjob_schedules.day", null,'is');
		$day->where("cronjob_schedules.day", date::format("d"),'=','or');

		$hour = new parenthesis();
		$hour->where("cronjob_schedules.hour", null,'is');
		$hour->where("cronjob_schedules.hour", date::format("H"),'=','or');

		$minute = new parenthesis();
		$minute->where("cronjob_schedules.minute", null,'is');
		$minute->where("cronjob_schedules.minute", $min,'=','or');

		db::join("cronjob_schedules", "cronjob_schedules.task=cronjob_tasks.id", "INNER");
		db::where($year);
		db::where($month);
		db::where($day);
		db::where($hour);
		db::where($minute);
		db::where("cronjob_tasks.status", self::active);
		db::setQueryOption('DISTINCT');
		$datas = db::get("cronjob_tasks", null, array("cronjob_tasks.*"));
		foreach($datas as $key => $data){
			db::join("base_processes", "cronjob_runs.process=base_processes.id", "INNER");
			db::where("cronjob_runs.task",$data['id']);
			db::where("start", $starthismin, '>=');
			if(db::has('cronjob_runs')){
				unset($datas[$key]);
			}
		}
		$tasks = array();
		foreach($datas as $data){
			$tasks[] = new static($data);
		}
		return $tasks;
	}
	public function hasSchedule(schedule $newSchedule){
		foreach($this->schedules as $schedule){
			if(
				($newSchedule->month === $schedule->month) and
				($newSchedule->day === $schedule->day) and
				($newSchedule->hour === $schedule->hour) and
				($newSchedule->minute === $schedule->minute)
			){
				return $schedule;
			}
		}
		return false;
	}
}
