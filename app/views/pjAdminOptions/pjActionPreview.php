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
	
	pjUtil::printNotice(__('infoPreviewInstallTitle', true), __('infoPreviewInstallDesc', true), false, false)
	?>
	<div class="pj-loader-outer">
		<fieldset class="fieldset white">
			<legend><?php __('lblChooseTheme'); ?></legend>
			<div class="theme-holder">
				<?php include PJ_VIEWS_PATH . 'pjAdminOptions/elements/theme.php'; ?>
			</div>
		</fieldset>
		<fieldset class="fieldset white">
			<legend><?php __('lblInstallCode'); ?></legend>
			<br/>
			<form action="" method="get" class="pj-form form">
				<p>
					<textarea class="pj-form-field textarea_install" id="install_code" style="overflow: auto; height:100px; width: 695px;">&lt;link href="<?php echo PJ_INSTALL_URL.PJ_FRAMEWORK_LIBS_PATH . 'pj/css/'; ?>pj.bootstrap.min.css" type="text/css" rel="stylesheet" /&gt;
&lt;link href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFrontEnd&action=pjActionLoadCss" type="text/css" rel="stylesheet" /&gt;
&lt;script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFrontEnd&action=pjActionLoad"&gt;&lt;/script&gt;</textarea>
				</p>
			</form>
		</fieldset>

		<?php
		$option_arr = $tpl['option_arr'];
		$is_theme11_active = isset($option_arr['o_theme']) && $option_arr['o_theme'] === 'theme11';

		if ($is_theme11_active)
		{
			$theme11_color_groups = array(
				'General' => array(
					'o_theme11_header' => array('label' => 'Header / accent colour', 'default' => '#e8836b'),
					'o_theme11_page'   => array('label' => 'Page background', 'default' => '#fbf0ea'),
					'o_theme11_card'   => array('label' => 'Card background', 'default' => '#f4dccf')
				),
				'Fonts' => array(
					'o_theme11_text_heading' => array('label' => 'Heading text colour', 'default' => '#4a2a22'),
					'o_theme11_text_body'    => array('label' => 'Body / description text colour', 'default' => '#7b5b51'),
					'o_theme11_text_accent'  => array('label' => 'Accent text colour (duration, price, links)', 'default' => '#ae3c22')
				),
				'Buttons' => array(
					'o_theme11_button'       => array('label' => 'Button colour', 'default' => '#c94e33'),
					'o_theme11_button_hover' => array('label' => 'Button hover colour', 'default' => '#b73f24')
				),
				'List (service cards, date & time)' => array(
					'o_theme11_list_active'   => array('label' => 'Active / selected colour', 'default' => '#dc775f'),
					'o_theme11_list_inactive' => array('label' => 'Inactive colour', 'default' => '#f0d3c6')
				)
			);
			$theme11_color_hints = array(
				'o_theme11_header'       => 'Step bar, icons, links, active elements',
				'o_theme11_page'         => 'Behind the whole booking widget',
				'o_theme11_card'         => 'Service cards and summary cards',
				'o_theme11_text_heading' => 'Service names and titles',
				'o_theme11_text_body'    => 'Descriptions and small labels',
				'o_theme11_text_accent'  => 'Duration, price and links',
				'o_theme11_button'       => 'Main buttons (white text)',
				'o_theme11_button_hover' => 'When the mouse is over a button',
				'o_theme11_list_active'  => 'Chosen service, date and time',
				'o_theme11_list_inactive'=> 'Unselected icon circles and step bars'
			);
			$theme11_text = array(
				't11_legend' => 'Theme 11 colours',
				't11_quick_palettes' => 'Quick palettes',
				't11_tab_general' => 'General',
				't11_tab_fonts' => 'Fonts',
				't11_tab_buttons' => 'Buttons',
				't11_tab_list' => 'List',
				't11_lbl_header' => 'Header / accent colour',
				't11_lbl_page' => 'Page background',
				't11_lbl_card' => 'Card background',
				't11_lbl_text_heading' => 'Heading text colour',
				't11_lbl_text_body' => 'Body / description text colour',
				't11_lbl_text_accent' => 'Accent text colour (duration, price, links)',
				't11_lbl_button' => 'Button colour',
				't11_lbl_button_hover' => 'Button hover colour',
				't11_lbl_list_active' => 'Active / selected colour',
				't11_lbl_list_inactive' => 'Inactive colour',
				't11_hint_header' => 'Step bar, icons, links, active elements',
				't11_hint_page' => 'Behind the whole booking widget',
				't11_hint_card' => 'Service cards and summary cards',
				't11_hint_text_heading' => 'Service names and titles',
				't11_hint_text_body' => 'Descriptions and small labels',
				't11_hint_text_accent' => 'Duration, price and links',
				't11_hint_button' => 'Main buttons (white text)',
				't11_hint_button_hover' => 'When the mouse is over a button',
				't11_hint_list_active' => 'Chosen service, date and time',
				't11_hint_list_inactive' => 'Unselected icon circles and step bars',
				't11_click_to_change' => 'click to change',
				't11_btn_save' => 'Save colours',
				't11_btn_reset' => 'Reset to default',
				't11_note' => 'Picking a quick palette or changing a colour only previews it here. Press “Save colours” to apply it to the booking widget. The readability badge shows text contrast (4.5 or higher is good).',
				't11_live_preview' => 'Live preview',
				't11_live_preview_badge' => 'updates as you edit',
				't11_read_good' => 'Good',
				't11_read_low' => 'Low',
				't11_read_poor' => 'Poor',
				't11_read_title' => 'Readability (WCAG): 4.5 or higher is good',
				't11_pal_sage' => 'Sage',
				't11_pal_ocean' => 'Ocean Breeze',
				't11_pal_lavender' => 'Lavender Dream',
				't11_pal_coral' => 'Sunset Coral (default)',
				't11_pal_forest' => 'Forest Emerald',
				't11_pal_gold' => 'Midnight Gold',
				't11_pal_rose' => 'Rose Blush',
				't11_pal_mint' => 'Aqua Mint',
			);
			/* Every label below comes from the script's own translation fields (Admin > Options > Notification / language labels,
			   keys t11_*; see app/config/updates/theme11_dynamic_panel_labels_*.sql). The English text above is only a fallback
			   used until that SQL has been run, so the panel never shows blank labels. */
			$t11 = function ($key) use ($theme11_text) {
				$v = __($key, true);
				return ($v !== null && $v !== false && $v !== '') ? $v : (isset($theme11_text[$key]) ? $theme11_text[$key] : $key);
			};
			$t11_pal_keys = array('t11_pal_coral','t11_pal_sage','t11_pal_ocean','t11_pal_lavender','t11_pal_forest','t11_pal_gold','t11_pal_rose','t11_pal_mint');
			$t11_pal_vals = array(
				array('#e8836b', '#fbf0ea', '#f4dccf', '#4a2a22', '#7b5b51', '#ae3c22', '#c94e33', '#b73f24', '#dc775f', '#f0d3c6'),
				array('#8ba5a2', '#ece9e2', '#dbd8d3', '#3f4543', '#8d9290', '#6f8d89', '#86a1a8', '#76929a', '#8ba5a2', '#d8d3c8'),
				array('#3d8fb5', '#eaf3f7', '#d3e4ec', '#17384a', '#4a6878', '#18698e', '#237da7', '#216d96', '#3d8fb5', '#c5dbe6'),
				array('#8b7ec8', '#f0edf8', '#e0daf0', '#2e2a4a', '#605b7d', '#6153ac', '#7868c1', '#6657b0', '#8b7ec8', '#d6cfe9'),
				array('#3f9a73', '#eaf4ee', '#d4e8dc', '#1b3a2c', '#4a6a58', '#22714c', '#308359', '#2a714e', '#3f9a73', '#c7dfd0'),
				array('#c9a24a', '#f4efe3', '#e6dcc3', '#2b2a33', '#656158', '#7e5801', '#2f3142', '#1f2130', '#b48d35', '#ddd2b6'),
				array('#d9809a', '#fbeff2', '#f2d9e0', '#4a2733', '#785964', '#a53c5e', '#ba5272', '#ab4364', '#d37a94', '#ecd0d9'),
				array('#2bb5a3', '#e9f6f4', '#cfeae6', '#12403b', '#426d68', '#007363', '#008474', '#007466', '#1ca694', '#bfe0db'),
			);
			$t11_palettes = array();
			foreach ($t11_pal_keys as $t11_n => $t11_k) { $t11_palettes[] = array($t11($t11_k), $t11_pal_vals[$t11_n]); }
			$t11_js_text = array(
				'good' => $t11('t11_read_good'), 'low' => $t11('t11_read_low'), 'poor' => $t11('t11_read_poor'), 'title' => $t11('t11_read_title')
			);
			$t11_tab_keys = array('t11_tab_general','t11_tab_fonts','t11_tab_buttons','t11_tab_list');
			$t11_w_dur = __('front_duration', true) ?: 'Duration';
			$t11_w_price = __('front_price', true) ?: 'Price';
			$t11_w_hours = __('front_hours', true) ?: 'hours';
			$t11_w_mins = __('front_minutes', true) ?: 'minutes';
			$t11_w_selected = __('front_service_selected', true) ?: 'service selected';
			$t11_w_btn = __('front_btn_select_date_time', true) ?: 'Select Date & Time';
			?>
			<fieldset class="fieldset white" id="pjT11Panel">
				<legend><?php echo pjSanitize::html($t11('t11_legend')); ?></legend>
				<style>
				#pjT11Panel .t11-grid{display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap;padding:14px 4px 6px}
				#pjT11Panel .t11-main{flex:1 1 360px;min-width:0}
				#pjT11Panel .t11-side{flex:0 0 290px;max-width:100%;position:sticky;top:12px}
				#pjT11Panel .t11-lab{font-size:11.5px;text-transform:uppercase;letter-spacing:.06em;color:#7a8088;font-weight:bold;margin:0 0 8px}
				#pjT11Panel .t11-pres{display:grid;grid-template-columns:repeat(auto-fill,minmax(104px,1fr));gap:9px;margin-bottom:20px}
				#pjT11Panel .t11-pt{border:2px solid #e2e5e7;border-radius:11px;padding:7px;background:#fff;cursor:pointer;text-align:left;font:inherit;display:block;width:100%}
				#pjT11Panel .t11-pt:hover{border-color:#b9c4cc}
				#pjT11Panel .t11-pt.on{border-color:#17375e;box-shadow:0 0 0 3px rgba(23,55,94,.12)}
				#pjT11Panel .t11-strip{display:flex;height:30px;border-radius:7px;overflow:hidden;border:1px solid rgba(0,0,0,.08)}
				#pjT11Panel .t11-strip i{flex:1;display:block}
				#pjT11Panel .t11-pt b{display:block;font-size:12px;margin-top:6px;color:#2b3036}
				#pjT11Panel .t11-tabs{display:flex;gap:4px;border-bottom:2px solid #e3e6e8;margin:0 0 14px}
				#pjT11Panel .t11-tabs button{border:0;background:none;padding:9px 14px;font:bold 13px Arial,sans-serif;color:#7a8088;cursor:pointer;border-bottom:3px solid transparent;margin-bottom:-2px}
				#pjT11Panel .t11-tabs button.on{color:#17375e;border-color:#17375e}
				#pjT11Panel .t11-tiles{display:none;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px}
				#pjT11Panel .t11-tiles.on{display:grid}
				#pjT11Panel .t11-tile{border:1px solid #e3e6e8;border-radius:12px;padding:10px;background:#fff}
				#pjT11Panel .t11-big{display:block;height:64px;border-radius:9px;border:1px solid rgba(0,0,0,.12);position:relative;cursor:pointer;margin-bottom:9px;overflow:hidden}
				#pjT11Panel .t11-big input{position:absolute;left:-10px;top:-10px;width:calc(100% + 20px);height:calc(100% + 20px);opacity:0;cursor:pointer;margin:0;padding:0;border:0}
				#pjT11Panel .t11-big span{position:absolute;right:6px;bottom:5px;background:rgba(255,255,255,.9);border-radius:5px;font-size:10px;font-weight:bold;padding:1px 6px;color:#333;pointer-events:none}
				#pjT11Panel .t11-tn{font-size:12.5px;font-weight:bold;margin-bottom:5px;line-height:1.3;color:#2b3036}
				#pjT11Panel .t11-row2{display:flex;gap:6px;align-items:center;flex-wrap:wrap}
				#pjT11Panel .t11-hex{flex:1 1 84px;min-width:0;width:auto !important;padding:7px 9px;border:1px solid #cfd4d8;border-radius:7px;font-family:Menlo,Consolas,monospace;font-size:13px;box-sizing:border-box;height:auto;line-height:1.3}
				#pjT11Panel .t11-hex.err{border-color:#d33;background:#fff5f5}
				#pjT11Panel .t11-bd{font-size:10.5px;font-weight:bold;border-radius:999px;padding:2px 8px;white-space:nowrap}
				#pjT11Panel .t11-bd.ok{background:#dff3e5;color:#1d6b38}
				#pjT11Panel .t11-bd.warn{background:#fff1d6;color:#8a5a00}
				#pjT11Panel .t11-bd.bad{background:#fde0de;color:#a3261d}
				#pjT11Panel .t11-hint{font-size:11.5px;color:#7a8088;margin-top:5px;line-height:1.4}
				#pjT11Panel .t11-actions{display:flex;gap:10px;align-items:center;margin-top:16px;flex-wrap:wrap}
				#pjT11Panel .t11-reset{background:#fff;color:#444;border:1px solid #c9cdd0;border-radius:4px;padding:6px 14px;font-size:13px;cursor:pointer}
				#pjT11Panel .t11-note{font-size:12px;color:#7a8088;margin-top:8px}
				#pjT11Panel .t11-lp{font-size:11px;color:#7a8088;margin:0 0 6px;display:flex;justify-content:space-between;align-items:center}
				#pjT11Panel .t11-lp span{background:#e8f4ea;color:#1d6b38;border-radius:999px;padding:1px 8px;font-weight:bold}
				/* live preview widget (mirrors the front-end theme 11 look) */
				#pjT11Panel .t11w{--header:#e8836b;--page:#fbf0ea;--card:#f4dccf;--th:#4a2a22;--tb:#7b5b51;--ta:#ae3c22;--btn:#c94e33;--btnh:#b73f24;--la:#dc775f;--li:#f0d3c6;background:var(--page);border-radius:16px;padding:14px;color:var(--th);font-family:Arial,Helvetica,sans-serif;overflow:hidden}
				#pjT11Panel .t11w *{box-sizing:border-box}
				#pjT11Panel .t11w svg.i{width:17px;height:17px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;display:block}
				#pjT11Panel .t11w .steps{display:flex;gap:6px;margin-bottom:10px}
				#pjT11Panel .t11w .st{flex:1;text-align:center}
				#pjT11Panel .t11w .st .bar{height:4px;border-radius:2px;background:var(--li);margin-bottom:7px}
				#pjT11Panel .t11w .st .chip{width:32px;height:32px;border-radius:50%;margin:0 auto 3px;background:#fff;color:#a7aba5;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(63,69,67,.08)}
				#pjT11Panel .t11w .st .l{font-size:9px;font-weight:bold;letter-spacing:.06em;text-transform:uppercase;color:#a7aba5}
				#pjT11Panel .t11w .st.done .bar,#pjT11Panel .t11w .st.cur .bar{background:var(--header)}
				#pjT11Panel .t11w .st.done .chip{color:var(--header)}
				#pjT11Panel .t11w .st.cur .chip{background:var(--header);color:#fff;box-shadow:0 6px 14px rgba(0,0,0,.18)}
				#pjT11Panel .t11w .st.done .l,#pjT11Panel .t11w .st.cur .l{color:var(--th)}
				#pjT11Panel .t11w .wt{text-align:center;font-weight:bold;font-size:15px;margin:2px 0 9px;color:var(--th)}
				#pjT11Panel .t11w .svc{position:relative;background:var(--card);border:1px solid var(--card);border-radius:13px;padding:10px 38px 10px 60px;margin-bottom:7px;min-height:56px}
				#pjT11Panel .t11w .svc.act{border-color:var(--la)}
				#pjT11Panel .t11w .svc .ic{position:absolute;left:11px;top:50%;margin-top:-18px;width:36px;height:36px;border-radius:50%;background:var(--li);color:var(--ta);display:flex;align-items:center;justify-content:center}
				#pjT11Panel .t11w .svc.act .ic{background:var(--la);color:#fff}
				#pjT11Panel .t11w .svc .t{font-weight:bold;font-size:13.5px;color:var(--th)}
				#pjT11Panel .t11w .svc .d{font-size:11.5px;color:var(--tb);margin:1px 0 2px}
				#pjT11Panel .t11w .svc .u{font-size:11px;color:var(--th)}
				#pjT11Panel .t11w .svc .u b{color:var(--ta);font-weight:normal}
				#pjT11Panel .t11w .svc.act:after{content:"";position:absolute;right:11px;top:50%;margin-top:-8px;width:16px;height:16px;border-radius:50%;background:var(--la) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='5 13 10 18 19 7'/%3E%3C/svg%3E") center/10px no-repeat}
				#pjT11Panel .t11w .bar2{background:var(--th);color:#fff;border-radius:0 0 14px 14px;padding:11px 14px;display:flex;justify-content:space-between;align-items:center;margin:8px -14px -14px;gap:8px}
				#pjT11Panel .t11w .bar2 .a{font-size:10px;text-transform:uppercase;letter-spacing:.04em;opacity:.8}
				#pjT11Panel .t11w .bar2 .b{font-size:11.5px;margin-top:2px}
				#pjT11Panel .t11w .pbtn{background:var(--btn);color:#fff;border-radius:999px;padding:9px 12px;font-weight:bold;font-size:11.5px;white-space:nowrap;cursor:default}
				#pjT11Panel .t11w .pbtn:hover{background:var(--btnh)}
				</style>
				<form id="frmTheme11Colors" action="?controller=pjAdminOptions&action=pjActionUpdate" method="post" class="pj-form form">
					<input type="hidden" name="options_update" value="1" />
					<input type="hidden" name="next_action" value="pjActionPreview" />
					<div class="t11-grid">
						<div class="t11-main">
							<p class="t11-lab"><?php echo pjSanitize::html($t11('t11_quick_palettes')); ?></p>
							<div class="t11-pres" id="t11Pres"></div>

							<div class="t11-tabs" id="t11Tabs">
								<?php $t11_i = 0; foreach ($theme11_color_groups as $group_label => $group_colors) { ?>
									<button type="button" data-t="<?php echo $t11_i; ?>" class="<?php echo $t11_i === 0 ? 'on' : ''; ?>"><?php echo pjSanitize::html($t11($t11_tab_keys[$t11_i])); ?></button>
									<?php $t11_i++; } ?>
							</div>
							<?php
							$t11_i = 0;
							foreach ($theme11_color_groups as $group_label => $group_colors) { ?>
								<div class="t11-tiles<?php echo $t11_i === 0 ? ' on' : ''; ?>" data-g="<?php echo $t11_i; ?>">
									<?php foreach ($group_colors as $key => $info) {
										$value = isset($option_arr[$key]) && !empty($option_arr[$key]) ? $option_arr[$key] : $info['default'];
										$picker = preg_match('/^#[0-9a-fA-F]{6}$/', $value) ? $value : $info['default'];
										$t11_suffix = substr($key, strlen('o_theme11_'));
										$hint = $t11('t11_hint_' . $t11_suffix);
										$t11_label = $t11('t11_lbl_' . $t11_suffix);
										?>
										<div class="t11-tile">
											<div class="t11-big" style="background:<?php echo pjSanitize::html($picker); ?>">
												<input type="color" class="t11-pick" data-key="<?php echo $key; ?>" value="<?php echo pjSanitize::html($picker); ?>" />
												<span><?php echo pjSanitize::html($t11('t11_click_to_change')); ?></span>
											</div>
											<div class="t11-tn"><?php echo pjSanitize::html($t11_label); ?></div>
											<div class="t11-row2">
												<input type="text" id="<?php echo $key; ?>_text" name="value-string-<?php echo $key; ?>" value="<?php echo pjSanitize::html($value); ?>" maxlength="7" class="pj-form-field t11-hex" data-key="<?php echo $key; ?>" data-default="<?php echo pjSanitize::html($info['default']); ?>" spellcheck="false" />
												<span class="t11-bd" data-badge="<?php echo $key; ?>"></span>
											</div>
											<div class="t11-hint"><?php echo pjSanitize::html($hint); ?></div>
										</div>
									<?php } ?>
								</div>
								<?php $t11_i++;
							} ?>

							<div class="t11-actions">
								<input type="submit" value="<?php echo pjSanitize::html($t11('t11_btn_save')); ?>" class="pj-button" />
								<button type="button" class="t11-reset" id="t11Reset"><?php echo pjSanitize::html($t11('t11_btn_reset')); ?></button>
							</div>
							<div class="t11-note"><?php echo pjSanitize::html($t11('t11_note')); ?></div>
						</div>

						<div class="t11-side">
							<div class="t11-lp"><?php echo pjSanitize::html($t11('t11_live_preview')); ?> <span><?php echo pjSanitize::html($t11('t11_live_preview_badge')); ?></span></div>
							<div class="t11w" id="t11Widget">
								<div class="steps">
									<div class="st done"><div class="bar"></div><div class="chip"><svg class="i" viewBox="0 0 24 24" aria-hidden="true"><rect width="8" height="4" x="8" y="2" rx="1" ry="1" /> <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" /> <path d="M12 11h4" /> <path d="M12 16h4" /> <path d="M8 11h.01" /> <path d="M8 16h.01" /></svg></div><div class="l"><?php echo pjSanitize::html(__('front_step_service', true) ?: 'Service'); ?></div></div>
									<div class="st cur"><div class="bar"></div><div class="chip"><svg class="i" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 2v3" /> <path d="M16 2v3" /> <rect x="3" y="3" width="18" height="18" rx="2" /> <path d="M3 9h18" /> <path d="m9 15 2 2 4-4" /></svg></div><div class="l"><?php echo pjSanitize::html(__('front_step_time', true) ?: 'Time'); ?></div></div>
									<div class="st"><div class="bar"></div><div class="chip"><svg class="i" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="5" /> <path d="M20 21a8 8 0 0 0-16 0" /></svg></div><div class="l"><?php echo pjSanitize::html(__('front_step_details', true) ?: 'Details'); ?></div></div>
									<div class="st"><div class="bar"></div><div class="chip"><svg class="i" viewBox="0 0 24 24" aria-hidden="true"><path d="M21.801 10A10 10 0 1 1 17 3.335" /> <path d="m9 11 3 3L22 4" /></svg></div><div class="l"><?php echo pjSanitize::html(__('front_step_confirm', true) ?: 'Confirm'); ?></div></div>
								</div>
								<div class="wt"><?php echo pjSanitize::html(__('front_select_services', true) ?: 'Select Service(s)'); ?></div>
								<div class="svc act"><div class="ic"><svg class="i" viewBox="0 0 24 24" aria-hidden="true"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z" /> <path d="M20 2v4" /> <path d="M22 4h-4" /> <circle cx="4" cy="20" r="2" /></svg></div><div class="t">Car Detailing</div><div class="d">Full interior &amp; exterior</div><div class="u"><?php echo pjSanitize::html($t11_w_dur); ?>: <b>3 <?php echo pjSanitize::html($t11_w_hours); ?></b> &nbsp; <?php echo pjSanitize::html($t11_w_price); ?>: <b>$48.00</b></div></div>
								<div class="svc"><div class="ic"><svg class="i" viewBox="0 0 24 24" aria-hidden="true"><circle cx="6" cy="6" r="3" /> <path d="M8.12 8.12 12 12" /> <path d="M20 4 8.12 15.88" /> <circle cx="6" cy="18" r="3" /> <path d="M14.8 14.8 20 20" /></svg></div><div class="t">Hair Cutting</div><div class="d">Cut &amp; style</div><div class="u"><?php echo pjSanitize::html($t11_w_dur); ?>: <b>45 <?php echo pjSanitize::html($t11_w_mins); ?></b> &nbsp; <?php echo pjSanitize::html($t11_w_price); ?>: <b>$25.00</b></div></div>
								<div class="bar2"><div><div class="a">1 <?php echo pjSanitize::html($t11_w_selected); ?></div><div class="b"><?php echo pjSanitize::html($t11_w_dur); ?>: 3 <?php echo pjSanitize::html($t11_w_hours); ?></div></div><span class="pbtn"><?php echo pjSanitize::html($t11_w_btn); ?></span></div>
							</div>
						</div>
					</div>
				</form>
				<script type="text/javascript">
				(function () {
					var root = document.getElementById('pjT11Panel');
					if (!root) { return; }
					var PALS = <?php echo json_encode($t11_palettes); ?>;
					var TXT = <?php echo json_encode($t11_js_text); ?>;
					var KEYS = ['o_theme11_header','o_theme11_page','o_theme11_card','o_theme11_text_heading','o_theme11_text_body','o_theme11_text_accent','o_theme11_button','o_theme11_button_hover','o_theme11_list_active','o_theme11_list_inactive'];
					var VARS = ['--header','--page','--card','--th','--tb','--ta','--btn','--btnh','--la','--li'];
					/* [foreground index (-1 = white), background index] */
					var PAIRS = {3:[3,1], 4:[4,2], 5:[5,2], 6:[-1,6], 8:[-1,8]};
					var widget = document.getElementById('t11Widget');
					var pres = document.getElementById('t11Pres');
					function $all(sel) { return Array.prototype.slice.call(root.querySelectorAll(sel)); }
					function hexInput(i) { return root.querySelector('.t11-hex[data-key="' + KEYS[i] + '"]'); }
					function pickInput(i) { return root.querySelector('.t11-pick[data-key="' + KEYS[i] + '"]'); }
					function norm(v) {
						v = (v || '').replace(/^\s+|\s+$/g, '').toLowerCase();
						if (v.charAt(0) !== '#') { v = '#' + v; }
						if (/^#[0-9a-f]{3}$/.test(v)) { v = '#' + v.charAt(1) + v.charAt(1) + v.charAt(2) + v.charAt(2) + v.charAt(3) + v.charAt(3); }
						return /^#[0-9a-f]{6}$/.test(v) ? v : null;
					}
					function current() { return KEYS.map(function (k, i) { var h = norm(hexInput(i).value); return h || '#000000'; }); }
					function lum(h) {
						var c = [1, 3, 5].map(function (n) { var v = parseInt(h.substr(n, 2), 16) / 255; return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4); });
						return 0.2126 * c[0] + 0.7152 * c[1] + 0.0722 * c[2];
					}
					function ratio(a, b) { var x = lum(a), y = lum(b); return (Math.max(x, y) + 0.05) / (Math.min(x, y) + 0.05); }
					function refresh() {
						var S = current();
						VARS.forEach(function (v, i) { widget.style.setProperty(v, S[i]); });
						$all('.t11-big').forEach(function (b) { var p = b.querySelector('.t11-pick'); var i = KEYS.indexOf(p.getAttribute('data-key')); b.style.background = S[i]; });
						Object.keys(PAIRS).forEach(function (i) {
							var p = PAIRS[i], fg = p[0] < 0 ? '#ffffff' : S[p[0]], r = ratio(fg, S[p[1]]);
							var cls = r >= 4.5 ? 'ok' : (r >= 3 ? 'warn' : 'bad'), lbl = r >= 4.5 ? TXT.good : (r >= 3 ? TXT.low : TXT.poor);
							var el = root.querySelector('[data-badge="' + KEYS[i] + '"]');
							if (el) { el.className = 't11-bd ' + cls; el.textContent = r.toFixed(1) + ' · ' + lbl; el.title = TXT.title; }
						});
						var key = S.join(',');
						$all('.t11-pt').forEach(function (b, n) { b.className = 't11-pt' + (PALS[n][1].join(',') === key ? ' on' : ''); });
					}
					function setAll(arr) {
						arr.forEach(function (h, i) { hexInput(i).value = h; hexInput(i).className = hexInput(i).className.replace(' err', ''); pickInput(i).value = h; });
						refresh();
					}
					/* quick palettes */
					pres.innerHTML = PALS.map(function (p, n) {
						return '<button type="button" class="t11-pt" data-p="' + n + '"><span class="t11-strip">' + [0, 1, 2, 3, 6].map(function (k) { return '<i style="background:' + p[1][k] + '"></i>'; }).join('') + '</span><b>' + p[0] + '</b></button>';
					}).join('');
					$all('.t11-pt').forEach(function (b) { b.onclick = function () { setAll(PALS[+b.getAttribute('data-p')][1]); }; });
					/* tabs */
					$all('#t11Tabs button').forEach(function (b) {
						b.onclick = function () {
							var t = b.getAttribute('data-t');
							$all('#t11Tabs button').forEach(function (x) { x.className = x === b ? 'on' : ''; });
							$all('.t11-tiles').forEach(function (g) { g.className = 't11-tiles' + (g.getAttribute('data-g') === t ? ' on' : ''); });
						};
					});
					/* colour pickers + hex fields */
					$all('.t11-pick').forEach(function (p) {
						p.oninput = function () { var i = KEYS.indexOf(p.getAttribute('data-key')); hexInput(i).value = p.value; hexInput(i).className = hexInput(i).className.replace(' err', ''); refresh(); };
					});
					$all('.t11-hex').forEach(function (h) {
						h.oninput = function () {
							var v = norm(h.value), i = KEYS.indexOf(h.getAttribute('data-key'));
							if (v) { h.className = h.className.replace(' err', ''); pickInput(i).value = v; refresh(); } else if (h.className.indexOf(' err') < 0) { h.className += ' err'; }
						};
						h.onblur = function () { var v = norm(h.value); if (v) { h.value = v; } };
					});
					document.getElementById('t11Reset').onclick = function () {
						setAll(KEYS.map(function (k, i) { return hexInput(i).getAttribute('data-default'); }));
					};
					/* never post an invalid colour */
					document.getElementById('frmTheme11Colors').onsubmit = function () {
						var bad = null;
						$all('.t11-hex').forEach(function (h) { var v = norm(h.value); if (v) { h.value = v; } else if (!bad) { bad = h; } });
						if (bad) { bad.className += bad.className.indexOf(' err') < 0 ? ' err' : ''; bad.focus(); return false; }
						return true;
					};
					refresh();
				})();
				</script>
			</fieldset>
			<?php
		}
		?>
	</div>
	<?php
}
?>