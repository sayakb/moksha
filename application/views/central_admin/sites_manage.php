<legend>
	<a href="<?= base_url('admin/central/sites/add') ?>" class="btn btn-primary pull-right">
		<?= $this->lang->line('add_new_site') ?>
	</a>

	<?= $this->lang->line('site_list') ?>
</legend>

<?php if (count($sites) > 0): ?>
	<table class="table table-bordered table-striped">
		<colgroup>
			<col />
			<col />
			<col width="100" />
		</colgroup>

		<thead>
			<tr>
				<th><?= $this->lang->line('site_url') ?></th>
				<th><?= $this->lang->line('actions') ?></th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($sites as $site): ?>
				<tr>
					<td>
						<a href="//<?= $site->site_url ?>">
							<?= $site->site_url ?>
						</a>
					</td>
					
					<td>
						<a href="<?= base_url('admin/central/sites/edit/' . $site->site_id) ?>" class="btn btn-mini">
							<?= $this->lang->line('edit') ?>
						</a>

						<a href="<?= base_url('admin/central/sites/delete/' . $site->site_id) ?>" class="btn btn-mini">
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
		<?= $this->lang->line('no_sites') ?>
	</div>
<?php endif; ?>