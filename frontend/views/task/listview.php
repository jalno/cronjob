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
use \packages\base\view\error;
use \packages\cronjob\task;
use \packages\cronjob\views\task\listview as taskListView;
class listview extends taskListView{
	use viewTrait, listTrait, formTrait;
	protected $btnAdd;
	function __beforeLoad(){
		$this->setTitle(array(
			t("cronjob"),
			t("list"),
			t("cronjob.tasks")
		));
		navigation::active("settings/cronjob");
		$this->addBodyClass('cronjob-task');
		$this->setButtons();
		$this->btnAdd = parent::$canAdd;
		if(empty($this->getDataList())){
			$this->addNotFoundError();
		}
	}
	private function addNotFoundError(){
		$error = new error();
		$error->setType(error::NOTICE);
		$error->setCode('cronjob.task.notfound');
		if($this->btnAdd){
			$error->setData([
				[
					'type' => 'btn-success',
					'txt' => t('cronjob.task.create'),
					'link' => userpanel\url('settings/cronjob/tasks/create')
				]
			], 'btns');
		}
		$this->addError($error);
	}
	public static function onSourceLoad(){
		parent::onSourceLoad();
		if(parent::$navigation){
			if($settings = navigation::getByName("settings")){
				$cronjob = new menuItem("cronjob");
				$cronjob->setTitle(t("cronjob"));
				$cronjob->setIcon("fa fa-undo");
				$cronjob->setURL(userpanel\url('settings/cronjob/tasks'));
				$settings->addItem($cronjob);
			}
		}
	}

	public function setButtons(){
		$this->setButton('task_edit', $this->canEdit, array(
			'title' => t('cronjob.edit'),
			'icon' => 'fa fa-edit',
			'classes' => array('btn', 'btn-xs', 'btn-teal')
		));
		$this->setButton('task_delete', $this->canDel, array(
			'title' => t('titles.cronjob.delete'),
			'icon' => 'fa fa-times',
			'classes' => array('btn', 'btn-xs', 'btn-bricky')
		));
	}
	protected function getComparisonsForSelect(){
		return array(
			array(
				'title' => t('search.comparison.contains'),
				'value' => 'contains'
			),
			array(
				'title' => t('search.comparison.equals'),
				'value' => 'equals'
			),
			array(
				'title' => t('search.comparison.startswith'),
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
				'title' => t('cronjob.task.status.active'),
				'value' => task::active
			),
			array(
				'title' => t('cronjob.task.status.deactive'),
				'value' => task::deactive
			)
		);
	}
}
