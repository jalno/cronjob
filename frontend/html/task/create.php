<?php
use \packages\base\translator;
use \packages\userpanel;
use \packages\userpanel\date;
use \themes\clipone\utility;
use \packages\monitoring\task;
$this->the_header();
?>
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-plus"></i> <?php echo translator::trans("monitoring.task.create"); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body">
				<form class="create_form" action="<?php echo userpanel\url("settings/cronjob/tasks/create"); ?>" method="post">
					<div class="row">
						<div class="col-md-6">
							<?php $this->createField(array(
									'name' => 'name',
									'type' => 'select',
									'label' => translator::trans("cronjob.task.name"),
									'options' => $this->getTasksForSelect()
								));
							?>
						</div>
						<div class="col-md-6">
							<?php $this->createField(array(
								'type' => 'select',
								'name' => 'status',
								'label' => translator::trans("cronjob.task.status"),
								'options' => $this->getStatusForSelect()
							));
							?>
						</div>
					</div>
					<div class="row process" <?php if(!$this->isCustom())echo('style="display: none;"'); ?>>
						<div class="col-xs-12">
							<?php $this->createField(array(
									'name' => 'process',
									'ltr' => true,
									'label' => translator::trans("cronjob.task.process")
								));
							?>
						</div>
					</div>
					<div class="row parameters" <?php if(!$this->isCustom())echo('style="display: none;"'); ?>>
						<div class="col-xs-12">
							<?php
							$this->createField(array(
								'label' => translator::trans("cronjob.task.parameters"),
								'name' => 'parameters',
								'class' => 'tags'
							));
							?>
						</div>
					</div>
					<div class="row cronjob-time">
						<div class="col-md-6">
							<p> <?php echo translator::trans("cronjob.minutes"); ?> </p>
							<div class="row">
								<div class="col-xs-2">
									<?php $this->createField(array(
										'type' => 'checkbox',
										'name' => 'allminutes',
										'inline' => true,
										'data' => array(
											'type' => 'minutes'
										),
										'options' => array(
											array(
												'value' => 'all',
												'label' => translator::trans("cronjob.all")
											)
										)
									));
									?>
								</div>
								<div class="col-xs-10">
									<?php for($i=0;$i != 60;$i++){ 
										$this->createField(array(
											'type' => 'checkbox',
											'name' => 'minutes[]',
											'inline' => true,
											'data' => array(
												'type' => 'minutes'
											),
											'options' => array(
												array(
													'value' => $i,
													'label' => $i
												)
											)
										));
									} ?>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<p> <?php echo translator::trans("cronjob.hours"); ?> </p>
							<div class="row">
								<div class="col-xs-2">
									<?php $this->createField(array(
										'type' => 'checkbox',
										'name' => 'allhours',
										'inline' => true,
										'data' => array(
											'type' => 'hours'
										),
										'options' => array(
											array(
												'value' => 'all',
												'label' => translator::trans("cronjob.all")
											)
										)
									));
									?>
								</div>
								<div class="col-xs-10">
									<?php for($i=0;$i != 24;$i++){ 
										$this->createField(array(
											'type' => 'checkbox',
											'name' => 'hours[]',
											'inline' => true,
											'data' => array(
												'type' => 'hours'
											),
											'options' => array(
												array(
													'value' => $i,
													'label' => $i
												)
											)
										));
									} ?>
								</div>
							</div>
						</div>
					</div>
					<div class="row cronjob-time">
						<div class="col-md-6 months">
							<p> <?php echo translator::trans("cronjob.months"); ?> </p>
							<div class="row">
								<div class="col-xs-3 col-sm-2">
									<?php $this->createField(array(
										'type' => 'checkbox',
										'name' => 'allmonths',
										'inline' => true,
										'data' => array(
											'type' => 'months'
										),
										'options' => array(
											array(
												'value' => 'all',
												'label' => translator::trans("cronjob.all")
											)
										)
									));
									?>
								</div>
								<div class="col-xs-9 col-sm-10">
									<?php for($i=1;$i != 13;$i++){ 
										$this->createField(array(
											'type' => 'checkbox',
											'name' => 'months[]',
											'inline' => true,
											'data' => array(
												'type' => 'months'
											),
											'options' => array(
												array(
													'value' => $i,
													'label' => date::format("F", date::mktime(0, 0, 0, $i))
												)
											)
										));
									} ?>
								</div>
								
							</div>
						</div>
						<div class="col-md-6">
							<p> <?php echo translator::trans("cronjob.days"); ?> </p>
							<div class="row">
								<div class="col-xs-2">
									<?php $this->createField(array(
										'type' => 'checkbox',
										'name' => 'alldays',
										'inline' => true,
										'data' => array(
											'type' => 'days'
										),
										'options' => array(
											array(
												'value' => 'all',
												'label' => translator::trans("cronjob.all")
											)
										)
									));
									?>
								</div>
								<div class="col-xs-10">
									<?php 
									for($i=1;$i <= 31;$i++){ 
										$this->createField(array(
											'type' => 'checkbox',
											'name' => 'days[]',
											'inline' => true,
											'data' => array(
												'type' => 'days'
											),
											'options' => array(
												array(
													'value' => $i,
													'label' => $i
												)
											)
										));
									} ?>
								</div>
								
							</div>
						</div>
					</div>
					<p class="text-left">
						<a href="<?php echo userpanel\url('cronjob/tasks'); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-right"></i> <?php echo translator::trans('monitoring.return'); ?></a>
						<button type="submit" class="btn btn-success"><i class="fa fa-plus"></i> <?php echo translator::trans("monitoring.create") ?></button>
					</p>
				</form>
			</div>
		</div>
	</div>
</div>
<?php
$this->the_footer();