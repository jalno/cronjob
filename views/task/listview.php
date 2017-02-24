<?php
namespace packages\cronjob\views\task;
use \packages\userpanel\views\listview as list_view;
use \packages\cronjob\authorization;
use \packages\base\views\traits\form as formTrait;
class listview extends list_view{
	use formTrait;
	static public $canAdd;
	protected $canEdit;
	protected $canDel;
	static protected $navigation;
	function __construct(){
		$this->canEdit = authorization::is_accessed('task_edit');
		$this->canDel = authorization::is_accessed('task_delete');
	}
	public function getDataList(){
		return $this->dataList;
	}
	public static function onSourceLoad(){
		self::$navigation = authorization::is_accessed('task_list');
		self::$canAdd = authorization::is_accessed('task_create');
	}
}
