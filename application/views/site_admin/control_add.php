<?= form_open(current_url(), array('class' => 'form-horizontal')) ?>
	<legend>
		<a href="<?= base_url('admin/hubs/manage') ?>" class="small pull-right">
			&laquo; <?= $this->lang->line('back_hub_mgmt') ?>
		</a>

		<?= $this->lang->line('add_control') ?>
	</legend>

	<ul class="toolbox">
		<li class="heading"><?= $this->lang->line('toolbox') ?></li>
		<?php foreach($toolbox_items as $item): ?>
			<li>
				<i class="icon-tool-<?= $item->icon ?>"></i>
				<?= $this->lang->line($item->label) ?>
			</li>
		<?php endforeach ?>
	</ul>

	<div class="control-box">
		<h1><?= $this->lang->line('control') ?></h1>
		<ul class="control-area"></ul>
	</div>
<?= form_close() ?>

<script type="text/javascript">
	$(function() {
		$('.toolbox li').draggable({
			appendTo: 'body',
			helper: 'clone'
		});
		$('.control-area').droppable({
			drop: function(event, ui) {
				$('<li></li>').html(ui.draggable.html()).appendTo(this);
			}
		});
	});
</script>