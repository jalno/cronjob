<?php

namespace themes\clipone\Views\CronJob\Task;

use packages\base\View\Error;
use packages\cronjob\Task;
use packages\cronjob\Views\Task\ListView as TaskListView;
use packages\userpanel;
use themes\clipone\Navigation;
use themes\clipone\Navigation\MenuItem;
use themes\clipone\Views\FormTrait;
use themes\clipone\Views\ListTrait;
use themes\clipone\ViewTrait;

class ListView extends TaskListView
{
    use ViewTrait;
    use ListTrait;
    use FormTrait;
    protected $btnAdd;

    public function __beforeLoad()
    {
        $this->setTitle([
            t('cronjob'),
            t('list'),
            t('cronjob.tasks'),
        ]);
        Navigation::active('settings/cronjob');
        $this->addBodyClass('cronjob-task');
        $this->setButtons();
        $this->btnAdd = parent::$canAdd;
        if (empty($this->getDataList())) {
            $this->addNotFoundError();
        }
    }

    private function addNotFoundError()
    {
        $error = new Error();
        $error->setType(Error::NOTICE);
        $error->setCode('cronjob.task.notfound');
        if ($this->btnAdd) {
            $error->setData([
                [
                    'type' => 'btn-success',
                    'txt' => t('cronjob.task.create'),
                    'link' => userpanel\url('settings/cronjob/tasks/create'),
                ],
            ], 'btns');
        }
        $this->addError($error);
    }

    public static function onSourceLoad()
    {
        parent::onSourceLoad();
        if (parent::$navigation) {
            if ($settings = Navigation::getByName('settings')) {
                $cronjob = new MenuItem('cronjob');
                $cronjob->setTitle(t('cronjob'));
                $cronjob->setIcon('fa fa-undo');
                $cronjob->setURL(userpanel\url('settings/cronjob/tasks'));
                $settings->addItem($cronjob);
            }
        }
    }

    public function setButtons()
    {
        $this->setButton('task_edit', $this->canEdit, [
            'title' => t('cronjob.edit'),
            'icon' => 'fa fa-edit',
            'classes' => ['btn', 'btn-xs', 'btn-teal'],
        ]);
        $this->setButton('task_delete', $this->canDel, [
            'title' => t('titles.cronjob.delete'),
            'icon' => 'fa fa-times',
            'classes' => ['btn', 'btn-xs', 'btn-bricky'],
        ]);
    }

    protected function getComparisonsForSelect()
    {
        return [
            [
                'title' => t('search.comparison.contains'),
                'value' => 'contains',
            ],
            [
                'title' => t('search.comparison.equals'),
                'value' => 'equals',
            ],
            [
                'title' => t('search.comparison.startswith'),
                'value' => 'startswith',
            ],
        ];
    }

    protected function getStatusForSelect()
    {
        return [
            [
                'title' => '',
                'value' => '',
            ],
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
}
