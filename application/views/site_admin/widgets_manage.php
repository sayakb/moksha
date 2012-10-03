<legend>
	<a href="<?= base_url('admin/widgets/add') ?>" class="btn btn-primary pull-right">
		<?= $this->lang->line('add_new_widget') ?>
	</a>

	<?= $this->lang->line('widget_list') ?>
</legend>

<?php if (count($widgets) > 0): ?>
	<table class="table table-bordered table-striped">
		<colgroup>
			<col />
			<col width="100" />
		</colgroup>

		<thead>
			<tr>
				<th><?= $this->lang->line('widget_name') ?></th>
				<th><?= $this->lang->line('actions') ?></th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($widgets as $widget): ?>
				<tr>
					<td>
						<?= $widget->widget_name ?>
					</td>

					<td>
						<a href="<?= base_url('admin/widgets/edit/'.$widget->widget_id) ?>" class="btn btn-mini">
							<?= $this->lang->line('edit') ?>
						</a>

						<a href="<?= base_url('admin/widgets/delete/'.$widget->widget_id) ?>" class="btn btn-mini">
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
		<?= $this->lang->line('no_widgets') ?>
	</div>
<?php endif ?>