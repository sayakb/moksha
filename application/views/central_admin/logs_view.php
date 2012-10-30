<legend>
	<?= $this->lang->line('admin_logs') ?>
</legend>

<?= form_open(current_url(), array('class' => 'well form-horizontal form-narrow')) ?>
	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('site') ?>
		</label>

		<div class="controls">
			<?= form_dropdown('site', $log_sites, $this->input->post('site') != '' ? $this->input->post('site') : -1) ?>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('from_date') ?>
		</label>

		<div class="controls">
			<div class="input-append">
				<?= form_input('from_date', $this->input->post('from_date'), 'class="datepicker"') ?>
				<span class="add-on"><i class="icon-calendar"></i></span>
			</div>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('to_date') ?>
		</label>

		<div class="controls">
			<div class="input-append">
				<?= form_input('to_date', $this->input->post('to_date'), 'class="datepicker"') ?>
				<span class="add-on"><i class="icon-calendar"></i></span>
			</div>
		</div>
	</div>

	<div class="form-actions">
		<?= form_submit('filter_logs', $this->lang->line('filter_logs'), 'class="btn"') ?>

		<a href="<?= base_url('admin/central/logs/clear') ?>" class="btn btn-danger">
			<?= $this->lang->line('clear_logs') ?>
		</a>
	</div>
<?= form_close() ?>

<?php if (count($entries) > 0): ?>
	<table class="table table-bordered table-striped">
		<colgroup>
			<col />
			<col />
			<col />
		</colgroup>

		<thead>
			<tr>
				<th><?= $this->lang->line('site') ?></th>
				<th><?= $this->lang->line('log_message') ?></th>
				<th><?= $this->lang->line('log_time') ?></th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($entries as $entry): ?>
				<tr>
					<td>
						<?php if ( ! empty($entry->site_url)): ?>
							<?= $entry->site_url ?>
						<?php else: ?>
							<em><?= $this->lang->line('central') ?></em>
						<?php endif ?>
					</td>
					<td><?= $entry->message ?></td>
					<td><?= date('m/d/Y h:i:s', $entry->log_time) ?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>

	<div class="pagination">
		<?= $pagination ?>
	</div>
<?php else: ?>
	<div class="alert alert-info">
		<?= $this->lang->line('no_log_entries') ?>
	</div>
<?php endif ?>