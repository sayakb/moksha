<?= form_open('admin/central/sites', array('class' => 'form-horizontal')) ?>
	<legend>
		<?= $this->lang->line('add_new_site') ?>
	</legend>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('site_url') ?>
		</label>

		<div class="controls">
			<input type="text" name="site_url" maxlength="255" />
			<span class="help-block"><?= $this->lang->line('site_url_exp') ?></span>
		</div>
	</div>

	<div class="control-group">
		<div class="controls">
			<input type="submit" name="add_site" class="btn" text="<?= $this->lang->line('add_site') ?>" />
		</div>
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
							<a href="//<?= $site['site_url'] ?>">
								<?= $site['site_url'] ?>
							</a>
						</td>

						<td>
							<a href="<?= base_url('admin/central/sites/delete/' . $site['site_id']) ?>"
								onclick="return confirm('<?= $this->lang->line('confirm_action') ?>')">
								<?= $this->lang->line('delete') ?>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<div class="pagination">
			<ul>
				<?= $pagination ?>
			</ul>
		</div>
	<?php else: ?>
		<div class="alert alert-info">
			<?= $this->lang->line('no_sites') ?>
		</div>
	<?php endif; ?>
</form>