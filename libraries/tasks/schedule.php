<?php
namespace packages\cronjob\task;
use \packages\base\db\dbObject;
class schedule extends dbObject{
	const saturday = 1;
	const sunday = 2;
	const monday = 3;
	const tuesday = 4;
	const wednesday = 5;
	const thursday = 6;
	const friday = 7;
	protected $dbTable = "cronjob_schedules";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'task' => array('type' => 'int', 'required' => true),
		'year' => array('type' => 'int'),
		'month' => array('type' => 'int'),
		'day' => array('type' => 'int'),
		'hour' => array('type' => 'int'),
		'minute' => array('type' => 'int')
    );
	protected $relations = array(
		'task' => array('hasOne', 'packages\\cronjob\\task', 'task')
	);
}
