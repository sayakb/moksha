<?= form_open(current_url(), array('class' => 'form-horizontal', 'autocomplete' => 'off')) ?>
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

	<?php if ($captcha != NULL): ?>
		<div class="control-group">
			<label class="control-label">
				<?= $this->lang->line('visual_verif') ?>
			</label>

			<div class="controls">
				<div id="captcha-image">
					<?= $captcha ?>
				</div>
			</div>
		</div>

		<div class="control-group">
			<div class="controls">
				<div class="input-append">
					<?= form_input('captcha') ?>

					<a id="captcha-reload" href="#" class="btn" title="<?= $this->lang->line('reload_captcha') ?>">
						<i class="icon-refresh"></i>
					</a>
				</div>

				<div class="help-block"><?= $this->lang->line('visual_verif_exp') ?></div>
			</div>
		</div>
	<?php endif ?>

	<div class="form-actions">
		<?= form_submit('register_submit', $this->lang->line('submit'), 'class="btn btn-primary"') ?>
	</div>
<?= form_close() ?>

<?php if ($captcha != NULL): ?>
	<script type="text/javascript">
		$('#captcha-reload').click(function() {
			$.get('<?= base_url('register/captcha') ?>', function(data) {
				$('#captcha-image').html(data);
			});

			return false;
		});
	</script>
<?php endif ?>