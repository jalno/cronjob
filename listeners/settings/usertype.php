<?php
namespace packages\cronjob\listeners\settings;
use \packages\userpanel\usertype\permissions;
class usertype{
	public function permissions_list(){
		$permissions = array(
			'task_list',
			'task_edit',
			'task_delete',
			'task_create'
		);
		foreach($permissions as $permission){
			permissions::add('cronjob_'.$permission);
		}
	}
}
