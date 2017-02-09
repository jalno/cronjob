<?php
namespace packages\cronjob\task;
use \packages\base\db\dbObject;
class schedule extends dbObject{
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
