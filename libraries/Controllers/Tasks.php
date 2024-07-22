<?php

namespace packages\cronjob\Controllers;

use packages\base\DB;
use packages\base\DB\DuplicateRecord;
use packages\base\DB\Parenthesis;
use packages\base\Http;
use packages\base\InputValidation;
use packages\base\NotFound;
use packages\base\Process;
use packages\base\Views\FormError;
use packages\cronjob\Authorization;
use packages\cronjob\Events\Tasks as TasksEvents;
use packages\cronjob\Task;
use packages\cronjob\Task\Schedule;
use packages\cronjob\View;
use themes\clipone\Views\CronJob as Views;
use packages\userpanel;
use packages\userpanel\Controller;

class Tasks extends Controller
{
    protected $authentication = true;

    public function listview()
    {
        Authorization::haveOrFail('task_list');
        $view = View::byName(Views\Task\ListView::class);
        $inputsRules = [
            'id' => [
                'type' => 'number',
                'optional' => true,
                'empty' => true,
            ],
            'name' => [
                'type' => 'string',
                'optional' => true,
                'empty' => true,
            ],
            'status' => [
                'type' => 'number',
                'optional' => true,
                'empty' => true,
                'values' => [
                    Task::active,
                    Task::deactive,
                ],
            ],
            'word' => [
                'type' => 'string',
                'optional' => true,
                'empty' => true,
            ],
            'comparison' => [
                'values' => ['equals', 'startswith', 'contains'],
                'default' => 'contains',
                'optional' => true,
            ],
        ];
        $this->response->setStatus(true);
        try {
            $inputs = $this->checkinputs($inputsRules);
            foreach (['id', 'name', 'status'] as $item) {
                if (isset($inputs[$item]) and $inputs[$item]) {
                    $comparison = $inputs['comparison'];
                    if (in_array($item, ['id', 'status'])) {
                        $comparison = 'equals';
                    }
                    DB::where("cronjob_tasks.{$item}", $inputs[$item], $comparison);
                }
            }
            if (isset($inputs['word']) and $inputs['word']) {
                $parenthesis = new Parenthesis();
                foreach (['name', 'parameters', 'process'] as $item) {
                    if (!isset($inputs[$item]) or !$inputs[$item]) {
                        $parenthesis->where($item, $inputs['word'], $inputs['comparison'], 'OR');
                    }
                }
                DB::where($parenthesis);
            }
            $view->setDataForm($this->inputsvalue($inputs));
            DB::pageLimit($this->items_per_page);
            $cronjobsTasksData = DB::paginate('cronjob_tasks', $this->page, ['cronjob_tasks.*']);
            $view->setPaginate($this->page, DB::totalCount(), $this->items_per_page);
            $cronjobsTasks = [];
            foreach ($cronjobsTasksData as $cronjob) {
                $cronjobsTasks[] = new Task($cronjob);
            }
            $view->setDataList($cronjobsTasks);
        } catch (InputValidation $error) {
            $view->setFormError(FormError::fromException($error));
            $this->response->setStatus(false);
        }
        $this->response->setView($view);

        return $this->response;
    }

    public function delete($data)
    {
        Authorization::haveOrFail('task_delete');
        $this->response->setStatus(false);
        $task = Task::byId($data['task']);
        if (!$task) {
            throw new NotFound();
        }
        $view = View::byName(Views\Task\Delete::class);
        $view->setTask($task);
        if (HTTP::is_post()) {
            try {
                $task->delete();
                $this->response->setStatus(true);
                $this->response->Go(userpanel\url('settings/cronjob/tasks'));
            } catch (InputValidation $error) {
                $view->setFormError(FormError::fromException($error));
            }
            $view->setDataForm($this->inputsvalue($inputs));
        } else {
            $this->response->setStatus(true);
        }
        $this->response->setView($view);

        return $this->response;
    }

