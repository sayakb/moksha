<?= form_open(current_url()) ?>
	<legend>
		<a href="<?= base_url('admin/pages/manage') ?>" class="small pull-right">
			&laquo; <?= $this->lang->line('back_page_mgmt') ?>
		</a>

		<?= $this->lang->line('add_new_page') ?>
	</legend>

	<div class="form-horizontal">
		<div class="control-group">
			<label class="control-label">
				<?= $this->lang->line('page_title') ?>
			</label>

			<div class="controls">
				<?= form_input('pg_title', $pg_title) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">
				<?= $this->lang->line('page_url') ?>
			</label>

			<div class="controls">
				<?= form_input('pg_url', $pg_url) ?>
				<span class="help-block"><?= $this->lang->line('page_url_exp') ?></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">
				<?= $this->lang->line('page_layout') ?>
			</label>

			<div class="controls">
				<div id="page-layout" class="btn-group" data-toggle="buttons-radio">
					<button type="button" class="btn active" data-toggle="button" data-layout="1-1-1">
						<i class="icon-layout-1-1-1"></i>
						<?= form_radio('pg_layouts[]', '1-1-1') ?>
					</button>

					<button type="button" class="btn" data-toggle="button" data-layout="2-1">
						<i class="icon-layout-2-1"></i>
						<?= form_radio('pg_layouts[]', '2-1') ?>
					</button>

					<button type="button" class="btn" data-toggle="button" data-layout="1-2">
						<i class="icon-layout-1-2"></i>
						<?= form_radio('pg_layouts[]', '1-2') ?>
					</button>

					<button type="button" class="btn" data-toggle="button" data-layout="3">
						<i class="icon-layout-3"></i>
						<?= form_radio('pg_layouts[]', '3') ?>
					</button>
				</div>

				<?= form_hidden('pg_layout', $pg_layout) ?>
			</div>
		</div>
	</div>
	<hr />

	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#widget-config" data-toggle="tab"><?= $this->lang->line('widget_config') ?></a>
			</li>

			<li>
				<a href="#access-control" data-toggle="tab"><?= $this->lang->line('access_control') ?></a>
			</li>
		</ul>

		<div class="tab-content">
			<div id="widget-config" class="tab-pane active fade in">
				<div class="widget-box">
					<a id="widget-box-toggle" href="#" class="btn btn-mini pull-right">
						<i class="icon-chevron-down"></i>
					</a>

					<h1><?= $this->lang->line('widgets') ?></h1>

					<div class="widget-area widget-area-page">
						<?php foreach($widgets as $widget): ?>
							<span class="widget" data-widget-id="<?= $widget->widget_id ?>">
								<a href="#" title="<?= $this->lang->line('widget_remove') ?>" class="widget-remove">
									<i class="icon-remove"></i>
								</a>

								<i class="icon-control-widget"></i>
								<?= $widget->widget_name ?>

								<?= form_hidden('widget_key', $widget->widget_id) ?>
							</span>
						<?php endforeach ?>
					</div>
				</div>

				<div class="row page-box">
					<h1><?= $this->lang->line('page') ?></h1>

					<div class="page-area column1 span1"></div>
					<div class="page-area column2 span1"></div>
					<div class="page-area column3 span1"></div>

					<?= form_hidden('pg_column1', $pg_column1) ?>
					<?= form_hidden('pg_column2', $pg_column2) ?>
					<?= form_hidden('pg_column3', $pg_column3) ?>
				</div>
			</div>
			
			<div id="access-control" class="tab-pane fade">
				<div class="form-horizontal">
					<div class="control-group page-roles">
						<label class="control-label">
							<?= $this->lang->line('page_accessible_by') ?>
						</label>

						<div class="controls">
							<?php foreach ($roles as $role): ?>
								<label class="checkbox">
									<?= form_checkbox('pg_role', $role->role_id) ?>
									<?= $role->role_name ?>
								</label>
							<?php endforeach ?>
							
							<?= form_hidden('pg_roles', $pg_roles) ?>
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

