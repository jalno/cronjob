<?php
namespace packages\cronjob\listeners;
use \packages\cronjob\views;
use \packages\notice\events\views as event;
use \packages\notice\events\views\view;

class notice{
	public function views(event $event){
		$event->addView(new view(views\task\listview::class));
		$event->addView(new view(views\task\create::class));
		$event->addView(new view(views\task\edit::class));
		$event->addView(new view(views\task\delete::class));
	}
}
