<?= form_open(current_url()) ?>
	<legend>
		<a href="<?= base_url('admin/widgets/manage') ?>" class="small pull-right">
			&laquo; <?= $this->lang->line('back_widget_mgmt') ?>
		</a>

		<?= $editor_title ?>
	</legend>

	<div class="form-horizontal">
		<div class="control-group">
			<label class="control-label">
				<?= $this->lang->line('widget_name') ?>
			</label>

			<div class="controls">
				<?= form_input('widget_name', $widget_name) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">
				<?= $this->lang->line('widget_width') ?>
			</label>

			<div class="controls">
				<?= form_dropdown('widget_width', $widget_widths, $widget_width) ?>
				<div class="help-block"><?= $this->lang->line('widget_width_exp') ?></div>
			</div>
		</div>
	</div>
	<hr />

	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#control-config" data-toggle="tab"><?= $this->lang->line('control_config') ?></a>
			</li>

			<li>
				<a href="#hub-config" data-toggle="tab"><?= $this->lang->line('hub_config') ?></a>
			</li>
		</ul>

		<div class="tab-content">
			<div id="control-config" class="tab-pane active fade in">
				<div class="alert alert-info">
					<?= $this->lang->line('widget_exp') ?>
				</div>

				<div class="toolbox">
					<h1><?= $this->lang->line('toolbox') ?></h1>

					<div class="toolbox-area">
						<?php foreach($toolbox_items as $key => $item): ?>
							<span class="control">
								<i class="icon-tool-<?= $item->icon ?>"></i>
								<?= $this->lang->line($item->label) ?>

								<a href="#" title="<?= $this->lang->line('control_configure') ?>" class="control-configure">
									<i class="icon-wrench"></i>
								</a>

								<?= form_hidden('toolbox_keys[]', $key) ?>
							</span>
						<?php endforeach ?>
					</div>
				</div>

				<div class="widget-box">
					<h1><?= $this->lang->line('widget') ?></h1>
					<div class="widget-area"></div>
					<div class="widget-autoloaded">
						<?php foreach($widget_items as $item): ?>
							<span class="control dropped">
								<i class="icon-tool-<?= $item->icon ?>"></i>
								<?= $this->lang->line($item->label) ?>

								<a href="#" title="<?= $this->lang->line('control_configure') ?>" class="control-configure">
									<i class="icon-wrench"></i>
								</a>

								<?= form_hidden('control_keys[]', $item->key) ?>
								<?= form_hidden('control_classes[]', $item->classes) ?>
								<?= form_hidden('control_disp_paths[]', $item->disp_paths) ?>
								<?= form_hidden('control_value_paths[]', $item->value_paths) ?>
								<?= form_hidden('control_formats[]', $item->formats) ?>
							</span>
						<?php endforeach ?>
					</div>
				</div>
			</div>

			<div id="hub-config" class="tab-pane fade">
				<div class="form-horizontal">
					<div class="control-group">
						<label class="control-label">
							<?= $this->lang->line('attached_hub') ?>
						</label>

						<div class="controls">
							<?= form_dropdown('attached_hub', $hubs_list, $attached_hub) ?>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">
							<?= $this->lang->line('data_filters') ?>
						</label>

						<div class="controls">
							<?= form_textarea(array('name' => 'data_filters', 'rows' => '4'), $data_filters) ?>
							<div class="help-block"><?= $this->lang->line('data_filters_exp') ?></div>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">
							<?= $this->lang->line('order_by') ?>
						</label>

						<div class="controls">
							<?= form_textarea(array('name' => 'order_by', 'rows' => '4'), $order_by) ?>
							<div class="help-block"><?= $this->lang->line('order_by_exp') ?></div>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">
							<?= $this->lang->line('max_records') ?>
						</label>

						<div class="controls">
							<?= form_input('max_records', $max_records) ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="form-actions">
		<?= form_submit('submit', $this->lang->line('submit'), 'class="btn btn-primary"') ?>
	</div>
<?= form_close() ?>

