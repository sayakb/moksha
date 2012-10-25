<legend>
	<a href="<?= base_url('admin/files/add') ?>" class="btn btn-primary pull-right">
		<?= $this->lang->line('add_new_file') ?>
	</a>

	<?= $this->lang->line('file_list') ?>
</legend>

<?php if (count($files) > 0): ?>
	<table class="table table-bordered table-striped">
		<colgroup>
			<col />
			<col width="100" />
		</colgroup>

		<thead>
			<tr>
				<th><?= $this->lang->line('file') ?></th>
				<th><?= $this->lang->line('actions') ?></th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($files as $file): ?>
				<tr>
					<td>
						<a href="<?= base_url($file->relative_path) ?>">
							<?= $file->file_name ?>
						</a>
					</td>
					
					<td>
						<a href="<?= base_url('admin/files/delete/'.$file->file_id) ?>" class="btn btn-mini">
							<?= $this->lang->line('delete') ?>
						</a>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>

	<div class="pagination">
		<?= $pagination ?>
	</div>
<?php else: ?>
	<div class="alert alert-info">
		<?= $this->lang->line('no_files') ?>
	</div>
<?php endif ?>