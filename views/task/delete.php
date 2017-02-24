<?php
namespace packages\cronjob\views\task;
use \packages\cronjob\task;
use \packages\cronjob\views\form;
class delete extends form{
	public function setTask(task $task){
		$this->setData($task, "task");
	}
	protected function getTask(){
		return $this->getData("task");
	}
}
