<?php if ($hub->hub_driver == HUB_DATABASE): ?>
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#rename-hub" data-toggle="tab">
					<?= $this->lang->line('rename_hub') ?>
				</a>
			</li>

			<li>
				<a href="#add-column" data-toggle="tab">
					<?= $this->lang->line('add_column') ?>
				</a>
			</li>

			<li>
				<a href="#rename-column" data-toggle="tab">
					<?= $this->lang->line('rename_column') ?>
				</a>
			</li>

			<li>
				<a href="#delete-column" data-toggle="tab">
					<?= $this->lang->line('delete_column') ?>
				</a>
			</li>

			<li class="nav-text pull-right">
				<a href="<?= base_url('admin/hubs/manage') ?>" class="small pull-right">
					&laquo; <?= $this->lang->line('back_hub_mgmt') ?>
				</a>
			</li>
		</ul>

		<div class="tab-content">
			<div id="rename-hub" class="tab-pane active fade in">
				<?= form_open(current_url(), array('class' => 'form-horizontal')) ?>
					<div class="control-group">
						<label class="control-label">
							<?= $this->lang->line('hub_name') ?>
						</label>

						<div class="controls">
							<?= form_input('hub_name', set_value('hub_name', $hub->hub_name)) ?>
							<span class="help-block"><?= $this->lang->line('rename_hub_exp') ?></span>
						</div>
					</div>

					<div class="form-actions">
						<?= form_hidden('hub_name_existing', $hub->hub_name) ?>
						<?= form_hidden('hub_driver', $hub->hub_driver) ?>
						<?= form_submit('rename_hub', $this->lang->line('submit'), 'class="btn btn-primary"') ?>
					</div>
				<?= form_close() ?>
			</div>

			<div id="add-column" class="tab-pane fade">
				<?= form_open(current_url(), array('class' => 'form-horizontal')) ?>
					<div class="control-group">
						<label class="control-label">
							<?= $this->lang->line('column_name') ?>
						</label>

						<div class="controls">
							<?= form_input('column_name', set_value('column_name')) ?>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">
							<?= $this->lang->line('data_type') ?>
						</label>

						<div class="controls">
							<?= form_dropdown('column_datatype', $data_types, set_value('column_datatype')) ?>
						</div>
					</div>

					<div class="form-actions">
						<?= form_hidden('hub_name', $hub->hub_name) ?>
						<?= form_submit('add_column', $this->lang->line('submit'), 'class="btn btn-primary"') ?>
					</div>
				<?= form_close() ?>
			</div>

			<div id="rename-column" class="tab-pane fade">
				<?= form_open(current_url(), array('class' => 'form-horizontal')) ?>
					<div class="control-group">
						<label class="control-label">
							<?= $this->lang->line('column_name') ?>
						</label>

						<div class="controls">
							<?= form_dropdown('column_name_existing', $hub_columns, set_value('column_name_existing')) ?>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">
							<?= $this->lang->line('column_name_new') ?>
						</label>

						<div class="controls">
							<?= form_input('column_name', set_value('column_name')) ?>
						</div>
					</div>

					<div class="form-actions">
						<?= form_hidden('hub_name', $hub->hub_name) ?>
						<?= form_submit('rename_column', $this->lang->line('submit'), 'class="btn btn-primary"') ?>
					</div>
				<?= form_close() ?>
			</div>

			<div id="delete-column" class="tab-pane fade">
				<?= form_open(current_url(), array('class' => 'form-horizontal')) ?>
					<div class="control-group">
						<label class="control-label">
							<?= $this->lang->line('column_name') ?>
						</label>

						<div class="controls">
							<?= form_dropdown('column_name_existing', $hub_columns, set_value('column_name_existing')) ?>
							<span class="help-block"><?= $this->lang->line('delete_column_exp') ?></span>
						</div>
					</div>

					<div class="form-actions">
						<?= form_hidden('hub_name', $hub->hub_name) ?>
						<?= form_submit('delete_column', $this->lang->line('submit'), 'class="btn btn-primary"') ?>
					</div>
				<?= form_close() ?>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$(function() {
			// Go to the saved tab, if it is set
			var lastTab = localStorage.getItem('moksha_last_tab');

			if (lastTab) {
				$('a[href=' + lastTab + ']').tab('show');
			}
		});

		// Save the current table to local storage
		$('a[data-toggle="tab"]').on('shown', function (e) {
			localStorage.setItem('moksha_last_tab', $(e.target).attr('href'));
		});
	</script>
<?php elseif ($hub->hub_driver == HUB_RSS): ?>
	<?= form_open(current_url(), array('class' => 'form-horizontal')) ?>
		<legend>
			<?= $this->lang->line('modify_hub') ?>

			<a href="<?= base_url('admin/hubs/manage') ?>" class="small pull-right">
				&laquo; <?= $this->lang->line('back_hub_mgmt') ?>
			</a>
		</legend>

		<div class="control-group">
			<label class="control-label">
				<?= $this->lang->line('hub_name') ?>
			</label>

			<div class="controls">
				<?= form_input('hub_name', set_value('hub_name', $hub->hub_name)) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">
				<?= $this->lang->line('hub_source') ?>
			</label>

			<div class="controls">
				<?= form_input('hub_source', set_value('hub_source', $hub->hub_source)) ?>
			</div>
		</div>

		<div class="form-actions">
			<?= form_hidden('hub_name_existing', $hub->hub_name) ?>
			<?= form_hidden('hub_driver', $hub->hub_driver) ?>
			<?= form_submit('modify_hub', $this->lang->line('submit'), 'class="btn btn-primary"') ?>
		</div>
	<?= form_close() ?>
<?php endif ?>