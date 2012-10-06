<?= form_open(current_url(), array('class' => 'form-horizontal')) ?>
	<legend>
		<a href="<?= base_url('admin/roles/manage') ?>" class="small pull-right">
			&laquo; <?= $this->lang->line('back_role_mgmt') ?>
		</a>

		<?= $this->lang->line('add_new_role') ?>
	</legend>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('role_name') ?>
		</label>

		<div class="controls">
			<?= form_input('role_name', $role_name) ?>
		</div>
	</div>

	<div class="form-actions">
		<?= form_submit('add_role', $this->lang->line('submit'), 'class="btn btn-primary"') ?>
	</div>
<?= form_close() ?>