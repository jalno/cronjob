<?php
use packages\base\Translator;
use packages\userpanel;
use packages\userpanel\Date;

$this->the_header();
?>
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-plus"></i> <?php echo Translator::trans('monitoring.task.create'); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body">
				<form class="managements" action="<?php echo userpanel\url('settings/cronjob/tasks/create'); ?>" method="post">
					<div class="row">
						<div class="col-md-6">
							<?php $this->createField([
							    'name' => 'name',
							    'type' => 'select',
							    'label' => Translator::trans('cronjob.task.name'),
							    'options' => $this->getTasksForSelect(),
							]);
?>
						</div>
						<div class="col-md-6">
							<?php $this->createField([
							    'type' => 'select',
							    'name' => 'status',
							    'label' => Translator::trans('cronjob.task.status'),
							    'options' => $this->getStatusForSelect(),
							]);
?>
						</div>
					</div>
					<div class="row process" <?php if (!$this->isCustom()) {
					    echo 'style="display: none;"';
					} ?>>
						<div class="col-xs-12">
							<?php $this->createField([
							    'name' => 'process',
							    'ltr' => true,
							    'label' => Translator::trans('cronjob.task.process'),
							]);
?>
						</div>
					</div>
					<div class="row parameters" <?php if (!$this->isCustom()) {
					    echo 'style="display: none;"';
					} ?>>
						<div class="col-xs-12">
							<?php
                            $this->createField([
                                'label' => Translator::trans('cronjob.task.parameters'),
                                'name' => 'parameters',
                                'class' => 'tags',
                            ]);
?>
						</div>
					</div>
					<div class="row cronjob-time">
						<div class="col-md-6">
							<p> <?php echo Translator::trans('cronjob.minutes'); ?> </p>
							<div class="row">
								<div class="col-xs-2">
									<?php $this->createField([
									    'type' => 'checkbox',
									    'name' => 'allminutes',
									    'inline' => true,
									    'options' => [
									        [
									            'value' => 'all',
									            'label' => Translator::trans('cronjob.all'),
									            'data' => [
									                'type' => 'minutes',
									            ],
									        ],
									    ],
									]);
?>
								</div>
								<div class="col-xs-10">
									<?php for ($i = 0; 60 != $i; ++$i) {
									    $this->createField([
									        'type' => 'checkbox',
									        'name' => 'minutes[]',
									        'inline' => true,
									        'options' => [
									            [
									                'value' => $i,
									                'label' => $i,
									                'data' => [
									                    'type' => 'minutes',
									                ],
									            ],
									        ],
									    ]);
									} ?>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<p> <?php echo Translator::trans('cronjob.hours'); ?> </p>
							<div class="row">
								<div class="col-xs-2">
									<?php $this->createField([
									    'type' => 'checkbox',
									    'name' => 'allhours',
									    'inline' => true,
									    'data' => [
									        'type' => 'hours',
									    ],
									    'options' => [
									        [
									            'value' => 'all',
									            'label' => Translator::trans('cronjob.all'),
									            'data' => [
									                'type' => 'hours',
									            ],
									        ],
									    ],
									]);
?>
								</div>
								<div class="col-xs-10">
									<?php for ($i = 0; 24 != $i; ++$i) {
									    $this->createField([
									        'type' => 'checkbox',
									        'name' => 'hours[]',
									        'inline' => true,
									        'options' => [
									            [
									                'value' => $i,
									                'label' => $i,
									                'data' => [
									                    'type' => 'hours',
									                ],
									            ],
									        ],
									    ]);
									} ?>
								</div>
							</div>
						</div>
					</div>
					<div class="row cronjob-time">
						<div class="col-md-6 months">
							<p> <?php echo Translator::trans('cronjob.months'); ?> </p>
							<div class="row">
								<div class="col-xs-3 col-sm-2">
									<?php $this->createField([
									    'type' => 'checkbox',
									    'name' => 'allmonths',
									    'inline' => true,
									    'options' => [
									        [
									            'value' => 'all',
									            'label' => Translator::trans('cronjob.all'),
									            'data' => [
									                'type' => 'months',
									            ],
									        ],
									    ],
									]);
?>
								</div>
								<div class="col-xs-9 col-sm-10">
									<?php for ($i = 1; 13 != $i; ++$i) {
									    $this->createField([
									        'type' => 'checkbox',
									        'name' => 'months[]',
									        'inline' => true,
									        'options' => [
									            [
									                'value' => $i,
									                'label' => Date::format('F', Date::mktime(0, 0, 0, $i)),
									                'data' => [
									                    'type' => 'months',
									                ],
									            ],
									        ],
									    ]);
									} ?>
								</div>
								
							</div>
						</div>
						<div class="col-md-6">
							<p> <?php echo Translator::trans('cronjob.days'); ?> </p>
							<div class="row">
								<div class="col-xs-2">
									<?php $this->createField([
									    'type' => 'checkbox',
									    'name' => 'alldays',
									    'inline' => true,
									    'options' => [
									        [
									            'value' => 'all',
									            'label' => Translator::trans('cronjob.all'),
									            'data' => [
									                'type' => 'days',
									            ],
									        ],
									    ],
									]);
?>
								</div>
								<div class="col-xs-10">
									<?php
for ($i = 1; $i <= 31; ++$i) {
    $this->createField([
        'type' => 'checkbox',
        'name' => 'days[]',
        'inline' => true,
        'options' => [
            [
                'value' => $i,
                'label' => $i,
                'data' => [
                    'type' => 'days',
                ],
            ],
        ],
    ]);
} ?>
								</div>
								
							</div>
						</div>
					</div>
					<p class="text-left">
						<a href="<?php echo userpanel\url('cronjob/tasks'); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-right"></i> <?php echo Translator::trans('monitoring.return'); ?></a>
						<button type="submit" class="btn btn-success"><i class="fa fa-plus"></i> <?php echo Translator::trans('monitoring.create'); ?></button>
					</p>
				</form>
			</div>
		</div>
	</div>
</div>
<?php
$this->the_footer();
