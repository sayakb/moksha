<?= form_open(current_url(), array('class' => 'form-horizontal')) ?>
	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('username') ?>
		</label>

		<div class="controls">
			<?= form_input('username') ?>
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

	<div class="form-actions">
		<?= form_submit('login_submit', $this->lang->line('submit'), 'class="btn"') ?>
	</div>
<?= form_close() ?>