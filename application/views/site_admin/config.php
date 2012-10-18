<?= form_open(current_url(), array('class' => 'form-horizontal')) ?>
	<legend>
		<?= $this->lang->line('site_config') ?>
	</legend>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('site_status') ?>
		</label>

		<div class="controls">
			<label class="radio inline">
				<?= form_radio('status', ONLINE, $status_online) ?>
				<?= $this->lang->line('online') ?>
			</label>

			<label class="radio inline">
				<?= form_radio('status', OFFLINE, $status_offline) ?>
				<?= $this->lang->line('offline') ?>
			</label>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('user_login') ?>
		</label>

		<div class="controls">
			<label class="radio inline">
				<?= form_radio('login', ENABLED, $login_enabled) ?>
				<?= $this->lang->line('enabled') ?>
			</label>

			<label class="radio inline">
				<?= form_radio('login', DISABLED, $login_disabled) ?>
				<?= $this->lang->line('disabled') ?>
			</label>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('user_registration') ?>
		</label>

		<div class="controls">
			<label class="radio inline">
				<?= form_radio('registration', ENABLED, $registration_enabled) ?>
				<?= $this->lang->line('enabled') ?>
			</label>

			<label class="radio inline">
				<?= form_radio('registration', DISABLED, $registration_disabled) ?>
				<?= $this->lang->line('disabled') ?>
			</label>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('registration_captcha') ?>
		</label>

		<div class="controls">
			<label class="radio inline">
				<?= form_radio('captcha', ENABLED, $captcha_enabled) ?>
				<?= $this->lang->line('enabled') ?>
			</label>

			<label class="radio inline">
				<?= form_radio('captcha', DISABLED, $captcha_disabled) ?>
				<?= $this->lang->line('disabled') ?>
			</label>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('visitor_stats') ?>
		</label>

		<div class="controls">
			<label class="radio inline">
				<?= form_radio('stats', ENABLED, $stats_enabled) ?>
				<?= $this->lang->line('enabled') ?>
			</label>

			<label class="radio inline">
				<?= form_radio('stats', DISABLED, $stats_disabled) ?>
				<?= $this->lang->line('disabled') ?>
			</label>
		</div>
	</div>

	<div class="form-actions">
		<?= form_submit('add_role', $this->lang->line('submit'), 'class="btn btn-primary"') ?>
	</div>
<?= form_close() ?>