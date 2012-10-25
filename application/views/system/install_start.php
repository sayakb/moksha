<?= form_open(current_url(), array('class' => 'form-horizontal')) ?>
	<legend>
		<?= $this->lang->line('db_config') ?>
	</legend>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('hostname') ?>
		</label>

		<div class="controls">
			<?= form_input('hostname', set_value('hostname')) ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('port') ?>
		</label>

		<div class="controls">
			<?= form_input('port', set_value('port')) ?>
			<div class="help-block"><?= $this->lang->line('port_exp') ?></div>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('username') ?>
		</label>

		<div class="controls">
			<?= form_input('username', set_value('username')) ?>
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
			<?= $this->lang->line('db_name') ?>
		</label>

		<div class="controls">
			<?= form_input('db_name', set_value('db_name')) ?>
		</div>
	</div>

	<div class="form-actions">
		<?= form_hidden('key') ?>
		<?= form_submit('start_install', $this->lang->line('start_install'), 'class="btn btn-primary"') ?>
	</div>
<?= form_close() ?>