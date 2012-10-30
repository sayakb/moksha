<?= form_open(current_url(), array('class' => 'form-horizontal form-narrow')) ?>
	<div id="password-box" class="modal modal-medium show">
		<div class="modal-header">
			<h3><?= $this->lang->line('authentication_reqd') ?></h3>
		</div>

		<div class="modal-body">
			<p>
				<i class="icon-lock"></i>
				<?= $this->lang->line('password_exp') ?>
			</p>
			<br />

			<div class="control-group">
				<label class="control-label">
					<?= $this->lang->line('password') ?>
				</label>

				<div class="controls">
					<?= form_password('password') ?>
				</div>
			</div>
		</div>

		<div class="modal-footer">
			<?= form_submit('submit', $this->lang->line('submit'), 'class="btn btn-primary"') ?>
			<?= form_submit('cancel', $this->lang->line('cancel'), 'class="btn"') ?>
		</div>
	</div>
<?= form_close() ?>

<script type="text/javascript">
	$('#password-box').modal('show');
</script>