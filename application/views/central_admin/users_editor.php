<?= form_open(current_url(), array('class' => 'form-horizontal', 'autocomplete' => 'off')) ?>
	<legend>
		<a href="<?= base_url('admin/central/users/manage') ?>" class="small pull-right">
			&laquo; <?= $this->lang->line('back_user_mgmt') ?>
		</a>

		<?= $this->lang->line('add_new_user') ?>
	</legend>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('username') ?>
		</label>

		<div class="controls">
			<?= form_input('username', $username) ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('email_address') ?>
		</label>

		<div class="controls">
			<?= form_input('email_address', $email_address) ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('password') ?>
		</label>

		<div class="controls">
			<?= form_password('password') ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('confirm_password') ?>
		</label>

		<div class="controls">
			<?= form_password('confirm_password') ?>
		</div>
	</div>

	<div class="form-actions">
		<?= form_submit('user_submit', $this->lang->line('submit'), 'class="btn btn-primary"') ?>
	</div>
<?= form_close() ?>
