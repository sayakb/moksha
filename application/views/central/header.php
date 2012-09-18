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
	<div class="container">
		<div class="row">
			<div class="span12">
				<div class="admin-header admin-header-central">
					<h1><?= $page_title ?></h1>
					<p><?= $page_desc ?></p>
				</div>

				<?php if (isset($page_notice)): ?>
					<div class="alert alert-<?= $page_notice['type'] ?>">
						<?= $page_notice['message'] ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<div class="row">
			<div class="span3">
				<ul class="nav nav-tabs nav-stacked">
					<?= $page_menu ?>
				</ul>
			</div>

			<div class="span9">