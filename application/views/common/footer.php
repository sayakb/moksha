	<script type="text/javascript">
		$(initPage);

		<?php if (isset($page_notice)): ?>
			setTabState('<?= $page_notice['type'] ?>');
		<?php endif ?>
	</script>

	<div class="row">
		<div class="span12">
			<footer><?= $page_copyright ?></footer>
		</div>
	</div>
</body>
</html>