<?php

namespace packages\cronjob;

use packages\userpanel\Authorization as UserPanelAuthorization;

class Authorization extends UserPanelAuthorization
{
    public static function is_accessed($permission, $prefix = 'cronjob')
    {
        return parent::is_accessed($permission, $prefix);
    }

    public static function haveOrFail($permission, $prefix = 'cronjob')
    {
        parent::haveOrFail($permission, $prefix);
    }
}
