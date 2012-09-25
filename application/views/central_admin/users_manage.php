<legend>
	<a href="<?= base_url('admin/central/users/add') ?>" class="btn btn-primary pull-right">
		<?= $this->lang->line('add_new_user') ?>
	</a>

	<?= $this->lang->line('user_list') ?>
</legend>

<?= form_open(current_url(), array('class' => 'well form-horizontal')) ?>
	<?= $this->lang->line('filter_by_username') ?>
	<?= form_input('user_filter', $this->input->post('user_filter')) ?>
	<?= form_submit('submit', $this->lang->line('submit'), 'class="btn"') ?>
<?= form_close() ?>

<?php if (count($users) > 0): ?>
	<table class="table table-bordered table-striped">
		<colgroup>
			<col />
			<col />
			<col width="100" />
		</colgroup>

		<thead>
			<tr>
				<th><?= $this->lang->line('username') ?></th>
				<th><?= $this->lang->line('email_address') ?></th>
				<th><?= $this->lang->line('actions') ?></th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($users as $user): ?>
				<tr>
					<td><?= $user->user_name ?></td>
					<td><?= $user->user_email ?></td>
					<td>
						<a href="<?= base_url('admin/central/users/edit/'.$user->user_id) ?>" class="btn btn-mini">
							<?= $this->lang->line('edit') ?>
						</a>

						<?php if ($user->user_founder == 0): ?>
							<a href="<?= base_url('admin/central/users/delete/'.$user->user_id) ?>" class="btn btn-mini">
								<?= $this->lang->line('delete') ?>
							</a>
						<?php endif; ?>
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
		<?= $this->lang->line('no_users') ?>
	</div>
<?php endif; ?>