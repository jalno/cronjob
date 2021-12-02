<?php
namespace themes\clipone\views\cronjob\task;
use \packages\userpanel;
use \themes\clipone\navigation;
use \themes\clipone\viewTrait;
use \themes\clipone\views\formTrait;
use \packages\base\db\dbObject;
use \packages\base\view\error;
use \packages\base\views\FormError;
use \packages\cronjob\task;
use \packages\cronjob\views\task\create as tasks_create;
class create extends tasks_create{
	use viewTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(t("cronjob.task.create"));
		$this->setNavigation();
		$this->handlingScheduleError();
		$this->addBodyClass('cronjob-task');
	}
	private function setNavigation(){
		navigation::active("settings/cronjob");
	}
	private function handlingScheduleError(){
		foreach(array("months", "days", "hours", "minutes") as $item){
			if($this->getFormErrorsByInput($item)){
				$error = new error();
				$error->setCode('schedule.inputvalidation.'.$item);
				$this->addError($error);
				$this->clearInputErrors($item);
				break;
			}
		}
	}
	protected function getStatusForSelect(){
		return array(
			array(
				'title' => t('cronjob.task.status.active'),
				'value' => task::active
			),
			array(
				'title' => t('cronjob.task.status.deactive'),
				'value' => task::deactive
			)
		);
	}
	public function getTasksForSelect(){
		$options = array();
		$formname = $this->getDataForm('name');
		$found = false;
		foreach($this->getTasks() as $task){
			if($task->name == $formname){
				$found = true;
			}
			$title = t('cronjob.task.name.'.$task->name);
			$options[] = array(
				'value' => $task->name,
				'title' => $title ? $title : $task->name,
				'data' => array(
					'schedules' => dbObject::objectToArray($task->name == $this->getDataForm('name') ? $this->getDataForm('schedules') : $task->data['schedules'])
				)
			);
		}
		if(!$found and $formname){
			array_unshift($options,array(
				'value' => $formname,
				'title' => $formname,
				'data' => array(
					'schedules' => dbObject::objectToArray($this->getDataForm('schedules')),
					'custom' => true
				)
			));
		}
		return $options;
	}
	protected function isCustom(){
		foreach($this->getTasks() as $task){
			if($task->name == $this->getDataForm('name')){
				return false;
			}
		}
		return true;
	}
}
