<!DOCTYPE html>

<html dir="<?= $page_text_dir ?>" lang="<?= $page_lang ?>">
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?= $page_charset ?>" />
	<meta name="description" content="Moksha: <?= $page_desc ?>" />

	<title><?= $page_title ?></title>

	<link href="<?= base_url('assets/css/bootstrap.css') ?>" rel="stylesheet" />
	<link href="<?= base_url('assets/css/bootstrap-moksha.css') ?>" rel="stylesheet" />
	<script type="text/javascript" src="<?= base_url('assets/js/jquery.js') ?>"></script>
	<script type="text/javascript" src="<?= base_url('assets/js/bootstrap.js') ?>"></script>
</head>
<body>
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="brand" href="<?= base_url('admin/central') ?>">
					<?= $page_title ?>
				</a>

				<ul class="nav">
					<?= $page_menu ?>
				</ul>
			</div>
		</div>
	</div>

	<div class="container">
		<h4 class="pull-right">
			<i class="icon-wrench"></i>
			<?= $page_desc ?>
		</h4>
		
		<?php if (isset($page_notice)): ?>
			<div class="alert alert-<?= $page_notice['type'] ?>">
				<?= $page_notice['message'] ?>
			</div>
		<?php endif; ?>