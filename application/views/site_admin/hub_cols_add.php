<?= form_open(current_url()) ?>
	<legend>
		<a href="<?= base_url('admin/hubs/manage') ?>" class="small pull-right">
			&laquo; <?= $this->lang->line('back_hub_mgmt') ?>
		</a>

		<?= $this->lang->line('add_columns') ?>
	</legend>

	<a id="add-col" href="#" class="btn btn-mini pull-right">
		<?= $this->lang->line('add_more_col') ?>
	</a>

	<table>
		<colgroup>
			<col width="240" />
			<col width="240" />
		</colgroup>

		<thead>
			<tr>
				<th><?= $this->lang->line('data_type') ?></th>
				<th><?= $this->lang->line('column_name') ?></th>
			</tr>
		</thead>

		<tbody>
			<?php for ($idx = 1; $idx <= 100; $idx++): ?>
				<tr id="col-<?= $idx ?>" class="hide">
					<td><?= form_dropdown('column_datatypes[]', $data_types, set_value('column_datatypes[]')) ?></td>
					<td><?= form_input('column_names[]', set_value('column_names[]')) ?></td>
				</tr>
			<?php endfor ?>
		</tbody>
	</table>

	<div class="form-actions">
		<?= form_hidden('column_count', set_value('column_count', 5)) ?>
		<?= form_hidden('validation_key', $this->config->item('encryption_key')) ?>
		<?= form_submit('submit', $this->lang->line('submit'), 'class="btn btn-primary"') ?>
	</div>
<?= form_close() ?>

<script type="text/javascript">
	// Show boxes based on column count
	setInterval(function() {
		var count = $('[name=column_count]').val();

		for (idx = 1; idx <= count; idx++) {
			$('#col-' + idx).show();
		}
	}, 200);

	// Increment column count
	$('#add-col').click(function() {
		var new_count = parseInt($('[name=column_count]').val()) + 1;
		$('[name=column_count]').val(new_count);

		return false;
	});
</script>