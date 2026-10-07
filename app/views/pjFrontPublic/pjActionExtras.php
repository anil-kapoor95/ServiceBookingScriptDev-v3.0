<?php
$option_arr = $tpl['option_arr'];
$STORE = @$_SESSION[$controller->defaultStore];
$services = isset($STORE['service_id']) ? $STORE['service_id'] : array();
$selected_extras = isset($STORE['extra_id']) ? $STORE['extra_id'] : array();
$service_arr = isset($tpl['service_arr']) ? $tpl['service_arr'] : array();
$extra_arr = isset($tpl['extra_arr']) ? $tpl['extra_arr'] : array();
$pjSbsStep = 'extras';
include_once dirname(__FILE__) . '/elements/steps.php';

/* "1 hour 30 minutes" text for a number of minutes */
if (!function_exists('pjSbsDurationText'))
{
	function pjSbsDurationText($minutes)
	{
		$temp_arr = pjUtil::convertToHoursMins((int) $minutes);
		$duration_arr = array();
		if((int) $temp_arr['hours'] > 0)
		{
			$duration_arr[] = $temp_arr['hours']. ' ' . ($temp_arr['hours'] != 1 ? __('front_hours', true) : __('front_hour', true));
		}
		if((int) $temp_arr['minutes'] > 0)
		{
			$duration_arr[] = $temp_arr['minutes'] . ' '. ($temp_arr['minutes'] != 1 ? __('front_minutes', true) : __('front_minute', true));
		}
		return join(" ", $duration_arr);
	}
}

// totals of the selected services and extras
$subtotal = 0;
$total_duration = 0;
foreach($service_arr as $v)
{
	$subtotal += $v['price'];
	$total_duration += (int) $v['duration'];
}
$extras_count = 0;
foreach($extra_arr as $v)
{
	if(array_key_exists($v['id'], $selected_extras))
	{
		$subtotal += $v['price'];
		$total_duration += (int) $v['duration'];
		$extras_count++;
	}
}
?>
<div class="pjSbs-services pjSbs-services-sticky pjSbs-extras-step">
	<form id="pjSbsExtrasForm_<?php echo $_GET['index']?>" action="" method="post">
		<div class="pjSbs-services-head">
			<div class="pjSbs-services-title"><?php __('front_select_extras');?></div><!-- /.pjSbs-services-title -->

			<a href="#" class="pjSbs-btn-back pjSbsBackToServices"><span class="glyphicon glyphicon-share-alt"></span></a>
		</div><!-- /.pjSbs-services-head -->

		<div class="pjSbs-services-body">
			<?php
			foreach($service_arr as $v)
			{
				?><input type="hidden" name="service_id[<?php echo (int) $v['id'];?>]" value="<?php echo (int) $v['duration'];?>"><?php
			}
			foreach($extra_arr as $v)
			{
				$is_selected = array_key_exists($v['id'], $selected_extras);
				$duration_text = (int) $v['duration'] > 0 ? pjSbsDurationText($v['duration']) : '';
				?>
				<label class="pjSbs-service pjSbs-extra<?php echo $is_selected ? ' active' : NULL;?>">
					<i class="pjSbs-ico-check"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M12 5v14"/><path d="M5 12h14"/></svg></i>

					<span class="pjSbs-service-title"><?php echo pjSanitize::html($v['title']);?></span>

					<?php if(!empty($v['description'])) { ?>
					<span class="pjSbs-service-desc"><?php echo nl2br(pjSanitize::html($v['description']));?></span>
					<?php } ?>

					<span class="pjSbs-service-utilities">
						<?php if($duration_text != '') { ?>
						<em><i class="glyphicon glyphicon-time"></i> <strong><?php __('front_duration');?>:</strong> <?php echo $duration_text;?></em>
						<?php } ?>

						<em><i class="glyphicon glyphicon-tag"></i> <strong><?php __('front_price');?>:</strong> + <?php echo pjUtil::formatCurrencySign($v['price'], $option_arr['o_currency']);?></em>
					</span>

					<input type="checkbox" name="extra_id[<?php echo $v['id'];?>]" value="<?php echo (int) $v['duration'];?>"<?php echo $is_selected ? ' checked="checked"' : NULL;?> class="pjSbs-hidden">
				</label>
				<?php
			}
			?>
		</div><!-- /.pjSbs-services-body -->

		<div class="pjSbs-services-footer pjSbs-footer-sticky">
			<div class="pjSbs-services-footer-title"><?php echo count($service_arr);?> <?php count($service_arr) != 1 ? __('front_services_selected') : __('front_service_selected');?><?php if($extras_count > 0) { ?> + <?php echo $extras_count;?> <?php $extras_count != 1 ? __('front_extras_selected') : __('front_extra_selected'); } ?></div><!-- /.pjSbs-services-footer-title -->

			<p><?php __('front_duration');?>: <span><?php echo pjSbsDurationText($total_duration);?></span></p>

			<p><?php __('front_price');?>: <span><?php echo pjUtil::formatCurrencySign(number_format($subtotal, 2), $option_arr['o_currency']);?></span></p>

			<input type="hidden" name="duration" value="<?php echo $total_duration;?>">
			<input type="submit" class="btn btn-primary" value="<?php __('front_btn_select_date_time');?>">
		</div><!-- /.pjSbs-services-footer -->
	</form>
</div><!-- /.pjSbs-services -->
