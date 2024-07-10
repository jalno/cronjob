<?php

namespace packages\cronjob\Listeners\Settings;

use packages\userpanel\UserType\Permissions;

class UserType
{
    public function permissions_list()
    {
        $permissions = [
            'task_list',
            'task_edit',
            'task_delete',
            'task_create',
        ];
        foreach ($permissions as $permission) {
            Permissions::add('cronjob_'.$permission);
        }
    }
}
