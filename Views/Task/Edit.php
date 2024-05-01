<?php

namespace packages\cronjob\Views\Task;

use packages\cronjob\Task;
use packages\cronjob\Views\Form;

class Edit extends Form
{
    public function setTask(Task $task)
    {
        $this->setData($task, 'task');
        $this->setDataForm($task->toArray());
        if (is_array($task->parameters)) {
            $parameters = '';
            foreach ($task->parameters as $key => $val) {
                if ($parameters) {
                    $parameters .= ',';
                }
                $parameters .= $key.'='.$val;
            }
            $this->setDataForm($parameters, 'parameters');
        }

        $minutes = [];
        $hours = [];
        $days = [];
        $months = [];
        $this->setDataForm($task->schedules, 'schedules');
        foreach ($task->schedules as $schedule) {
            if (null === $schedule->minute) {
                $this->setDataForm('all', 'allminutes');
            } else {
                $minutes[] = $schedule->minute;
            }
            if (null === $schedule->hour) {
                $this->setDataForm('all', 'allhours');
            } else {
                $hours[] = $schedule->hour;
            }
            if (null === $schedule->day) {
                $this->setDataForm('all', 'alldays');
            } else {
                $days[] = $schedule->day;
            }
            if (null === $schedule->month) {
                $this->setDataForm('all', 'allmonths');
            } else {
                $months[] = $schedule->month;
            }
        }
        if (!empty($minutes)) {
            $this->setDataForm($minutes, 'minutes');
        }
        if (!empty($hours)) {
            $this->setDataForm($hours, 'hours');
        }
        if (!empty($days)) {
            $this->setDataForm($days, 'days');
        }
        if (!empty($months)) {
            $this->setDataForm($months, 'months');
        }
    }

    protected function getTask()
    {
        return $this->getData('task');
    }

    public function setTasks($tasks)
    {
        $this->setData($tasks, 'tasks');
    }

    protected function getTasks()
    {
        return $this->getData('tasks');
    }
}
