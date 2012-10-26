<?= form_open(current_url(), array('class' => 'form-horizontal')) ?>
	<legend>
		<a href="<?= base_url('admin/hubs/manage') ?>" class="small pull-right">
			&laquo; <?= $this->lang->line('back_hub_mgmt') ?>
		</a>

		<?= $this->lang->line('add_new_hub') ?>
	</legend>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('hub_name') ?>
		</label>

		<div class="controls">
			<?= form_input('hub_name', set_value('hub_name')) ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('hub_type') ?>
		</label>

		<div class="controls">
			<?= form_dropdown('hub_type', $hub_types, set_value('hub_type')) ?>
		</div>
	</div>

	<div id="hub-source" class="control-group hide">
		<label class="control-label">
			<?= $this->lang->line('hub_source') ?>
		</label>

		<div class="controls">
			<?= form_input('source', set_value('source')) ?>
			<span class="help-block"><?= $this->lang->line('hub_source_exp') ?></span>
		</div>
	</div>

	<div class="form-actions">
		<?= form_submit('submit', $this->lang->line('submit'), 'class="btn btn-primary"') ?>
	</div>
<?= form_close() ?>

<script type="text/javascript">
	// Show the hub source box on load if the textbox has value
	$(function() {
		if ($('[name=hub_type]').val() == '<?= HUB_RSS ?>') {
			$('#hub-source').show();
		}
	});

	// Toggle visibility of rss source box
	$('[name=hub_type]').change(function() {
		if ($(this).val() == '<?= HUB_RSS ?>') {
			$('#hub-source').show();
		} else {
			$('#hub-source').hide();
		}
	});
</script>