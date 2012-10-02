<?= form_open(current_url()) ?>
	<legend>
		<a href="<?= base_url('admin/controls/manage') ?>" class="small pull-right">
			&laquo; <?= $this->lang->line('back_control_mgmt') ?>
		</a>

		<?= $editor_title ?>
	</legend>

	<div class="form-horizontal control-group">
		<div class="control-label">
			<?= $this->lang->line('control_name') ?>
		</div>

		<div class="controls">
			<?= form_input('control_name', $control_name) ?>
		</div>
	</div>
	<hr />

	<div class="alert alert-info">
		<?= $this->lang->line('control_exp') ?>
	</div>

	<div class="toolbox">
		<h1><?= $this->lang->line('toolbox') ?></h1>

		<div class="toolbox-area">
			<?php foreach($toolbox_items as $key => $item): ?>
				<span class="element">
					<i class="icon-tool-<?= $item->icon ?>"></i>
					<span class="key hide"><?= $key ?></span>
					<?= $this->lang->line($item->label) ?>
				</span>
			<?php endforeach ?>
		</div>
	</div>

	<div class="control-box">
		<h1><?= $this->lang->line('control') ?></h1>
		<div class="control-area"></div>
	</div>

	<div class="form-actions">
		<?= form_hidden('controls', $controls) ?>
		<?= form_submit('submit', $this->lang->line('submit'), 'class="btn btn-primary"') ?>
	</div>
<?= form_close() ?>

<script type="text/javascript">
	$(function() {
		// Add POSTed controls
		var controls = $('[name=controls]').val();

		if (controls != '') {
			controls = controls.split('|');

			$('.toolbox .element').each(function() {
				var key = $(this).children('.key').first().text();

				if (controls.indexOf(key) != -1) {
					var element = $(this).html();
					$('<span class="element dropped"></span>').html(element).appendTo('.control-area');
				}
			});
		}
		
		// Allow double clicking on element
		$('.toolbox .element').dblclick(function() {
			var element = $(this).html();
			$('<span class="element dropped"></span>').html(element).appendTo('.control-area');
			processItems();
		});

		// Drag from toolbar to control box
		$('.toolbox .element').draggable({
			appendTo: 'body',
			helper: 'clone'
		});
		$('.control-area')
			.droppable({
				activeClass: 'control-drop-highlight',
			    accept: ':not(.dropped)',
				drop: function(event, ui) {
					$('<span class="element dropped"></span>').html(ui.draggable.html()).appendTo(this);
					processItems();
				}
			})
			.sortable({
				items: '.element',
				update: processItems
			});

		// Drag back to toolbox from control box
		$('.control-area .element').draggable();
		$('.toolbox-area').droppable({
			accept: '.dropped',
			activeClass: 'control-drop-highlight',
			drop: function(event, ui) {
				$(ui.draggable).remove();
			},
		});
	});

	// Add/remove items to the control
	function processItems() {
		var items = new Array();

		$('.control-area .element').each(function() {
			var key = $(this).children('.key').first().text();
			items.push(key);
		});

		$('[name=controls]').val(items.join('|'));
	}
</script>