    private function makeMultiDimensionalSchedule($data, $key = 0)
    {
        $b = [];
        $keys = array_keys($data);
        if (is_array($data[$keys[$key]])) {
            foreach ($data[$keys[$key]] as $d) {
                if (array_key_exists($key + 1, $keys)) {
                    $b[$d] = $this->makeMultiDimensionalSchedule($data, $key + 1);
                } else {
                    $b = $data[$keys[$key]];
                }
            }
        } else {
            if (array_key_exists($key + 1, $keys)) {
                $b[$data[$keys[$key]]] = $this->makeMultiDimensionalSchedule($data, $key + 1);
            } else {
                $b = $data[$keys[$key]];
            }
        }

        return $b;
    }

    private function validateParameters($parameters)
    {
        $validatedParameters = [];
        $parts = [];
        $quotation = false;
        $equals = false;
        $len = strlen($parameters);
        for ($x = 0; $x < $len; ++$x) {
            $chr = $parameters[$x];
            if ('"' == $chr) {
                if (0 == $x or '\\' != $parameters[$x - 1]) {
                    if (false == $quotation) {
                        if (0 == $x) {
                            $quotation = true;
                            $parameters = substr($parameters, 1);
                            --$x;
                            $len = strlen($parameters);
                        } else {
                            throw new InputValidation('parameters');
                        }
                    } else {
                        if ($x == $len - 1) {
                            $quotation = false;
                            $parameters = substr($parameters, 0, $len - 1);
                            --$x;
                            $len = strlen($parameters);
                        } else {
                            $nextChr = $parameters[$x + 1];
                            if (',' == $nextChr or '=' == $nextChr) {
                                $quotation = false;
                                $parameters = substr($parameters, 0, $x).substr($parameters, $x + 1);
                                --$x;
                                $len = strlen($parameters);
                            }
                        }
                    }
                } else {
                    $parameters = ($x ? substr($parameters, 0, $x - 1) : '').substr($parameters, $x);
                    --$x;
                    $len = strlen($parameters);
                }
            }
            if ('=' == $chr or ',' == $chr or $x == $len - 1) {
                if (!$quotation) {
                    if (',' == $chr or $x == $len - 1) {
                        if ($equals) {
                            $equals = false;
                        } else {
                            throw new InputValidation('parameters');
                        }
                    } else {
                        $equals = true;
                    }
                    $parts[] = substr($parameters, 0, $x);
                    $parameters = $x != $len - 1 ? substr($parameters, $x + 1) : '';
                    $x = -1;
                    $len = strlen($parameters);
                } elseif ($x == $len - 1) {
                    throw new InputValidation('parameters');
                }
            }
        }
        if (0 != count($parts) % 2) {
            throw new InputValidation('parameters');
        }
        $len = count($parts);
        for ($x = 0; $x < $len; $x += 2) {
            $validatedParameters[$parts[$x]] = $parts[$x + 1];
        }

        return $validatedParameters;
    }

