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
use \packages\cronjob\views\task\listview as taskListView;

class listview extends taskListView{
	use viewTrait,listTrait,formTrait;
	protected $btnAdd;
	function __beforeLoad(){
		$this->setTitle(array(
			translator::trans("cronjob"),
			translator::trans("list"),
			translator::trans("cronjob.tasks")
		));
		navigation::active("settings/cronjob");
		$this->setButtons();
		$this->btnAdd = parent::$canAdd;
	}
	public static function onSourceLoad(){
		parent::onSourceLoad();
		if(parent::$navigation){
			if($settings = navigation::getByName("settings")){
				$cronjob = new menuItem("cronjob");
				$cronjob->setTitle(translator::trans("cronjob"));
				$cronjob->setIcon("fa fa-undo");
				$cronjob->setURL(userpanel\url('settings/cronjob/tasks'));
				$settings->addItem($cronjob);
			}
		}
	}

	public function setButtons(){
		$this->setButton('task_edit', $this->canEdit, array(
			'title' => translator::trans('edit'),
			'icon' => 'fa fa-edit',
			'classes' => array('btn', 'btn-xs', 'btn-warning')
		));
		$this->setButton('task_delete', $this->canDel, array(
			'title' => translator::trans('delete'),
			'icon' => 'fa fa-times',
			'classes' => array('btn', 'btn-xs', 'btn-bricky')
		));
	}
	protected function getComparisonsForSelect(){
		return array(
			array(
				'title' => translator::trans('search.comparison.contains'),
				'value' => 'contains'
			),
			array(
				'title' => translator::trans('search.comparison.equals'),
				'value' => 'equals'
			),
			array(
				'title' => translator::trans('search.comparison.startswith'),
				'value' => 'startswith'
			)
		);
	}
	protected function getStatusForSelect(){
		return array(
			array(
				'title' => '',
				'value' => ''
			),
			array(
				'title' => translator::trans('cronjob.task.status.active'),
				'value' => task::active
			),
			array(
				'title' => translator::trans('cronjob.task.status.deactive'),
				'value' => task::deactive
			)
		);
	}
}
