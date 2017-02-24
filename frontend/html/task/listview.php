<?php
use \packages\base\translator;
use \packages\userpanel;
use \themes\clipone\utility;
use \packages\cronjob\task;
$this->the_header();
?>
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-tasks"></i> <?php echo translator::trans("cronjob.tasks"); ?>
				<div class="panel-tools">
					<?php if($this->btnAdd){ ?>
					<a class="btn btn-xs btn-link tooltips" title="<?php echo translator::trans('cronjob.task.create'); ?>" href="<?php echo userpanel\url('settings/cronjob/tasks/create'); ?>"><i class="fa fa-plus"></i></a>
					<?php } ?>
					<a class="btn btn-xs btn-link tooltips" title="<?php echo translator::trans('search'); ?>" href="#search" data-toggle="modal" data-original-title=""><i class="fa fa-search"></i></a>
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-hover">
						<?php
						$hasButtons = $this->hasButtons();
						?>
						<thead>
							<tr>
								<th class="center">#</th>
								<th><?php echo translator::trans('cronjob.task.name'); ?></th>
								<th><?php echo translator::trans('cronjob.task.process'); ?></th>
								<th><?php echo translator::trans('cronjob.task.status'); ?></th>
								<?php if($hasButtons){ ?><th></th><?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($this->getDataList() as $task){
								$this->setButtonParam('task_edit', 'link', userpanel\url("settings/cronjob/tasks/edit/".$task->id));
								$this->setButtonParam('task_delete', 'link', userpanel\url("settings/cronjob/tasks/delete/".$task->id));
								$statusClass = utility::switchcase($task->status, array(
									'label label-success' => task::active,
									'label label-warning' => task::deactive
								));
								$statusTxt = utility::switchcase($task->status, array(
									'cronjob.task.status.active' => task::active,
									'cronjob.task.status.deactive' => task::deactive
								));
							?>
							<tr>
								<td class="center"><?php echo $task->id; ?></td>
								<td><?php echo( translator::trans("cronjob.task.name.{$task->name}") ? translator::trans("cronjob.task.name.{$task->name}") : $task->name);  ?></td>
								<td class="ltr"><?php echo($task->process);  ?></td>
								<td><span class="<?php echo $statusClass; ?>"><?php echo translator::trans($statusTxt); ?></span></td>
								<?php
								if($hasButtons){
									echo("<td class=\"center\">".$this->genButtons()."</td>");
								}
								?>
							</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				</div>
				<?php $this->paginator(); ?>
			</div>
		</div>
	</div>
</div>
<div class="modal fade manage_tasks" id="search" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo translator::trans('monitoring.search'); ?></h4>
	</div>
	<div class="modal-body">
		<form id="TaskSearch" class="form-horizontal" action="<?php echo userpanel\url("settings/cronjob/tasks"); ?>" method="GET">
			<?php
			$this->setHorizontalForm('sm-3','sm-9');
			$feilds = array(
				array(
					'name' => 'id',
					'type' => 'number',
					'label' => translator::trans("cronjob.task.id"),
					'ltr' => true
				),
				array(
					'name' => 'name',
					'label' => translator::trans("cronjob.task.name")
				),
				array(
					'name' => 'word',
					'label' => translator::trans("cronjob.search.word-key")
				),
				array(
					'type' => 'select',
					'label' => translator::trans('cronjob.task.status'),
					'name' => 'status',
					'options' => $this->getStatusForSelect()
				),
				array(
					'type' => 'select',
					'label' => translator::trans('search.comparison'),
					'name' => 'comparison',
					'options' => $this->getComparisonsForSelect()
				)
			);
			foreach($feilds as $input){
				$this->createField($input);
			}
			?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="TaskSearch" class="btn btn-success"><?php echo translator::trans("cronjob.search"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo translator::trans('cronjob.search.cancel'); ?></button>
	</div>
</div>
<?php
$this->the_footer();