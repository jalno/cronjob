<?php

namespace packages\cronjob;

trait ViewTrait
{
    protected $shortdescription;

    public function setShortDescription($description)
    {
        $this->shortdescription = $description;
    }

    public function getShortDescription()
    {
        return $this->shortdescription;
    }
}

class View extends \packages\base\View
{
    use ViewTrait;
}
