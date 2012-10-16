<!DOCTYPE html>

<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF8" />
	<title><?= $page_title ?></title>

	<link href="<?= base_url('assets/css/stylesheet.css') ?>" rel="stylesheet" />
	<link href="<?= base_url('assets/css/jquery-ui.css') ?>" rel="stylesheet" />

	<script type="text/javascript" src="<?= base_url('assets/js/jquery.js') ?>"></script>
	<script type="text/javascript" src="<?= base_url('assets/js/jquery-ui.js') ?>"></script>
	<script type="text/javascript" src="<?= base_url('assets/js/script.js') ?>"></script>
	<script type="text/javascript" src="<?= base_url('assets/js/wysiwyg-lib.js') ?>"></script>
	<script type="text/javascript" src="<?= base_url('assets/js/wysiwyg-editor.js') ?>"></script>
	<script type="text/javascript" src="<?= base_url('assets/js/prettify.js') ?>"></script>
	<script type="text/javascript" src="<?= base_url('assets/js/moksha.js') ?>"></script>

	<?= $page_header ?>
</head>
<body class="page <?= $page_class ?>">
	<div class="container">
		<div class="row">
			<?= $page_content ?>
		</div>
	</div>

	<div id="modal-delete" class="modal modal-small hide fade">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3><?= $this->lang->line('confirm') ?></h3>
		</div>

		<div class="modal-body">
			<p><?= $this->lang->line('confirm_delete') ?></p>
		</div>

		<div class="modal-footer">
			<a id="modal-yes" href="#" class="btn btn-danger">
				<?= $this->lang->line('yes') ?>
			</a>

			<a id="modal-no" href="#" class="btn" data-dismiss="modal">
				<?= $this->lang->line('no') ?>
			</a>
		</div>
	</div>

	<script type="text/javascript">
		$(initPage);
	</script>
</body>
</html>