<script type="text/javascript">
	$(function() {
		// Drag from widget box to page area
		$('.widget-box .widget').draggable({
			appendTo: 'body',
			helper: 'clone',
			revert: 'invalid'
		});

		$('.page-area')
			.droppable({
				activeClass: 'widget-drop-active',
				hoverClass: 'widget-drop-hover',
				accept: ':not(.dropped)',
				drop: function(event, ui) {
					$('<span class="widget dropped"></span>').html(ui.draggable.html()).appendTo($(this));
					syncWidgets();
				}
			})
			.sortable({
				items: '.widget',
				stop: syncWidgets
			});

		// Load page widgets
		loadWidgets($('[name=pg_column1]').val(), '1');
		loadWidgets($('[name=pg_column2]').val(), '2');
		loadWidgets($('[name=pg_column3]').val(), '3');

		// Load the page layout
		var layout = $('[name=pg_layout]').val();

		if (layout != '') {
			$('#page-layout button').removeClass('active');
			$('#page-layout [data-layout=' + layout + ']')
				.addClass('active')
				.children('input[type=radio]').attr('checked', 'checked');

			changeLayout();
		}

		// Load page roles
		var page_roles = $('[name=pg_roles]').val();

		if (page_roles != '') {
			$.each(page_roles.split('|'), function(idx, val) {
				$('.page-roles input[value=' + val + ']').attr('checked', 'checked');
			});
		}
	});

	setInterval(function() {
		// Remove widget
		$('.page-box .widget-remove')
			.off()
			.on('click', function() {
				$(this).parent().remove();
				syncWidgets();

				return false;
			});
	}, 500);

	// Toggle widget box height
	$('#widget-box-toggle').click(function() {
		if ($(this).hasClass('in')) {
			$('.widget-area').animate({
				maxHeight: 90
			}, 'slow', function() {
				$('#widget-box-toggle').removeClass('in');
				$('#widget-box-toggle i').attr('class', 'icon-chevron-down');
			});
		} else {
			$('.widget-area').animate({
				maxHeight: 500
			}, 'slow', function() {
				$('#widget-box-toggle').addClass('in');
				$('#widget-box-toggle i').attr('class', 'icon-chevron-up');
			});
		}

		return false;
	});
	
	// Update the page roles checkbox data to the hidden field
	$('.page-roles input[type=checkbox]').click(function() {
		var page_roles = new Array();

		$('.page-roles input[type=checkbox]').each(function() {
			if ($(this).is(':checked')) {
				page_roles.push($(this).val());
			}
		});

		$('[name=pg_roles]').val(page_roles.join('|'));
	});

	// Set page layout
	$('#page-layout button').click(changeLayout);

	// Change page layout
	function changeLayout() {
		var layout = $('#page-layout input[type=radio]:checked').val();
		$('[name=pg_layout]').val(layout);

		// Adjust the view based on layout
		switch(layout)
		{
			case '1-1-1':
				$('.column1, .column2, .column3').removeClass('span2 span3').addClass('span1').show();
				break;

			case '2-1':
				$('.column1').removeClass('span1 span3').addClass('span2').show();
				$('.column2').removeClass('span2 span3').addClass('span1').show();
				$('.column3').hide().children('.widget').remove();
				break;

			case '1-2':
				$('.column1').removeClass('span2 span3').addClass('span1').show();
				$('.column2').removeClass('span1 span3').addClass('span2').show();
				$('.column3').hide().children('.widget').remove();
				break;

			case '3':
				$('.column1').removeClass('span1 span2').addClass('span3').show();
				$('.column2, .column3').hide().children('.widget').remove();
				break;
		}

		// Sync it up!
		syncWidgets();
	}

	// Sync added widgets to hidden fields
	function syncWidgets() {
		var column1 = new Array();
		var column2 = new Array();
		var column3 = new Array();

		$('.column1 .widget').each(function() {
			var id = $(this).children('[name=widget_key]').val();
			column1.push(id);
		});

		$('.column2 .widget').each(function() {
			var id = $(this).children('[name=widget_key]').val();
			column2.push(id);
		});

		$('.column3 .widget').each(function() {
			var id = $(this).children('[name=widget_key]').val();
			column3.push(id);
		});

		$('[name=pg_column1]').val(column1.join('|'));
		$('[name=pg_column2]').val(column2.join('|'));
		$('[name=pg_column3]').val(column3.join('|'));
	}

	// Load widgets for the page
	function loadWidgets(widgets, column) {
		if (widgets != '') {
			var widget_ary = widgets.split('|');

			$.each(widget_ary, function(idx, val) {
				var widgetHTML = $('[data-widget-id=' + val + ']').html();
				$('<span class="widget dropped"></span>').html(widgetHTML).appendTo($('.column' + column));
			});
		}
	}
</script>