<?= form_open(current_url(), array('class' => 'form-horizontal')) ?>
	<legend>
		<a href="<?= base_url('admin/central/sites/manage') ?>" class="small pull-right">
			&laquo; <?= $this->lang->line('back_site_mgmt') ?>
		</a>

		<?= $this->lang->line('add_new_site') ?>
	</legend>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('site_url') ?>
		</label>

		<div class="controls">
			<?= form_input('site_url', $site_url) ?>
			<span class="help-block"><?= $this->lang->line('site_url_exp') ?></span>
		</div>
	</div>

	<div class="form-actions">
		<?= form_submit('add_site', $this->lang->line('submit'), 'class="btn btn-primary"') ?>
	</div>
<?= form_close() ?>