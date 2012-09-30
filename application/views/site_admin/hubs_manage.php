<legend>
	<a href="<?= base_url('admin/hubs/add') ?>" class="btn btn-primary pull-right">
		<?= $this->lang->line('add_new_hub') ?>
	</a>

	<?= $this->lang->line('hub_list') ?>
</legend>

<?php if (count($hubs) > 0): ?>
	<table class="table table-bordered table-striped">
		<colgroup>
			<col />
			<col />
			<col width="100" />
		</colgroup>

		<thead>
			<tr>
				<th><?= $this->lang->line('hub_name') ?></th>
				<th><?= $this->lang->line('hub_type') ?></th>
				<th><?= $this->lang->line('actions') ?></th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($hubs as $hub): ?>
				<tr>
					<td>
						<a href="<?= base_url("admin/hubs/view/{$hub->hub_id}") ?>">
							<?= $hub->hub_name ?>
						</a>
					</td>

					<td>
						<?= $this->lang->line('hub_type_'.$hub->hub_driver) ?>
					</td>
					
					<td>
						<a href="<?= base_url('admin/hubs/edit/'.$hub->hub_id) ?>" class="btn btn-mini">
							<?= $this->lang->line('edit') ?>
						</a>

						<a href="<?= base_url('admin/hubs/delete/'.$hub->hub_id) ?>" class="btn btn-mini">
							<?= $this->lang->line('delete') ?>
						</a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div class="pagination">
		<?= $pagination ?>
	</div>
<?php else: ?>
	<div class="alert alert-info">
		<?= $this->lang->line('no_hubs') ?>
	</div>
<?php endif; ?>

<script type="text/javascript">
	// Clear localStorage to reset tabs, if we were in edit mode
	localStorage.clear();
</script>