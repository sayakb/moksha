	<script type="text/javascript">
		$(initPage);

		<?php if (isset($page_notice)): ?>
			setTabState('<?= $page_notice['type'] ?>');
		<?php endif ?>
	</script>

	<?php if ($page_dir != 'sites/'): ?>
		<div class="row">
			<div class="span12">
				<footer><?= $page_copyright ?></footer>
			</div>
		</div>
	<?php endif ?>
</body>
</html>