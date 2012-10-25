<div class="well well-small">
	<h1><?= $this->lang->line('site_information') ?></h1>

	<table class="table">
		<colgroup>
			<col width="25%" />
			<col width="25%" />
			<col width="25%" />
			<col width="25%" />
		</colgroup>

		<tbody>
			<tr>
				<td><?= $this->lang->line('site_id') ?></td>
				<td>SITE<?= $site_info->site_id ?></td>

				<td><?= $this->lang->line('total_widgets') ?></td>
				<td><?= $site_info->widget_count ?></td>
			</tr>

			<tr>
				<td><?= $this->lang->line('site_tables') ?></td>
				<td><?= $site_info->tables ?></td>

				<td><?= $this->lang->line('total_pages') ?></td>
				<td><?= $site_info->pages_count ?></td>
			</tr>

			<tr>
				<td><?= $this->lang->line('db_size') ?></td>
				<td><?= $site_info->db_size ?></td>

				<td><?= $this->lang->line('stylesheets') ?></td>
				<td><?= $site_info->stylesheets ?></td>
			</tr>

			<tr>
				<td><?= $this->lang->line('total_users') ?></td>
				<td><?= $site_info->user_count ?></td>

				<td><?= $this->lang->line('js_files') ?></td>
				<td><?= $site_info->scripts ?></td>
			</tr>
		</tbody>
	</table>
</div>

<div class="well well-small">
	<h1><?= $this->lang->line('site_stats') ?></h1>

	<?php if ($site_stats != NULL): ?>
		<hr />
		<div class="row-fluid">
			<div class="span5">
				<h5><?= $this->lang->line('top_10_pages') ?></h5>

				<?php if (count($site_stats->top_pages) > 0): ?>
					<ol class="ol-padded">
						<?php foreach ($site_stats->top_pages as $page): ?>
							<li>
								<a href="<?= base_url(expr($page->page_url)) ?>"><?= $page->page_title ?></a>

								<?php if ($page->access_count != 1): ?>
									(<?= sprintf($this->lang->line('n_hits'), $page->access_count) ?>)
								<?php else: ?>
									(<?= sprintf($this->lang->line('n_hit'), $page->access_count) ?>)
								<?php endif ?>
							</li>
						<?php endforeach ?>
					</ol>
				<?php else: ?>
					<div class="alert alert-well">
						<?= $this->lang->line('no_top_pages') ?>
					</div>
				<?php endif ?>
			</div>

			<div class="span7">
				<?= form_dropdown('year', $years, date('Y'), 'class="input-small pull-right"') ?>
				<h5><?= $this->lang->line('visitors') ?></h5>

				<table id="visitor-stats" width="100%">
					<colgroup>
						<col width="30" />
						<col />
						<col width="5" />
					</colgroup>

					<tbody>
						<?php foreach ($months as $index => $month): ?>
							<tr>
								<td><?= $this->lang->line('cal_'.$month) ?></td>

								<td>
									<div class="progress progress-striped progress-condensed">
										<div id="graph-<?= $index ?>" class="bar"></div>
									</div>
								</td>

								<td id="count-<?= $index ?>">0</td>
							</li>
						<?php endforeach ?>
					</tbody>
				</table>
			</div>
		</div>
	<?php else: ?>
		<div class="alert alert-well">
			<?= $this->lang->line('stats_not_available') ?>
		</div>
	<?php endif ?>
</div>

<script type="text/javascript">
	// Trigger visitor stats on startup
	$(function() {
		showVisitors('<?= $site_stats->visitors ?>');
	});

	// Show visitor graph
	function showVisitors(data) {
		var data = data.split('|');
		var max = data.max();

		// Show the graph and counts
		if (max > 0) {
			for (idx in data) {
				$('#graph-' + idx).width((data[idx] / max * 100) + '%');
				$('#count-' + idx).text(data[idx]);
			}
		} else {
			for (idx in data) {
				$('#graph-' + idx).width('0%');
				$('#count-' + idx).text('0');
			}
		}
	}

	// Update stats using AJAX requests
	$('[name=year]').change(function() {
		$('#visitor-stats').animate({ opacity: 0.5 });

		$.get('<?= base_url('admin') ?>' + '/stats/' + $(this).val(), function(data) {
			$('#visitor-stats').animate({ opacity: 1 }, function() {
				showVisitors(data);
			});
		});
	});
</script>