<?php
namespace packages\cronjob\views\task;
use \packages\cronjob\task;
use \packages\cronjob\views\form;
class edit extends form{
	public function setTask(task $task){
		$this->setData($task, "task");
		$this->setDataForm($task->toArray());
		if(is_array($task->parameters)){
			$parameters = '';
			foreach($task->parameters as $key=>$val){
				if($parameters)$parameters .= ",";
				$parameters .= $key.'='.$val;
			}
			$this->setDataForm($parameters, "parameters");
		}

		$minutes = array();
		$hours = array();
		$days = array();
		$months = array();
		$this->setDataForm($task->schedules, "schedules");
		foreach($task->schedules as $schedule){
			if($schedule->minute === null){
				$this->setDataForm('all', "allminutes");
			}else{
				$minutes[] = $schedule->minute;
			}
			if($schedule->hour === null){
				$this->setDataForm('all', "allhours");
			}else{
				$hours[] = $schedule->hour;
			}
			if($schedule->day === null){
				$this->setDataForm('all', "alldays");
			}else{
				$days[] = $schedule->day;
			}
			if($schedule->month === null){
				$this->setDataForm('all', "allmonths");
			}else{
				$months[] = $schedule->month;
			}
		}
		if(!empty($minutes)){
			$this->setDataForm($minutes, 'minutes');
		}
		if(!empty($hours)){
			$this->setDataForm($hours, 'hours');
		}
		if(!empty($days)){
			$this->setDataForm($days, 'days');
		}
		if(!empty($months)){
			$this->setDataForm($months, 'months');
		}
	}
	protected function getTask(){
		return $this->getData("task");
	}
	public function setTasks($tasks){
        $this->setData($tasks, "tasks");
    }
    protected function getTasks(){
       return $this->getData("tasks");
    }
}
