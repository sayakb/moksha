<legend>
	<a href="<?= base_url('admin/hubs/manage') ?>" class="small pull-right">
		&laquo; <?= $this->lang->line('back_hub_mgmt') ?>
	</a>

	<?= $hub_title ?>
</legend>

<?= form_open(current_url(), array('class' => 'well form-horizontal')) ?>
	<?php foreach ($hub_columns as $key => $column): ?>
		<div class="control-group">
			<label class="control-label">
				<?= $column ?>
			</label>

			<div class="controls">
				<?= form_input('filters[]', $filters ? $filters[$index++] : '') ?>
				<?= form_hidden('columns[]', $column) ?>
			</div>
		</div>
	<?php endforeach ?>

	<div class="form-actions">
		<?= form_submit('filter', $this->lang->line('filter'), 'class="btn"') ?>
	</div>
<?= form_close() ?>

<div class="viewport">
	<table class="table table-bordered table-striped">
		<thead>
			<tr>
				<?php foreach ($hub_columns as $column): ?>
					<th><?= $column ?></td>
				<?php endforeach ?>
			</tr>
		</thead>

		<tbody>
			<?php if (count($hub_data) > 0): ?>
				<?php foreach ($hub_data as $data): ?>
					<tr>
						<?php foreach ($hub_columns as $column): ?>
							<td><?= htmlspecialchars($data->$column) ?></td>
						<?php endforeach ?>
					</tr>
				<?php endforeach ?>
			<?php else: ?>
				<tr>
					<td colspan="<?= count($hub_columns) ?>"><?= $this->lang->line('hub_empty') ?></td>
				</tr>
			<?php endif ?>
		</tbody>
	</table>
</div>