<?php
namespace packages\cronjob\views\task;
use \packages\cronjob\views\form;
class create extends form{
    public function setTasks($tasks){
        $this->setData($tasks, "tasks");
    }
    protected function getTasks(){
        return $this->getData("tasks");
    }
}
