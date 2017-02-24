<?php
namespace packages\cronjob\controllers;
use \packages\base;
use \packages\base\db;
use \packages\base\http;
use \packages\base\NotFound;
use \packages\base\translator;
use \packages\base\view\error;
use \packages\base\db\parenthesis;
use \packages\base\inputValidation;
use \packages\base\views\FormError;
use \packages\base\db\duplicateRecord;
use \packages\base\process;

use \packages\userpanel;
use \packages\userpanel\controller;

use \packages\cronjob\task;
use \packages\cronjob\task\schedule;
use \packages\cronjob\view;
use \packages\cronjob\events\tasks as tasksEvents;
use \packages\cronjob\authorization;
use \packages\cronjob\authentication;

class tasks extends controller{
	protected $authentication = true;
	public function listview(){
		authorization::haveOrFail('task_list');
		$view = view::byName("\\packages\\cronjob\\views\\task\\listview");
		$inputsRules = array(
			'id' => array(
				'type' => 'number',
				'optional' => true,
				'empty' => true
			),
			'name' => array(
				'type' => 'string',
				'optional' =>true,
				'empty' => true
			),
			'status' => array(
				'type' => 'number',
				'optional' =>true,
				'empty' => true,
				'values' => array(
					task::active,
					task::deactive
				)
			),
			'word' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'comparison' => array(
				'values' => array('equals', 'startswith', 'contains'),
				'default' => 'contains',
				'optional' => true
			)
		);
		$this->response->setStatus(true);
		try{
			$inputs = $this->checkinputs($inputsRules);
			foreach(array('id', 'name', 'status') as $item){
				if(isset($inputs[$item]) and $inputs[$item]){
					$comparison = $inputs['comparison'];
					if(in_array($item, array('id', 'status'))){
						$comparison = 'equals';
					}
					db::where("cronjob_tasks.{$item}", $inputs[$item], $comparison);
				}
			}
			if(isset($inputs['word']) and $inputs['word']){
				$parenthesis = new parenthesis();
				foreach(array('name', 'parameters', 'process') as $item){
					if(!isset($inputs[$item]) or !$inputs[$item]){
						$parenthesis->where($item,$inputs['word'], $inputs['comparison'], 'OR');
					}
				}
				db::where($parenthesis);
			}
			$view->setDataForm($this->inputsvalue($inputs));
			db::pageLimit($this->items_per_page);
			$cronjobsTasksData = db::paginate("cronjob_tasks", $this->page, array("cronjob_tasks.*"));
			$view->setPaginate($this->page, db::totalCount(), $this->items_per_page);
			$cronjobsTasks = array();
			foreach($cronjobsTasksData as $cronjob){
				$cronjobsTasks[] = new task($cronjob);
			}
			$view->setDataList($cronjobsTasks);

		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
			$this->response->setStatus(false);
		}
		$this->response->setView($view);
		return $this->response;
	}
	public function delete($data){
		authorization::haveOrFail('task_delete');
		$this->response->setStatus(false);
		$task = task::byId($data["task"]);
		if(!$task){
			throw new NotFound();
		}
		$view = view::byName("\\packages\\cronjob\\views\\task\\delete");
		$view->setTask($task);
		if(http::is_post()){
			try{
				$task->delete();
				$this->response->setStatus(true);
				$this->response->Go(userpanel\url("settings/cronjob/tasks"));
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}
			$view->setDataForm($this->inputsvalue($inputs));
		}else{
			$this->response->setStatus(true);
		}
		$this->response->setView($view);
		return $this->response;
	}
	private function makeMultiDimensionalSchedule($data, $key = 0){
		$b = array();
		$keys = array_keys($data);
		if(is_array($data[$keys[$key]])){
			foreach($data[$keys[$key]] as $d){
				if(array_key_exists($key+1, $keys)){
					$b[$d] = $this->makeMultiDimensionalSchedule($data, $key+1);
				}else{
					$b = $data[$keys[$key]];
				}
			}
		}else{
			if(array_key_exists($key+1, $keys)){
				$b[$data[$keys[$key]]] = $this->makeMultiDimensionalSchedule($data, $key+1);
			}else{
				$b = $data[$keys[$key]];
			}
		}
		return $b;
	}
	private function validateParameters($parameters){
		$validatedParameters = array();
		$parts = array();
		$quotation = false;
		$equals = false;
		$len = strlen($parameters);
		for($x = 0;$x < $len;$x++){
			$chr = $parameters[$x];
			if($chr == "\""){
				if($x == 0 or $parameters[$x-1] != "\\"){
					if($quotation == false){
						if($x == 0){
							$quotation = true;
							$parameters = substr($parameters, 1);
							$x--;
							$len = strlen($parameters);
						}else{
							throw new inputValidation("parameters");
						}
					}else{
						if($x == $len - 1){
							$quotation = false;
							$parameters = substr($parameters, 0, $len-1);
							$x--;
							$len = strlen($parameters);
						}else{
							$nextChr = $parameters[$x+1];
							if($nextChr == "," or $nextChr == "="){
								$quotation = false;
								$parameters = substr($parameters, 0, $x).substr($parameters, $x+1);
								$x--;
								$len = strlen($parameters);
							}
						}
					}
				}else{
					$parameters = ($x ? substr($parameters, 0, $x-1) : '').substr($parameters,$x);
					$x--;
					$len = strlen($parameters);
				}
			}
			if($chr == "=" or $chr == "," or $x == $len-1){
				if(!$quotation){
					if($chr == "," or $x == $len-1){
						if($equals){
							$equals = false;
						}else{
							throw new inputValidation("parameters");
						}
					}else{
						$equals = true;
					}
					$parts[] = substr($parameters, 0, $x);
					$parameters = $x != $len -1 ? substr($parameters, $x+1) : '';
					$x = -1;
					$len = strlen($parameters);
				}elseif($x == $len-1){
					throw new inputValidation("parameters");
				}
			}
		}
		if(count($parts) % 2 != 0){
			throw new inputValidation("parameters");
		}
		$len = count($parts);
		for($x = 0;$x < $len;$x+=2){
			$validatedParameters[$parts[$x]] = $parts[$x+1];
		}
		return $validatedParameters;
	}
	public function edit($data){
		authorization::haveOrFail('task_edit');
		$this->response->setStatus(false);
		$task = task::byId($data["task"]);
		if(!$task){
			throw new NotFound();
		}
		$view = view::byName("\\packages\\cronjob\\views\\task\\edit");
		$view->setTask($task);
		$tasksEvent = new tasksEvents();
		$tasksEvents = $tasksEvent->get();
		$view->setTasks($tasksEvents);
		if(http::is_post()){
			$inputsRules = array(
				'name' => array(
					'type' => 'string',
					'optional' => true
				),
				'process' => array(
					'empty' => true,
					'optional' => true,
					'regex' => "/^(?:packages(?:\\\\[A-Za-z0-9_]+){2,}@[A-Za-z0-9_]+$)?/"
				),
				'parameters' => array(
					'empty' => true,
					'optional' => true
				),
				'status' => array(
					'type' => 'number',
					'optional' => true,
					'values' => array(
						task::active,
						task::deactive
					)
				),
				'minutes' => array(
					'optional' => true
				),
				'hours' => array(
					'optional' => true
				),
				'days' => array(
					'optional' => true
				),
				'months' => array(
					'optional' => true
				)
			);
			try{
				$inputs = $this->checkinputs($inputsRules);
				$found = false;
				if(isset($inputs['name'])){
					foreach($tasksEvents as $taskEvent){
						if($taskEvent->name == $inputs['name']){
							$task->name = $taskEvent->name;
							$task->process = $taskEvent->process;
							$task->parameters = $taskEvent->parameters;
							$found = true;
							break;
						}
					}
					if(!$found){
						if(isset($inputs['process'])){
							list($class, $method) = explode("@", $inputs['process'], 2);
							if(!class_exists($class) or !method_exists($class, $method) and $class instanceof process){
								throw new inputValidation("process");
							}
							$task->process = $inputs['process'];
						}else{
							throw new inputValidation("process");
						}
						if(isset($inputs['parameters'])){
							$task->parameters = $this->validateParameters($inputs['parameters']);
						}
						if(isset($inputs['status'])){
							$task->status = $inputs['status'];
						}
					}
				}
				$task->save();
				if(!isset($inputs['months'])){
					throw new inputValidation("months");
				}
				if(!isset($inputs['days'])){
					throw new inputValidation("days");
				}
				if(!isset($inputs['hours'])){
					throw new inputValidation("hours");
				}
				if(!isset($inputs['minutes'])){
					throw new inputValidation("minutes");
				}
				if(count($inputs['months']) == 12){
					$inputs['months'] = null;
				}
				if(count($inputs['days']) == 31){
					$inputs['days'] = null;
				}
				if(count($inputs['hours']) == 24){
					$inputs['hours'] = null;
				}
				if(count($inputs['minutes']) == 60){
					$inputs['minutes'] = null;
				}
				$foundArray = false;
				$keys = array('month', 'day', 'hour', 'minute');
				$data = array();
				foreach($keys as $key){
					$data[$key] = $inputs[$key.'s'];
				}
				$data = $this->makeMultiDimensionalSchedule($data);
				$scheduleID = array();
				foreach($data as $month => $days){
					foreach($days as $day => $hours){
						foreach($hours as $hour => $minutes){
							if(!$minutes){
								$minutes = array(null);
							}
							foreach($minutes as $minute){
								$schedule = new schedule();
								$schedule->task = $task->id;
								$schedule->month = $month ? $month : null;
								$schedule->day = $day ? $day : null;
								$schedule->hour = ($hour !== '') ? $hour : null;
								$schedule->minute = $minute;
								if($oldSchedule = $task->hasSchedule($schedule)){
									$scheduleID[] = $oldSchedule->id;
								}else{
									$schedule->save();
									$scheduleID[] = $schedule->id;
								}
							}
						}
					}
				}
				$schedules = $task->schedules;
				foreach($schedules as $schedule){
					if(!in_array($schedule->id, $scheduleID)){
						$schedule->delete();
					}
				}
				$this->response->setStatus(true);
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}catch(duplicateRecord $error){
				$view->setFormError(FormError::fromException($error));
			}
			$view->setDataForm($this->inputsvalue($inputsRules));
		}else{
			$this->response->setStatus(true);
		}
		$this->response->setView($view);
		return $this->response;
	}
	public function create($data){
		authorization::haveOrFail('task_create');
		$this->response->setStatus(false);
		$view = view::byName("\\packages\\cronjob\\views\\task\\create");
		$tasksEvent = new tasksEvents();
		$tasksEvents = $tasksEvent->get();
		$view->setTasks($tasksEvents);
		if(http::is_post()){
			$inputsRules = array(
				'name' => array(
					'type' => 'string'
				),
				'process' => array(
					'empty' => true,
					'optional' => true
				),
				'parameters' => array(
					'empty' => true,
					'optional' => true
				),
				'status' => array(
					'type' => 'number',
					'values' => array(
						task::active,
						task::deactive
					)
				),
				'minutes' => array(
				),
				'hours' => array(
				),
				'days' => array(
				),
				'months' => array(
				)
			);
			try{
				$inputs = $this->checkinputs($inputsRules);
				$task = new task();
				$task->status = $inputs['status'];
				$found = false;
				if(isset($inputs['name'])){
					foreach($tasksEvents as $taskEvent){
						if($taskEvent->name == $inputs['name']){
							$task->name = $taskEvent->name;
							$task->process = $taskEvent->process;
							$task->parameters = $taskEvent->parameters;
							$found = true;
							break;
						}
					}
					if(!$found){
						$task->name = $inputs['name'];
						if(isset($inputs['process']) and preg_match("/^packages(?:\\\\[A-Za-z0-9_]+){2,}@[A-Za-z0-9_]+$/", $inputs['process'])){
							list($class, $method) = explode("@", $inputs['process'], 2);
							if(!class_exists($class) or !method_exists($class, $method) and $class instanceof process){
								throw new inputValidation("process");
							}
							$task->process = $inputs['process'];
						}else{
							throw new inputValidation("process");
						}
						if(isset($inputs['parameters'])){
							$task->parameters = $this->validateParameters($inputs['parameters']);
						}
					}
				}
				$task->save();
				if(!isset($inputs['months'])){
					throw new inputValidation("months");
				}
				if(!isset($inputs['days'])){
					throw new inputValidation("days");
				}
				if(!isset($inputs['hours'])){
					throw new inputValidation("hours");
				}
				if(!isset($inputs['minutes'])){
					throw new inputValidation("minutes");
				}
				if(count($inputs['months']) == 12){
					$inputs['months'] = null;
				}
				if(count($inputs['days']) == 31){
					$inputs['days'] = null;
				}
				if(count($inputs['hours']) == 24){
					$inputs['hours'] = null;
				}
				if(count($inputs['minutes']) == 60){
					$inputs['minutes'] = null;
				}
				$foundArray = false;
				$keys = array('month', 'day', 'hour', 'minute');
				$data = array();
				foreach($keys as $key){
					$data[$key] = $inputs[$key.'s'];
				}
				$data = $this->makeMultiDimensionalSchedule($data);
				$scheduleID = array();
				foreach($data as $month => $days){
					foreach($days as $day => $hours){
						foreach($hours as $hour => $minutes){
							if(!$minutes){
								$minutes = array(null);
							}
							foreach($minutes as $minute){
								$schedule = new schedule();
								$schedule->task = $task->id;
								$schedule->month = $month ? $month : null;
								$schedule->day = $day ? $day : null;
								$schedule->hour = ($hour !== '') ? $hour : null;
								$schedule->minute = $minute;
								$schedule->save();
							}
						}
					}
				}
				$this->response->setStatus(true);
				$this->response->Go(userpanel\url("settings/cronjob/tasks/edit/{$task->id}"));
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}catch(duplicateRecord $error){
				$view->setFormError(FormError::fromException($error));
			}
			$view->setDataForm($this->inputsvalue($inputsRules));
		}else{
			$this->response->setStatus(true);
		}
		$this->response->setView($view);
		return $this->response;
	}
}
