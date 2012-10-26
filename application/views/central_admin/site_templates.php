<?= form_open_multipart(current_url(), array('class' => 'form-horizontal')) ?>
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#export-tpl" data-toggle="tab"><?= $this->lang->line('export_tpl') ?></a>
			</li>

			<li>
				<a href="#import-tpl" data-toggle="tab"><?= $this->lang->line('import_tpl') ?></a>
			</li>
		</ul>

		<div class="tab-content">
			<div id="export-tpl" class="tab-pane active fade in">
				<div class="control-group">
					<div class="control-label">
						<?= $this->lang->line('select_site') ?>
					</div>

					<div class="controls">
						<?= form_dropdown('site', $sites) ?>
						<div class="help-block"><?= $this->lang->line('select_site_exp') ?></div>
					</div>
				</div>

				<div class="form-actions">
					<?= form_submit('export', $this->lang->line('export'), 'class="btn btn-primary"') ?>
				</div>
			</div>

			<div id="import-tpl" class="tab-pane fade">
				<div class="control-group">
					<div class="control-label">
						<?= $this->lang->line('site_url') ?>
					</div>

					<div class="controls">
						<?= form_input('site_url', set_value('site_url')) ?>
					</div>
				</div>

				<div class="control-group">
					<div class="control-label">
						<?= $this->lang->line('template_file') ?>
					</div>

					<div class="controls">
						<?= form_upload('template') ?>
						<div class="help-block"><?= $this->lang->line('template_file_exp') ?></div>
					</div>
				</div>

				<div class="form-actions">
					<?= form_submit('import', $this->lang->line('import'), 'class="btn btn-primary"') ?>
				</div>
			</div>
		</div>
	</div>
<?= form_close() ?>