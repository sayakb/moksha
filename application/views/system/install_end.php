<p><?= $this->lang->line('moksha_installed') ?></p>

<dl class="well dl-horizontal">
	<dt><?= $this->lang->line('central_url') ?></dt>
	<dd>
		<a href="<?= base_url('admin/central') ?>">
			<?= base_url('admin/central') ?>
		</a>
	</dd>

	<dt><?= $this->lang->line('username') ?></dt>
	<dd><?= $username ?></dd>

	<dt><?= $this->lang->line('password') ?></dt>
	<dd><?= $password ?></dd>
</dl>