    public function edit($data)
    {
        Authorization::haveOrFail('task_edit');
        $this->response->setStatus(false);
        $task = Task::byId($data['task']);
        if (!$task) {
            throw new NotFound();
        }
        $view = View::byName(Views\Task\Edit::class);
        $view->setTask($task);
        $tasksEvent = new TasksEvents();
        $tasksEvents = $tasksEvent->get();
        $view->setTasks($tasksEvents);
        if (HTTP::is_post()) {
            $inputsRules = [
                'name' => [
                    'type' => 'string',
                    'optional' => true,
                ],
                'process' => [
                    'empty' => true,
                    'optional' => true,
                    'regex' => '/^(?:packages(?:\\\\[A-Za-z0-9_]+){2,}@[A-Za-z0-9_]+$)?/',
                ],
                'parameters' => [
                    'empty' => true,
                    'optional' => true,
                ],
                'status' => [
                    'type' => 'number',
                    'optional' => true,
                    'values' => [
                        Task::active,
                        Task::deactive,
                    ],
                ],
                'minutes' => [
                    'optional' => true,
                ],
                'hours' => [
                    'optional' => true,
                ],
                'days' => [
                    'optional' => true,
                ],
                'months' => [
                    'optional' => true,
                ],
            ];
            try {
                $inputs = $this->checkinputs($inputsRules);
                $found = false;
                if (isset($inputs['name'])) {
                    foreach ($tasksEvents as $taskEvent) {
                        if ($taskEvent->name == $inputs['name']) {
                            $task->name = $taskEvent->name;
                            $task->process = $taskEvent->process;
                            $task->parameters = $taskEvent->parameters;
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        if (isset($inputs['process'])) {
                            list($class, $method) = explode('@', $inputs['process'], 2);
                            if (!class_exists($class) or !method_exists($class, $method) and $class instanceof Process) {
                                throw new InputValidation('process');
                            }
                            $task->process = $inputs['process'];
                        } else {
                            throw new InputValidation('process');
                        }
                        if (isset($inputs['parameters'])) {
                            $task->parameters = $this->validateParameters($inputs['parameters']);
                        }
                    }
                }

                if (isset($inputs['status'])) {
                    $task->status = $inputs['status'];
                }
                $task->save();
                if (!isset($inputs['months'])) {
                    throw new InputValidation('months');
                }
                if (!isset($inputs['days'])) {
                    throw new InputValidation('days');
                }
                if (!isset($inputs['hours'])) {
                    throw new InputValidation('hours');
                }
                if (!isset($inputs['minutes'])) {
                    throw new InputValidation('minutes');
                }
                if (12 == count($inputs['months'])) {
                    $inputs['months'] = null;
                }
                if (31 == count($inputs['days'])) {
                    $inputs['days'] = null;
                }
                if (24 == count($inputs['hours'])) {
                    $inputs['hours'] = null;
                }
                if (60 == count($inputs['minutes'])) {
                    $inputs['minutes'] = null;
                }
                $foundArray = false;
                $keys = ['month', 'day', 'hour', 'minute'];
                $data = [];
                foreach ($keys as $key) {
                    $data[$key] = $inputs[$key.'s'];
                }
                $data = $this->makeMultiDimensionalSchedule($data);
                $scheduleID = [];
                foreach ($data as $month => $days) {
                    foreach ($days as $day => $hours) {
                        foreach ($hours as $hour => $minutes) {
                            if (!$minutes) {
                                $minutes = [null];
                            }
                            foreach ($minutes as $minute) {
                                $schedule = new Schedule();
                                $schedule->task = $task->id;
                                $schedule->month = $month ? $month : null;
                                $schedule->day = $day ? $day : null;
                                $schedule->hour = ('' !== $hour) ? $hour : null;
                                $schedule->minute = $minute;
                                if ($oldSchedule = $task->hasSchedule($schedule)) {
                                    $scheduleID[] = $oldSchedule->id;
                                } else {
                                    $schedule->save();
                                    $scheduleID[] = $schedule->id;
                                }
                            }
                        }
                    }
                }
                $schedules = $task->schedules;
                foreach ($schedules as $schedule) {
                    if (!in_array($schedule->id, $scheduleID)) {
                        $schedule->delete();
                    }
                }
                $this->response->setStatus(true);
            } catch (InputValidation $error) {
                $view->setFormError(FormError::fromException($error));
            } catch (DuplicateRecord $error) {
                $view->setFormError(FormError::fromException($error));
            }
            $view->setDataForm($this->inputsvalue($inputsRules));
        } else {
            $this->response->setStatus(true);
        }
        $this->response->setView($view);

        return $this->response;
    }

    public function create($data)
    {
        Authorization::haveOrFail('task_create');
        $this->response->setStatus(false);
        $view = View::byName(Views\Task\Create::class);
        $tasksEvent = new TasksEvents();
        $tasksEvents = $tasksEvent->get();
        $view->setTasks($tasksEvents);
        if (HTTP::is_post()) {
            $inputsRules = [
                'name' => [
                    'type' => 'string',
                ],
                'process' => [
                    'empty' => true,
                    'optional' => true,
                ],
                'parameters' => [
                    'empty' => true,
                    'optional' => true,
                ],
                'status' => [
                    'type' => 'number',
                    'values' => [
                        Task::active,
                        Task::deactive,
                    ],
                ],
                'minutes' => [
                ],
                'hours' => [
                ],
                'days' => [
                ],
                'months' => [
                ],
            ];
            try {
                $inputs = $this->checkinputs($inputsRules);
                $task = new Task();
                $task->status = $inputs['status'];
                $found = false;
                if (isset($inputs['name'])) {
                    foreach ($tasksEvents as $taskEvent) {
                        if ($taskEvent->name == $inputs['name']) {
                            $task->name = $taskEvent->name;
                            $task->process = $taskEvent->process;
                            $task->parameters = $taskEvent->parameters;
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $task->name = $inputs['name'];
                        if (isset($inputs['process']) and preg_match('/^packages(?:\\\\[A-Za-z0-9_]+){2,}@[A-Za-z0-9_]+$/', $inputs['process'])) {
                            list($class, $method) = explode('@', $inputs['process'], 2);
                            if (!class_exists($class) or !method_exists($class, $method) and $class instanceof Process) {
                                throw new InputValidation('process');
                            }
                            $task->process = $inputs['process'];
                        } else {
                            throw new InputValidation('process');
                        }
                        if (isset($inputs['parameters'])) {
                            $task->parameters = $this->validateParameters($inputs['parameters']);
                        }
                    }
                }
                $task->save();
                if (!isset($inputs['months'])) {
                    throw new InputValidation('months');
                }
                if (!isset($inputs['days'])) {
                    throw new InputValidation('days');
                }
                if (!isset($inputs['hours'])) {
                    throw new InputValidation('hours');
                }
                if (!isset($inputs['minutes'])) {
                    throw new InputValidation('minutes');
                }
                if (12 == count($inputs['months'])) {
                    $inputs['months'] = null;
                }
                if (31 == count($inputs['days'])) {
                    $inputs['days'] = null;
                }
                if (24 == count($inputs['hours'])) {
                    $inputs['hours'] = null;
                }
                if (60 == count($inputs['minutes'])) {
                    $inputs['minutes'] = null;
                }
                $foundArray = false;
                $keys = ['month', 'day', 'hour', 'minute'];
                $data = [];
                foreach ($keys as $key) {
                    $data[$key] = $inputs[$key.'s'];
                }
                $data = $this->makeMultiDimensionalSchedule($data);
                $scheduleID = [];
                foreach ($data as $month => $days) {
                    foreach ($days as $day => $hours) {
                        foreach ($hours as $hour => $minutes) {
                            if (!$minutes) {
                                $minutes = [null];
                            }
                            foreach ($minutes as $minute) {
                                $schedule = new Schedule();
                                $schedule->task = $task->id;
                                $schedule->month = $month ? $month : null;
                                $schedule->day = $day ? $day : null;
                                $schedule->hour = ('' !== $hour) ? $hour : null;
                                $schedule->minute = $minute;
                                $schedule->save();
                            }
                        }
                    }
                }
                $this->response->setStatus(true);
                $this->response->Go(userpanel\url("settings/cronjob/tasks/edit/{$task->id}"));
            } catch (InputValidation $error) {
                $view->setFormError(FormError::fromException($error));
            } catch (DuplicateRecord $error) {
                $view->setFormError(FormError::fromException($error));
            }
            $view->setDataForm($this->inputsvalue($inputsRules));
        } else {
            $this->response->setStatus(true);
        }
        $this->response->setView($view);

        return $this->response;
    }
}
