<div class="well well-small">
	<h1><?= $this->lang->line('site_information') ?></h1>

	<div class="row">
		<div class="span4">
			<dl class="dl-horizontal dl-system">
				<dt><?= $this->lang->line('site_id') ?></dt>
				<dd>SITE<?= $site_info->site_id ?></dd>

				<dt><?= $this->lang->line('site_tables') ?></dt>
				<dd><?= $site_info->tables ?></dd>

				<dt><?= $this->lang->line('db_size') ?></dt>
				<dd><?= $site_info->db_size ?></dd>

				<dt><?= $this->lang->line('total_users') ?></dt>
				<dd><?= $site_info->user_count ?></dd>
			</dl>
		</div>

		<div class="span4">
			<dl class="dl-horizontal dl-system">
				<dt><?= $this->lang->line('total_widgets') ?></dt>
				<dd><?= $site_info->widget_count ?></dd>

				<dt><?= $this->lang->line('total_pages') ?></dt>
				<dd><?= $site_info->pages_count ?></dd>

				<dt><?= $this->lang->line('stylesheets') ?></dt>
				<dd><?= $site_info->stylesheets ?></dd>

				<dt><?= $this->lang->line('js_files') ?></dt>
				<dd><?= $site_info->scripts ?></dd>
			</dl>
		</div>
	</div>
</div>

<!--$lang['site_id']				= 'Site unique ID';
$lang['site_tables']			= 'Site tables';
$lang['db_size']				= 'Database size';
$lang['total_users']			= 'Total users';
$lang['total_widgets']			= 'Total widgets';
$lang['total_pages']			= 'Total pages';
$lang['stylesheets']			= 'Style sheets';-->

		<!--$info->site_id		= $this->bootstrap->site_id;
		$info->tables		= count($this->config->item('schema'));
		$info->db_size		= $this->fetch_size();
		$info->user_count	= $this->fetch_count('users') - 1;
		$info->widget_count	= $this->fetch_count('widgets');
		$info->pages_count	= $this->fetch_count('pages');
		$info->stylesheets	= $this->fetch_count('files', array('file_type' => 'css'));
		$info->scripts		= $this->fetch_count('files', array('file_type' => 'js'));-->