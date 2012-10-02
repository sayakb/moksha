<legend>
	<a href="<?= base_url('admin/controls/add') ?>" class="btn btn-primary pull-right">
		<?= $this->lang->line('add_new_control') ?>
	</a>

	<?= $this->lang->line('control_list') ?>
</legend>

<?php if (count($controls) > 0): ?>
	<table class="table table-bordered table-striped">
		<colgroup>
			<col />
			<col width="100" />
		</colgroup>

		<thead>
			<tr>
				<th><?= $this->lang->line('control_name') ?></th>
				<th><?= $this->lang->line('actions') ?></th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($controls as $control): ?>
				<tr>
					<td>
						<?= $control->control_name ?>
					</td>

					<td>
						<a href="<?= base_url('admin/controls/edit/'.$control->control_id) ?>" class="btn btn-mini">
							<?= $this->lang->line('edit') ?>
						</a>

						<a href="<?= base_url('admin/controls/delete/'.$control->control_id) ?>" class="btn btn-mini">
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
		<?= $this->lang->line('no_controls') ?>
	</div>
<?php endif ?>