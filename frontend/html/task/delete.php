<?php
use \packages\base\translator;

use \packages\userpanel;

$this->the_header();
?>
<div class="row">
	<div class="col-md-12">
		<!-- start: BASIC DELETE NEW -->
		<form action="<?php echo userpanel\url('settings/cronjob/tasks/delete/'.$this->task->id); ?>" method="POST">
			<div class="alert alert-block alert-warning fade in">
				<h4 class="alert-heading"><i class="fa fa-exclamation-triangle"></i> <?php echo translator::trans("attention"); ?>!</h4>
				<p>
					<?php echo translator::trans("monitoring.cronjob.task.delete.warning", array(
						"id"=>$this->task->id,
						"name"=>$this->task->name
					)); ?>
				</p>
				<hr>
				<p>
					<a href="<?php echo userpanel\url('settings/cronjob/tasks'); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-right"></i> <?php echo translator::trans('monitoring.return'); ?></a>
					<button type="submit" class="btn btn-danger"><i class="fa fa-trash-o tip"></i> <?php echo translator::trans("monitoring.delete") ?></button>
				</p>
			</div>
		</form>
		<!-- end: BASIC DELETE NEW  -->
	</div>
</div>
<?php
$this->the_footer();
