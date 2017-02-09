<?php
namespace packages\cronjob\processes;
use \packages\cronjob\task;
use \packages\cronjob\task\run;
use \packages\base\process;
class cronjob extends process{
	public function runTasks(){
		$task = new task();
		$tasks = $task->getScheduled();
		foreach($tasks as $task){
			$this->runTask($task);
		}
	}
	private function runTask(task $task){
		$process = new process();
		$process->name = $task->process;
		$process->paramters = $task->parameters;
		$process->save();
		if($process->background_run()){
			$run = new run();
			$run->task = $task->id;
			$run->process = $process->id;
			$run->save();
			return true;
		}
		return false;
	}
}
