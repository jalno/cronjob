<?php
namespace themes\clipone\views\cronjob\task;
use \packages\userpanel;

use \themes\clipone\breadcrumb;
use \themes\clipone\navigation;
use \themes\clipone\viewTrait;
use \themes\clipone\views\listTrait;
use \themes\clipone\views\formTrait;
use \themes\clipone\navigation\menuItem;

use \packages\base\translator;
use \packages\base\frontend\theme;
use \packages\cronjob\task;
use \packages\cronjob\views\task\delete as tasks_delete;

class delete extends tasks_delete{
	use viewTrait;
	protected $task;
	function __beforeLoad(){
		$this->task = $this->getTask();
		$this->setTitle(array(
			translator::trans("cronjob"),
			translator::trans("cronjob.task.delete")." ".$this->task->name
		));
		$this->setNavigation();
	}
	private function setNavigation(){
		navigation::active("settings/cronjob/tasks");
	}
}
