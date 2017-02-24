<?php
namespace themes\clipone\views\cronjob\task;
use \packages\userpanel;

use \themes\clipone\breadcrumb;
use \themes\clipone\navigation;
use \themes\clipone\viewTrait;
use \themes\clipone\views\listTrait;
use \themes\clipone\views\formTrait;
use \themes\clipone\navigation\menuItem;

use \packages\base\json;
use \packages\base\translator;
use \packages\base\frontend\theme;
use \packages\base\db\dbObject;
use \packages\base\view\error;
use \packages\base\views\FormError;
use \packages\cronjob\task;
use \packages\cronjob\views\task\create as tasks_create;

class create extends tasks_create{
	use viewTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans("cronjob"),
			translator::trans("cronjob.task.create")
		));
		$this->setNavigation();
		$this->addAssets();
		$this->handlingScheduleError();
	}
	private function setNavigation(){
		navigation::active("settings/cronjob/create_task");
	}
	public function addAssets() {
		$this->addCSSFile(theme::url('assets/plugins/select2/dist/css/select2.min.css'));
		$this->addCSSFile(theme::url('assets/plugins/select2-bootstrap-theme/dist/css/select2-bootstrap.min.css'));
		$this->addJSFile(theme::url('assets/plugins/select2/dist/js/select2.full.min.js'));
		$this->addJSFile(theme::url('assets/plugins/select2/dist/js/i18n/fa.js'));
		$this->addJSFile(theme::url('assets/plugins/jquery-validation/dist/jquery.validate.min.js'));
		$this->addJSFile(theme::url('assets/plugins/bootstrap-inputmsg/bootstrap-inputmsg.min.js'));
		$this->addCSSFile(theme::url('assets/plugins/jQuery-Tags-Input/dist/jquery.tagsinput.min.css'));
		$this->addJSFile(theme::url('assets/plugins/jQuery-Tags-Input/dist/jquery.tagsinput.min.js'));
		$this->addJSFile(theme::url('assets/js/pages/tasks.js'));
		$this->addCSSFile(theme::url('assets/css/pages/tasks.css'));
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
				'title' => translator::trans("cronjob.active"),
				'value' => task::active
			),
			array(
				'title' => translator::trans("cronjob.deactive"),
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
			$title = translator::trans('cronjob.task.name.'.$task->name);
			$options[] = array(
				'value' => $task->name,
				'title' => $title ? $title : $task->name,
				'data' => array(
					'schedules' => json\encode(dbObject::objectToArray($task->name == $this->getDataForm('name') ? $this->getDataForm('schedules') : $task->data['schedules']))
				)
			);
		}
		if(!$found and $formname){
			array_unshift($options,array(
				'value' => $formname,
				'title' => $formname,
				'data' => array(
					'schedules' => json\encode(dbObject::objectToArray($this->getDataForm('schedules')))
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
