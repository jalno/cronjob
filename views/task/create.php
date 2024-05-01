<?php

namespace packages\cronjob\Views\Task;

use packages\cronjob\Views\Form;

class Create extends Form
{
    public function setTasks($tasks)
    {
        $this->setData($tasks, 'tasks');
    }

    protected function getTasks()
    {
        return $this->getData('tasks');
    }
}
