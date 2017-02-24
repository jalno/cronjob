<?php
namespace packages\cronjob\events;
use \packages\base\events;
use \packages\base\event;
use \packages\cronjob\task;
class tasks extends event{
	private $tasks = array();
	public function addTask(task $task){
		$this->tasks[$task->name] = $task;
	}
	public function getTaskNames(){
		return array_keys($this->tasks);
	}
	public function getByName($name){
		return (isset($this->tasks[$name]) ? $this->tasks[$name] : null);
	}
	public function get(){
		if(!$this->tasks){
			$this->trigger();
		}
		return $this->tasks;
	}
	public function trigger(){
		events::trigger($this);
	}
}
