<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	if (isset($_GET['err']))
	{
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}

	pjUtil::printNotice(__('infoInstallCodeTitle', true), __('infoInstallCodeDesc', true), false, false)
	?>
	<form action="" method="get" class="pj-form form">
		<p>
			<textarea class="pj-form-field textarea_install" id="install_code" style="overflow: auto; height:100px; width: 720px;">&lt;link href="<?php echo PJ_INSTALL_URL.PJ_FRAMEWORK_LIBS_PATH . 'pj/css/'; ?>pj.bootstrap.min.css" type="text/css" rel="stylesheet" /&gt;
&lt;link href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFrontEnd&action=pjActionLoadCss" type="text/css" rel="stylesheet" /&gt;
&lt;script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFrontEnd&action=pjActionLoad"&gt;&lt;/script&gt;</textarea>
		</p>
	</form>

	<?php
	$option_arr = $tpl['option_arr'];
	$theme11_colors = array(
		'o_theme11_header' => array('label' => 'Header / accent colour', 'default' => '#8ba5a2'),
		'o_theme11_button' => array('label' => 'Buttons', 'default' => '#86a1a8'),
		'o_theme11_card'   => array('label' => 'Card background', 'default' => '#dbd8d3'),
		'o_theme11_page'   => array('label' => 'Page background', 'default' => '#ece9e2')
	);
	?>
	<p>&nbsp;</p>
	<form id="frmTheme11Colors" action="?controller=pjAdminOptions&action=pjActionUpdate" method="post" class="pj-form form">
		<input type="hidden" name="options_update" value="1" />
		<input type="hidden" name="next_action" value="pjActionInstall" />

		<p><strong>Theme 11 colours</strong></p>

		<table class="pj-table">
			<?php foreach ($theme11_colors as $key => $info) {
				$value = isset($option_arr[$key]) && !empty($option_arr[$key]) ? $option_arr[$key] : $info['default'];
				?>
				<tr>
					<td><?php echo pjSanitize::html($info['label']); ?></td>
					<td>
						<input type="color" value="<?php echo pjSanitize::html($value); ?>" onchange="document.getElementById('<?php echo $key; ?>_text').value=this.value;" />
						<input type="text" id="<?php echo $key; ?>_text" name="value-string-<?php echo $key; ?>" value="<?php echo pjSanitize::html($value); ?>" class="pj-form-field" style="width:100px;" onchange="this.previousElementSibling.value=this.value;" />
					</td>
				</tr>
				<?php
			} ?>
		</table>
		<p>
			<input type="submit" value="Save colours" class="pj-button" />
		</p>
	</form>
	<?php
}
?>
