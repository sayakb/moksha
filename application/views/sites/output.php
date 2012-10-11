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
	<script type="text/javascript" src="<?= base_url('assets/js/utils.js') ?>"></script>
</head>
<body class="page <?= $page_class ?>">
	<div class="container">
		<div class="row">
			<?= $page_content ?>
		</div>
	</div>
</body>
</html>