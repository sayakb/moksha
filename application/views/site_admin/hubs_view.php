<legend>
	<a href="<?= base_url('admin/hubs/manage') ?>" class="small pull-right">
		&laquo; <?= $this->lang->line('back_hub_mgmt') ?>
	</a>

	<?= $hub_title ?>
</legend>

<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<?php foreach($hub_columns as $column): ?>
				<th><?= $column ?></td>
			<?php endforeach; ?>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($hub_data as $data): ?>
			<tr>
				<?php foreach($hub_columns as $column): ?>
					<td><?= $data->$column ?></td>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>