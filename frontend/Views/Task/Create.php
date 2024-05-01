<?php

namespace themes\clipone\Views\cronjob\Task;

use packages\base\DB\DBObject;
use packages\base\View\Error;
use packages\cronjob\Task;
use packages\cronjob\Views\Task\Create as TasksCreate;
use themes\clipone\Navigation;
use themes\clipone\Views\FormTrait;
use themes\clipone\ViewTrait;

class Create extends TasksCreate
{
    use ViewTrait;
    use FormTrait;

    public function __beforeLoad()
    {
        $this->setTitle(t('cronjob.task.create'));
        $this->setNavigation();
        $this->handlingScheduleError();
        $this->addBodyClass('cronjob-task');
    }

    private function setNavigation()
    {
        Navigation::active('settings/cronjob');
    }

    private function handlingScheduleError()
    {
        foreach (['months', 'days', 'hours', 'minutes'] as $item) {
            if ($this->getFormErrorsByInput($item)) {
                $error = new Error();
                $error->setCode('schedule.inputvalidation.'.$item);
                $this->addError($error);
                $this->clearInputErrors($item);
                break;
            }
        }
    }

    protected function getStatusForSelect()
    {
        return [
            [
                'title' => t('cronjob.task.status.active'),
                'value' => Task::active,
            ],
            [
                'title' => t('cronjob.task.status.deactive'),
                'value' => Task::deactive,
            ],
        ];
    }

    public function getTasksForSelect()
    {
        $options = [];
        $formname = $this->getDataForm('name');
        $found = false;
        foreach ($this->getTasks() as $task) {
            if ($task->name == $formname) {
                $found = true;
            }
            $title = t('cronjob.task.name.'.$task->name);
            $options[] = [
                'value' => $task->name,
                'title' => $title ? $title : $task->name,
                'data' => [
                    'schedules' => DBObject::objectToArray($task->name == $this->getDataForm('name') ? $this->getDataForm('schedules') : $task->data['schedules']),
                ],
            ];
        }
        if (!$found and $formname) {
            array_unshift($options, [
                'value' => $formname,
                'title' => $formname,
                'data' => [
                    'schedules' => DBObject::objectToArray($this->getDataForm('schedules')),
                    'custom' => true,
                ],
            ]);
        }

        return $options;
    }

    protected function isCustom()
    {
        foreach ($this->getTasks() as $task) {
            if ($task->name == $this->getDataForm('name')) {
                return false;
            }
        }

        return true;
    }
}
