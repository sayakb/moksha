<?php if ($conf_writable): ?>
	<div class="alert alert-error">
		<?= $this->lang->line('config_writable') ?>
	</div>
<?php endif ?>

<div class="well well-small">
	<h1><?= $this->lang->line('system_information') ?></h1>

	<table class="table">
		<colgroup>
			<col width="25%" />
			<col width="25%" />
			<col width="25%" />
			<col width="25%" />
		</colgroup>

		<tbody>
			<tr>
				<td><?= $this->lang->line('php_version') ?></td>
				<td><?= $central_info->php_version ?></td>

				<td><?= $this->lang->line('mysql_version') ?></td>
				<td><?= $central_info->mysql_version ?></td>
			</tr>

			<tr>
				<td><?= $this->lang->line('moksha_version') ?></td>
				<td><?= $central_info->moksha_version ?></td>

				<td><?= $this->lang->line('db_size') ?></td>
				<td><?= $central_info->db_size ?></td>
			</tr>

			<tr>
				<td><?= $this->lang->line('server_load') ?></td>
				<td><?= $central_info->server_load ?></td>

				<td><?= $this->lang->line('server_uptime') ?></td>
				<td><?= $central_info->server_uptime ?></td>
			</tr>
		</tbody>
	</table>
</div>