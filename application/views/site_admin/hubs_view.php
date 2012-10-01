<legend>
	<a href="<?= base_url('admin/hubs/manage') ?>" class="small pull-right">
		&laquo; <?= $this->lang->line('back_hub_mgmt') ?>
	</a>

	<?= $hub_title ?>
</legend>

<div class="viewport">
	<table class="table table-striped">
		<thead>
			<tr>
				<?php foreach($hub_columns as $column): ?>
					<th><?= $column ?></td>
				<?php endforeach ?>
			</tr>
		</thead>

		<tbody>
			<?php if (count($hub_data) > 0): ?>
				<?php foreach ($hub_data as $data): ?>
					<tr>
						<?php foreach($hub_columns as $column): ?>
							<td><?= htmlspecialchars($data->$column) ?></td>
						<?php endforeach ?>
					</tr>
				<?php endforeach ?>
			<?php else: ?>
				<tr>
					<td colspan="2"><?= $this->lang->line('hub_empty') ?></td>
				</tr>
			<?php endif ?>
		</tbody>
	</table>
</div>