<?php
namespace packages\cronjob\task;
use \packages\base\db\dbObject;
class run extends dbObject{
	protected $dbTable = "cronjob_runs";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'task' => array('type' => 'int', 'required' => true),
		'process' => array('type' => 'int', 'required' => true)
    );
	protected $relations = array(
		'task' => array('hasOne', 'packages\\cronjob\\task', 'task'),
		'process' => array('hasOne', 'packages\\base\\process', 'process')
	);
}
