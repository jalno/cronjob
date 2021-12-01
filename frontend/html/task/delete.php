<?php

use packages\userpanel;

$this->the_header();
?>
<div class="row">
	<div class="col-md-12">
		<!-- start: BASIC DELETE NEW -->
		<form action="<?php echo userpanel\url('settings/cronjob/tasks/delete/'.$this->task->id); ?>" method="POST">
			<div class="alert alert-block alert-warning fade in">
				<h4 class="alert-heading"><i class="fa fa-exclamation-triangle"></i> <?php echo t("error.notice.title"); ?>!</h4>
				<p>
					<?php echo t("warnings.cronjob.task.delete", array(
						"id"=> $this->task->id,
						"name"=> ("cronjob.task.name." . $this->task->name)
					)); ?>
				</p>
				<hr>
				<p>
					<a href="<?php echo userpanel\url("settings/cronjob/tasks"); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-right"></i> <?php echo t('cronjob.return'); ?></a>
					<button type="submit" class="btn btn-danger"><i class="fa fa-trash-o tip"></i> <?php echo t("titles.cronjob.delete") ?></button>
				</p>
			</div>
		</form>
		<!-- end: BASIC DELETE NEW  -->
	</div>
</div>
<?php
$this->the_footer();
