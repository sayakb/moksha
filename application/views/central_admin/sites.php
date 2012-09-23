<?= form_open(current_url(), array('class' => 'form-horizontal')) ?>
	<legend>
		<?= $this->lang->line('add_new_site') ?>
	</legend>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('site_url') ?>
		</label>

		<div class="controls">
			<?= form_input('site_url', set_value('site_url', '')) ?>
			<span class="help-block"><?= $this->lang->line('site_url_exp') ?></span>
		</div>
	</div>

	<div class="form-actions">
		<?= form_submit('add_site', $this->lang->line('add_site'), 'class="btn btn-primary"') ?>
	</div>

	<legend>
		<?= $this->lang->line('site_list') ?>
	</legend>

	<?php if (count($sites) > 0): ?>
		<table class="table table-bordered table-striped">
			<colgroup>
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
							<a href="<?= base_url('admin/central/sites/delete/' . $site->site_id) ?>">
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
<?= form_close() ?>