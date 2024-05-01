<?php

namespace packages\cronjob\Listeners;

use packages\cronjob\Views;
use packages\notice\Events\Views as Event;
use packages\notice\Events\Views\View;

class Notice
{
    public function views(Event $event)
    {
        $event->addView(new View(Views\Task\ListView::class));
        $event->addView(new View(Views\Task\Create::class));
        $event->addView(new View(Views\Task\Edit::class));
        $event->addView(new View(Views\Task\Delete::class));
    }
}
