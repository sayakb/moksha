<!DOCTYPE html>

<html>
<head>
	<title>404: Page not found</title>
	<link href="<?= base_url('assets/css/stylesheet.css') ?>" rel="stylesheet" />
</head>
<body class="page">
	<div class="container">
		<div class="alert alert-error">
			<strong><?php echo $heading; ?></strong>
			<br />

			<p><?php echo $message; ?></p>
		</div>
	</div>
</body>
</html>