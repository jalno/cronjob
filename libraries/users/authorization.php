<?php
namespace packages\cronjob;
use \packages\userpanel\authorization as UserPanelAuthorization;

class authorization extends UserPanelAuthorization{
	static function is_accessed($permission, $prefix = 'cronjob'){
		return parent::is_accessed($permission, $prefix);
	}
	static function haveOrFail($permission, $prefix = 'cronjob'){
		parent::haveOrFail($permission, $prefix);
	}
}
