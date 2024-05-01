<?php

namespace packages\cronjob\Views\Task;

use packages\cronjob\Task;
use packages\cronjob\Views\Form;

class Delete extends Form
{
    public function setTask(Task $task)
    {
        $this->setData($task, 'task');
    }

    protected function getTask()
    {
        return $this->getData('task');
    }
}
