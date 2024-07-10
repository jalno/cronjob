<?php

namespace packages\cronjob\Processes;

use packages\base\Process;
use packages\cronjob\Task;
use packages\cronjob\Task\Run;

class CronJob extends Process
{
    public function runTasks()
    {
        $task = new Task();
        $tasks = $task->getScheduled();
        foreach ($tasks as $task) {
            $this->runTask($task);
        }
    }

    private function runTask(Task $task)
    {
        $process = new Process();
        $process->name = $task->process;
        $process->paramters = $task->parameters;
        $process->save();
        if ($process->background_run()) {
            $run = new Run();
            $run->task = $task->id;
            $run->process = $process->id;
            $run->save();

            return true;
        }

        return false;
    }
}
