<?= form_open(current_url(), array('class' => 'form-horizontal', 'autocomplete' => 'off')) ?>
	<legend>
		<a href="<?= base_url('admin/central/users/manage') ?>" class="small pull-right">
			&laquo; <?= $this->lang->line('back_user_mgmt') ?>
		</a>

		<?= $this->lang->line('add_new_user') ?>
	</legend>

	<div class="control-group">
		<div class="control-label">
			<?= $this->lang->line('username') ?>
		</div>

		<div class="controls">
			<?= form_input('username', $username) ?>
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<?= $this->lang->line('email_address') ?>
		</div>

		<div class="controls">
			<?= form_input('email', $email) ?>
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<?= $this->lang->line('password') ?>
		</div>

		<div class="controls">
			<?= form_password('password') ?>
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<?= $this->lang->line('confirm_password') ?>
		</div>

		<div class="controls">
			<?= form_password('confirm_password') ?>
		</div>
	</div>

	<div class="form-actions">
		<?= form_submit('user_submit', $this->lang->line('add_user'), 'class="btn btn-primary"') ?>
	</div>
<?= form_close() ?>