<div id="modal-properties" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="modal-label" aria-hidden="true">
	<div class="modal-header">
		<h3 id="modal-label"><?= $this->lang->line('control_properties') ?></h3>
	</div>
	
	<div class="modal-body">
		<div class="form-horizontal">
			<div class="control-group">
				<label class="control-label">
					<?= $this->lang->line('control_class') ?>
				</label>

				<div class="controls">
					<?= form_input('control_class') ?>
					<div class="help-block"><?= $this->lang->line('control_class_exp') ?></div>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label">
					<?= $this->lang->line('control_disp') ?>
				</label>

				<div class="controls">
					<?= form_textarea(array('name' => 'control_disp_path', 'rows' => '5')) ?>
					<div class="help-block"><?= $this->lang->line('control_disp_exp') ?></div>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">
					<?= $this->lang->line('control_value') ?>
				</label>

				<div class="controls">
					<?= form_input('control_value_path') ?>
					<div class="help-block"><?= $this->lang->line('control_value_exp') ?></div>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">
					<?= $this->lang->line('control_format') ?>
				</label>

				<div class="controls">
					<?= form_input('control_format') ?>
					<div class="help-block"><?= $this->lang->line('control_format_exp') ?></div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="modal-footer">
		<a id="modal-submit" href="#" class="btn btn-primary" data-dismiss="modal">
			<?= $this->lang->line('submit') ?>
		</a>

		<a id="modal-cancel" href="#" class="btn" data-dismiss="modal" aria-hidden="true">
			<?= $this->lang->line('cancel') ?>
		</a>
	</div>
</div>

<script type="text/javascript">
	$(function() {
		// Allow double clicking on control
		$('.toolbox .control').dblclick(function() {
			addControl($(this).html());
		});

		// Drag from toolbar to widget box
		$('.toolbox .control').draggable({
			appendTo: 'body',
			helper: 'clone'
		});
		$('.widget-area')
			.droppable({
				activeClass: 'widget-drop-highlight',
				accept: ':not(.dropped)',
				drop: function(event, ui) {
					addControl(ui.draggable.html());
				}
			})
			.sortable({
				items: '.control'
			});

		// Drag back to toolbox from widget box
		$('.widget-area .control').draggable();
		$('.toolbox-area').droppable({
			accept: '.dropped',
			activeClass: 'widget-drop-highlight',
			drop: function(event, ui) {
				$(ui.draggable).remove();
			},
		});

		// Go to the saved tab, if it is set
		var lastTab = localStorage.getItem('moksha_last_tab');

		if (lastTab) {
			$('a[href=' + lastTab + ']').tab('show');
		}

		// Prepare auto-load
		$('.widget-area').html($('.widget-autoloaded').html());
		$('.widget-autoloaded').remove();
	});

	setInterval(function() {
		// Control configuration
		$('.control-configure')
			.off()
			.on('click', function() {
				var $parent		= $(this).parent();
				var ctrl_hash	= hash();

				// Save the hash for future reference
				$parent.attr('id', ctrl_hash);
				localStorage.setItem('moksha_current_control', ctrl_hash);

				// Populate data from hidden fields
				var classes		= $parent.children('[name="control_classes[]"]').first().val();
				var disp_path	= $parent.children('[name="control_disp_paths[]"]').first().val();
				var value_path	= $parent.children('[name="control_value_paths[]"]').first().val();
				var format		= $parent.children('[name="control_formats[]"]').first().val();

				$('[name=control_class]').val(classes);
				$('[name=control_disp_path]').val(disp_path);
				$('[name=control_value_path]').val(value_path);
				$('[name=control_format]').val(format);

				// Show the modal config window
				$('#modal-properties').modal('show');

				return false;
			});
	}, 500);

	// Write data to hidden fields when closing
	$('#modal-submit').click(function() {
		// Get the modal form data
		var classes		= $('[name=control_class]').val();
		var disp_path	= $('[name=control_disp_path]').val();
		var value_path	= $('[name=control_value_path]').val();
		var format		= $('[name=control_format]').val();

		var key = localStorage.getItem('moksha_current_control');
		localStorage.removeItem('moksha_current_control');

		$('#' + key).children('[name="control_classes[]"]').val(classes);
		$('#' + key).children('[name="control_disp_paths[]"]').val(disp_path);
		$('#' + key).children('[name="control_value_paths[]"]').val(value_path);
		$('#' + key).children('[name="control_formats[]"]').val(format);

		// Clear modal text boxes
		$(this).children('input[type=text]').val('');
	});

	// Save the current table to local storage
	$('a[data-toggle="tab"]').on('shown', function (e) {
		localStorage.setItem('moksha_last_tab', $(e.target).attr('href'));
	});

	// Add/remove controls from the widget
	function addControl(controlHTML) {
		// Key field is already present, just change the name
		controlHTML = controlHTML.replace('toolbox_keys[]', 'control_keys[]');
		
		// Add other needed fields
		controlHTML += '<?= trim(form_hidden('control_classes[]')) ?>';
		controlHTML += '<?= trim(form_hidden('control_disp_paths[]')) ?>';
		controlHTML += '<?= trim(form_hidden('control_value_paths[]')) ?>';
		controlHTML += '<?= trim(form_hidden('control_formats[]')) ?>';
		
		// We're done, add the control to the widget
		$('<span class="control dropped"></span>').html(controlHTML).appendTo('.widget-area');
	}
</script>