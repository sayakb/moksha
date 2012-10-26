<legend>
	<a href="<?= base_url('admin/roles/add') ?>" class="btn btn-primary pull-right">
		<?= $this->lang->line('add_new_role') ?>
	</a>

	<?= $this->lang->line('role_list') ?>
</legend>

<?php if (count($roles) > 0): ?>
	<table class="table table-bordered table-striped">
		<colgroup>
			<col />
			<col width="100" />
		</colgroup>

		<thead>
			<tr>
				<th><?= $this->lang->line('role_name') ?></th>
				<th><?= $this->lang->line('actions') ?></th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($roles as $role): ?>
				<tr>
					<td>
						<?= $role->role_name ?>
					</td>
					
					<td>
						<a href="<?= base_url('admin/roles/edit/'.$role->role_id) ?>" class="btn btn-mini">
							<?= $this->lang->line('edit') ?>
						</a>

						<a href="<?= base_url('admin/roles/delete/'.$role->role_id) ?>" class="btn btn-mini">
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
		<?= $this->lang->line('no_roles') ?>
	</div>
<?php endif ?>