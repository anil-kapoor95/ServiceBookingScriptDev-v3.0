<?php
/* Category (required) and available extras of a service; included by pjActionCreate / pjActionUpdate */
$svc_category_id = isset($tpl['arr']['category_id']) ? (int) $tpl['arr']['category_id'] : 0;
$svc_extra_ids = isset($tpl['service_extra_id_arr']) ? $tpl['service_extra_id_arr'] : array();
$svc_filter = __('filter', true, false);
$svc_ph = __('lblSelectExtras', true);
if ($svc_ph === '' || $svc_ph === NULL || $svc_ph === 'lblSelectExtras') { $svc_ph = 'Select extras'; }
?>
<p>
	<label class="title"><?php __('lblCategory'); ?></label>
	<span class="inline_block">
		<select name="category_id" id="category_id" class="pj-form-field w300 required" data-msg-required="<?php __('pj_field_required');?>">
			<option value="">-- <?php __('lblChoose'); ?> --</option>
			<?php
			foreach ($tpl['category_arr'] as $c)
			{
				?><option value="<?php echo $c['id']; ?>"<?php echo (int) $c['id'] === $svc_category_id ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($c['title']); ?><?php echo $c['status'] == 'F' ? ' (' . pjSanitize::html($svc_filter['inactive']) . ')' : NULL; ?></option><?php
			}
			?>
		</select>
		<?php if (empty($tpl['category_arr'])) : ?>
		<a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminCategories&amp;action=pjActionCreate"><?php __('lblNoCategoriesYet'); ?></a>
		<?php endif; ?>
	</span>
</p>
<style type="text/css">
p.pj-ms-row { overflow: visible; position: relative; z-index: 5; }
.pj-ms { position: relative; display: inline-block; width: 300px; max-width: 100%; text-align: left; }
.pj-ms-toggle { display: block; position: relative; width: 100%; box-sizing: border-box; height: 34px; padding: 0 56px 0 10px; border: 1px solid #b8b8b8; border-radius: 3px; background: #fff; color: #333; font: inherit; line-height: 32px; text-align: left; cursor: pointer; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
.pj-ms-toggle:focus, .pj-ms.pj-ms-open .pj-ms-toggle { border-color: #6699cc; outline: 0; }
.pj-ms-text.pj-ms-placeholder { color: #888; }
.pj-ms-count { position: absolute; right: 26px; top: 7px; min-width: 18px; height: 18px; padding: 0 5px; box-sizing: border-box; border-radius: 9px; background: #4a7ab3; color: #fff; font-size: 11px; line-height: 18px; text-align: center; }
.pj-ms-caret { position: absolute; right: 10px; top: 14px; width: 0; height: 0; border-left: 5px solid transparent; border-right: 5px solid transparent; border-top: 5px solid #666; }
.pj-ms-panel { display: none; position: absolute; left: 0; top: 36px; z-index: 60; width: 100%; min-width: 280px; box-sizing: border-box; max-height: 240px; overflow-y: auto; padding: 6px 10px; border: 1px solid #b8b8b8; border-radius: 3px; background: #fff; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18); }
.pj-ms.pj-ms-open .pj-ms-panel { display: block; }
.pj-ms-panel label { display: block; margin: 0; padding: 5px 0; font-weight: normal; cursor: pointer; line-height: 1.3; }
.pj-ms-panel label input { margin-right: 6px; vertical-align: middle; }
.pj-ms-hint { display: block; padding: 2px 0 6px; margin-bottom: 4px; border-bottom: 1px solid #eee; color: #777; font-size: 12px; }
</style>
<p class="pj-ms-row">
	<label class="title"><?php __('lblExtras'); ?></label>
	<span class="inline_block">
		<?php
		if (empty($tpl['extra_arr']))
		{
			?><a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminExtras&amp;action=pjActionCreate"><?php __('lblNoExtrasYet'); ?></a><?php
		} else {
			?>
			<span class="pj-ms" id="pjExtrasDd">
				<button type="button" class="pj-ms-toggle" aria-haspopup="true" aria-expanded="false">
					<span class="pj-ms-text pj-ms-placeholder" data-placeholder="<?php echo pjSanitize::html($svc_ph); ?>"><?php echo pjSanitize::html($svc_ph); ?></span>
					<span class="pj-ms-count" style="display: none;"></span>
					<span class="pj-ms-caret"></span>
				</button>
				<span class="pj-ms-panel">
					<span class="pj-ms-hint"><?php __('lblExtrasHint'); ?></span>
					<?php
					foreach ($tpl['extra_arr'] as $e)
					{
						$e_meta = array(pjUtil::formatCurrencySign($e['price'], $tpl['option_arr']['o_currency']));
						if ((int) $e['duration'] > 0)
						{
							$e_meta[] = (int) $e['duration'] . ' ' . __('lblMinutes', true);
						}
						?>
						<label>
							<input type="checkbox" name="extra_id[<?php echo $e['id']; ?>]" value="<?php echo $e['id']; ?>" data-title="<?php echo pjSanitize::html($e['title']); ?>"<?php echo in_array((int) $e['id'], $svc_extra_ids) ? ' checked="checked"' : NULL; ?> />
							<?php echo pjSanitize::html($e['title']); ?> <small>(<?php echo join(' | ', $e_meta); ?>)<?php echo $e['status'] == 'F' ? ' - ' . pjSanitize::html($svc_filter['inactive']) : NULL; ?></small>
						</label>
						<?php
					}
					?>
				</span>
			</span>
			<?php
		}
		?>
	</span>
</p>
