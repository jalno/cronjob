<?php

namespace packages\cronjob\Events;

use packages\base\Event;
use packages\base\Events;
use packages\cronjob\Task;

class Tasks extends Event
{
    private $tasks = [];

    public function addTask(Task $task)
    {
        $this->tasks[$task->name] = $task;
    }

    public function getTaskNames()
    {
        return array_keys($this->tasks);
    }

    public function getByName($name)
    {
        return isset($this->tasks[$name]) ? $this->tasks[$name] : null;
    }

    public function get()
    {
        if (!$this->tasks) {
            $this->trigger();
        }

        return $this->tasks;
    }

    public function trigger()
    {
        Events::trigger($this);
    }
}
