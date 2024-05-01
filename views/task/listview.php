<?php

namespace packages\cronjob\Views\Task;

use packages\base\Views\Traits\Form as FormTrait;
use packages\cronjob\Authorization;
use packages\userpanel\Views\ListView as list_view;

class ListView extends list_view
{
    use FormTrait;
    public static $canAdd;
    protected $canEdit;
    protected $canDel;
    protected static $navigation;

    public function __construct()
    {
        $this->canEdit = Authorization::is_accessed('task_edit');
        $this->canDel = Authorization::is_accessed('task_delete');
    }

    public function getDataList()
    {
        return $this->dataList;
    }

    public static function onSourceLoad()
    {
        self::$navigation = Authorization::is_accessed('task_list');
        self::$canAdd = Authorization::is_accessed('task_create');
    }
}
