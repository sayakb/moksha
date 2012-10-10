<legend>
	<a href="<?= base_url('admin/pages/add') ?>" class="btn btn-primary pull-right">
		<?= $this->lang->line('add_new_page') ?>
	</a>

	<?= $this->lang->line('page_list') ?>
</legend>

<?php if (count($pages) > 0): ?>
	<table class="table table-bordered table-striped">
		<colgroup>
			<col />
			<col />
			<col width="100" />
		</colgroup>

		<thead>
			<tr>
				<th><?= $this->lang->line('page_title') ?></th>
				<th><?= $this->lang->line('page_url') ?></th>
				<th><?= $this->lang->line('actions') ?></th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($pages as $page): ?>
				<tr>
					<td>
						<?= $page->page_title ?>
					</td>

					<td>
						<a href="<?= base_url(expr($page->page_url)) ?>">
							<?= $page->page_url ?>
						</a>
					</td>

					<td>
						<a href="<?= base_url('admin/pages/edit/'.$page->page_id) ?>" class="btn btn-mini">
							<?= $this->lang->line('edit') ?>
						</a>

						<a href="<?= base_url('admin/pages/delete/'.$page->page_id) ?>" class="btn btn-mini">
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
		<?= $this->lang->line('no_pages') ?>
	</div>
<?php endif ?>