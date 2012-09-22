<?= form_open(current_url(), array('class' => 'well form-horizontal')) ?>
	<legend><?= $caption ?></legend>
	<p><?= $message ?></p>

	<?= form_submit('yes', $this->lang->line('yes'), 'class="btn"') ?>
	<?= form_submit('no', $this->lang->line('no'), 'class="btn"') ?>
<?= form_close() ?>

