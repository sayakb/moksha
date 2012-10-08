<!DOCTYPE html>

<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF8" />
	<meta name="description" content="Moksha: <?= $page_desc ?>" />

	<title><?= $page_title ?></title>

	<link href="<?= base_url('assets/css/stylesheet.css') ?>" rel="stylesheet" />
	<link href="<?= base_url('assets/css/wysiwyg.css') ?>" rel="stylesheet" />
	<link href="<?= base_url('assets/css/jquery-ui.css') ?>" rel="stylesheet" />
	<script type="text/javascript" src="<?= base_url('assets/js/jquery.js') ?>"></script>
	<script type="text/javascript" src="<?= base_url('assets/js/jquery-ui.js') ?>"></script>
	<script type="text/javascript" src="<?= base_url('assets/js/script.js') ?>"></script>
	<script type="text/javascript" src="<?= base_url('assets/js/wysiwyg-lib.js') ?>"></script>
	<script type="text/javascript" src="<?= base_url('assets/js/wysiwyg-editor.js') ?>"></script>
	<script type="text/javascript" src="<?= base_url('assets/js/utils.js') ?>"></script>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="span12">
				<div class="page-header">
					<div class="page-logout">
						<?= $page_logout ?>
					</div>

					<h1><?= $page_title ?></h1>
					<p><?= $page_desc ?></p>
				</div>

				<?php if (isset($page_notice)): ?>
					<div class="alert alert-<?= $page_notice['type'] ?>">
						<?= $page_notice['message'] ?>
					</div>
				<?php endif ?>
			</div>
		</div>