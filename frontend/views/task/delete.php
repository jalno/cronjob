<?php

namespace themes\clipone\views\cronjob\Task;

use packages\cronjob\Views\Task\Delete as TasksDelete;
use themes\clipone\Navigation;
use themes\clipone\ViewTrait;

class Delete extends TasksDelete
{
    use ViewTrait;
    protected $task;

    public function __beforeLoad()
    {
        $this->task = $this->getTask();
        $this->setTitle(t('titles.cronjob.tasks.delete'));
        $this->addBodyClass('cronjob-task');
        $this->setNavigation();
    }

    private function setNavigation()
    {
        Navigation::active('settings/cronjob');
    }
}
