<?= form_open(current_url(), array('class' => 'form-horizontal', 'autocomplete' => 'off')) ?>
	<legend>
		<a href="<?= base_url('admin/users/manage') ?>" class="small pull-right">
			&laquo; <?= $this->lang->line('back_user_mgmt') ?>
		</a>

		<?= $this->lang->line('add_new_user') ?>
	</legend>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('username') ?>
		</label>

		<div class="controls">
			<?= form_input('username', $username) ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('email_address') ?>
		</label>

		<div class="controls">
			<?= form_input('email_address', $email_address) ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('password') ?>
		</label>

		<div class="controls">
			<?= form_password('password') ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">
			<?= $this->lang->line('confirm_password') ?>
		</label>

		<div class="controls">
			<?= form_password('confirm_password') ?>
		</div>
	</div>

	<div class="control-group user-roles">
		<label class="control-label">
			<?= $this->lang->line('roles') ?>
		</label>

		<div id="user-roles" class="controls">
			<?php foreach ($roles as $role): ?>
				<label class="checkbox">
					<?= form_checkbox('roles[]', $role->role_id) ?>
					<?= $role->role_name ?>
				</label>
			<?php endforeach ?>
		</div>
	</div>

	<div class="form-actions">
		<?= form_hidden('roles', $roles) ?>
		<?= form_submit('user_submit', $this->lang->line('submit'), 'class="btn btn-primary"') ?>
	</div>
<?= form_close() ?>

<script type="text/javascript">
	// Load user roles to checkbox, if set
	$(function() {
		var roles = $('[name=roles]').val();

		if (roles != '') {
			var roles_ary = roles.split('|');

			$.each(roles_ary, function(idx, val) {
				$('.user-roles input[value=' + val + ']').attr('checked', 'checked');
			});
		}
	});

	// Update the hidden field on checkbox click
	$('#user-roles input[type=checkbox]').click(function() {
		var roles = new Array();

		$('#user-roles input[type=checkbox]').each(function() {
			if ($(this).is(':checked')) {
				roles.push($(this).val());
			}
		});

		$('[name=roles]').val(roles.join('|'));
	});

	// Disable the admin checkbox
	<?php if ($adm_disabled): ?>
		$('.user-roles input[value=<?= ROLE_ADMIN ?>]').attr('disabled', 'disabled');
	<?php endif ?>
</script>