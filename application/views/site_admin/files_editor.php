<?= form_open_multipart(current_url(), array('class' => 'form-horizontal')) ?>
	<legend>
		<a href="<?= base_url('admin/files/manage') ?>" class="small pull-right">
			&laquo; <?= $this->lang->line('back_file_mgmt') ?>
		</a>

		<?= $this->lang->line('add_new_file') ?>
	</legend>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('file') ?>
		</label>

		<div class="controls">
			<?= form_upload('file') ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('file_type') ?>
		</label>

		<div class="controls">
			<?= form_dropdown('file_type', $types, $file_type) ?>
		</div>
	</div>

	<div class="form-actions">
		<?= form_submit('add_file', $this->lang->line('submit'), 'class="btn btn-primary"') ?>
	</div>
<?= form_close() ?>