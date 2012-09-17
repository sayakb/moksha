<div class="row">
	<div class="span12">
		<?= form_open('admin/central/sites', array('class' => 'form-horizontal')) ?>
			<legend>
				<?= $this->lang->line('add_new_site') ?>
			</legend>

			<div class="control-group">
				<label class="control-label">
					<?= $this->lang->line('site_url') ?>
				</label>

				<div class="controls">
					<input type="text" name="site_url" />
					<span class="help-block"><?= $this->lang->line('site_url_exp') ?></span>
				</div>
			</div>

			<div class="control-group">
				<div class="controls">
					<input type="submit" name="add_site" class="btn" text="<?= $this->lang->line('add_site') ?>" />
				</div>
			</div>
		</form>
	</div>
</div>