<?php
use \packages\base\translator;
use \packages\userpanel;
use \packages\userpanel\date;
use \themes\clipone\utility;
use \packages\cronjob\task;
use \packages\cronjob\task\schedule;
$this->the_header();
?>
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-edit"></i> <?php echo t("cronjob.task.edit"); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body">
				<form class="managements" action="<?php echo userpanel\url("settings/cronjob/tasks/edit/{$this->task->id}"); ?>" method="post">
					<div class="row">
						<div class="col-md-6">
							<?php $this->createField(array(
									'name' => 'name',
									'type' => 'select',
									'label' => t("cronjob.task.name"),
									'options' => $this->getTasksForSelect()
								));
							?>
						</div>
						<div class="col-md-6">
							<?php $this->createField(array(
								'type' => 'select',
								'name' => 'status',
								'label' => t("cronjob.task.status"),
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
									'label' => t("cronjob.task.process")
								));
							?>
						</div>
					</div>
					<div class="row parameters" <?php if(!$this->isCustom())echo('style="display: none;"'); ?>>
						<div class="col-xs-12">
							<?php
							$this->createField(array(
								'name' => 'parameters',
								'data' => [
									'role' => 'tagsinput'
								],
								'label' => t("cronjob.task.parameters")
							));
							?>
						</div>
					</div>
					<div class="row cronjob-time">
						<div class="col-md-6">
							<p> <?php echo t("cronjob.minutes"); ?> </p>
							<div class="row">
								<div class="col-xs-2">
									<?php $this->createField(array(
										'type' => 'checkbox',
										'name' => 'allminutes',
										'inline' => true,
										'options' => array(
											array(
												'value' => 'all',
												'label' => t("cronjob.all"),
												'data' => array(
													'type' => 'minutes'
												)
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
											'options' => array(
												array(
													'value' => $i,
													'label' => $i,
													'data' => array(
														'type' => 'minutes'
													)
												)
											)
										));
									} ?>
								</div>
								
							</div>
						</div>
						<div class="col-md-6">
							<p> <?php echo t("cronjob.hours"); ?> </p>
							<div class="row">
								<div class="col-xs-2">
									<?php $this->createField(array(
										'type' => 'checkbox',
										'name' => 'allhours',
										'inline' => true,
										'options' => array(
											array(
												'value' => 'all',
												'label' => t("cronjob.all"),
												'data' => array(
													'type' => 'hours'
												)
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
											'options' => array(
												array(
													'value' => $i,
													'label' => $i,
													'data' => array(
														'type' => 'hours'
													)
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
							<p> <?php echo t("cronjob.months"); ?> </p>
							<div class="row">
								<div class="col-xs-3 col-sm-2">
									<?php $this->createField(array(
										'type' => 'checkbox',
										'name' => 'allmonths',
										'inline' => true,
										'options' => array(
											array(
												'value' => 'all',
												'label' => t("cronjob.all"),
												'data' => array(
													'type' => 'months'
												)
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
											'options' => array(
												array(
													'value' => $i,
													'label' => date::format("F", date::mktime(0, 0, 0, $i)),
													'data' => array(
														'type' => 'months'
													)
												)
											)
										));
									} ?>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<p> <?php echo t("cronjob.days"); ?> </p>
							<div class="row">
								<div class="col-xs-2">
									<?php $this->createField(array(
										'type' => 'checkbox',
										'name' => 'alldays',
										'inline' => true,
										'options' => array(
											array(
												'value' => 'all',
												'label' => t("cronjob.all"),
												'data' => array(
													'type' => 'days'
												)
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
											'options' => array(
												array(
													'value' => $i,
													'label' => $i,
													'data' => array(
														'type' => 'days'
													)
												)
											)
										));
									} ?>
								</div>
							</div>
						</div>
					</div>
					<p class="text-left">
						<a href="<?php echo userpanel\url('settings/cronjob/tasks'); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-right"></i> <?php echo t('cronjob.return'); ?></a>
						<button type="submit" class="btn btn-teal"><i class="fa fa-edit"></i> <?php echo t("cronjob.edit") ?></button>
					</p>
				</form>
			</div>
		</div>
	</div>
</div>
<?php
$this->the_footer();