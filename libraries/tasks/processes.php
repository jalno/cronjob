<?php
namespace packages\cronjob;
use \packages\base\events;
use \packages\cronjob\events\cronjob_processes_list;
class processes{
	static protected $processes = array();
	static public function add($process){
		if(!in_array($process, self::$processes)){
			self::$processes[] = $process;
		}
	}
	static public function get(){
		events::trigger(new cronjob_processes_list());
		return self::$processes;
	}
	static public function has($process){
		return in_array($process, self::$processes);
	}
}
