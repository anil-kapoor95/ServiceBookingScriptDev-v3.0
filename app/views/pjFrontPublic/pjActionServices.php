<?php
$option_arr = $tpl['option_arr']; 
$STORE = @$_SESSION[$controller->defaultStore];
$services = isset($STORE['service_id']) ? $STORE['service_id'] : array();
$category_arr = isset($tpl['category_arr']) ? $tpl['category_arr'] : array();
$extra_arr = isset($tpl['extra_arr']) ? $tpl['extra_arr'] : array();
$pjSbsStep = 1;
include_once dirname(__FILE__) . '/elements/steps.php';
include_once dirname(__FILE__) . '/elements/service_icon.php';

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
?>
<div class="pjSbs-services pjSbs-services-sticky">
	<form id="pjSbsServiceForm_<?php echo $_GET['index']?>" action="" method="post" data-has-extras="<?php echo !empty($extra_arr) ? 1 : 0;?>">
		<div class="pjSbs-services-head">
			<div class="pjSbs-services-title"><?php __('front_select_services');?></div><!-- /.pjSbs-services-title -->
		</div><!-- /.pjSbs-services-head -->

		<?php
		if(!empty($tpl['arr']))
		{
			// category filter (only useful when there is more than one category)
			if(count($category_arr) > 1)
			{
				?>
				<div class="pjSbs-categories-wrap">
				<button type="button" class="pjSbs-cat-nav pjSbs-cat-prev" aria-label="Previous" tabindex="-1"><span aria-hidden="true">&#8249;</span></button>
				<div class="pjSbs-categories">
					<button type="button" class="btn btn-primary pjSbs-category active" data-category="0"><?php __('front_all_categories');?></button>
					<?php
					foreach($category_arr as $category)
					{
						?><button type="button" class="btn btn-default pjSbs-category" data-category="<?php echo (int) $category['id'];?>"><?php echo pjSanitize::html($category['title']);?></button><?php
					}
					?>
				</div><!-- /.pjSbs-categories -->
				<button type="button" class="pjSbs-cat-nav pjSbs-cat-next" aria-label="Next" tabindex="-1"><span aria-hidden="true">&#8250;</span></button>
				</div><!-- /.pjSbs-categories-wrap -->
				<?php
			}
			?>
			<div class="pjSbs-services-body">
				<?php
				$subtotal = 0;
				$total_duration = 0;
				foreach($tpl['arr'] as $k => $v)
				{ 
					$duration_text = pjSbsDurationText($v['duration']);
					if(array_key_exists($v['id'], $services))
					{
						$subtotal += $v['price'];
						$total_duration += (int) $v['duration'];
					}
					?>
					<label class="pjSbs-service<?php echo array_key_exists($v['id'], $services) ? ' active' : NULL;?>" data-category="<?php echo (int) $v['category_id'];?>">
						<i class="pjSbs-ico-check"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><?php echo pjSbsServiceIcon($v['title'], isset($v['description']) ? $v['description'] : ''); ?></svg></i>
		
						<span class="pjSbs-service-title"><?php echo pjSanitize::html($v['title']);?></span>
		
						<span class="pjSbs-service-desc"><?php echo !empty($v['description']) ? nl2br(pjSanitize::html($v['description'])) : '';?></span>
		
						<span class="pjSbs-service-utilities">
							<em><i class="glyphicon glyphicon-time"></i> <strong><?php __('front_duration');?>:</strong> <?php echo $duration_text;?></em>
		
							<em><i class="glyphicon glyphicon-tag"></i> <strong><?php __('front_price');?>:</strong> <?php echo pjUtil::formatCurrencySign($v['price'], $option_arr['o_currency']);?></em>
						</span>
		
						<input type="checkbox" name="service_id[<?php echo $v['id'];?>]" value="<?php echo $v['duration'];?>"<?php echo array_key_exists($v['id'], $services) ? ' checked="checked"' : NULL;?> class="pjSbs-hidden">
					</label>
					<?php
				} 
				
				?>
			</div><!-- /.pjSbs-services-body -->
	
			<?php
			$duration = '&nbsp;'; 
			$price = '&nbsp;'; 
			if(!empty($services))
			{
				$duration = pjSbsDurationText($total_duration);
				$price = pjUtil::formatCurrencySign(number_format($subtotal, 2), $option_arr['o_currency']);
				?>
				<div class="pjSbs-services-footer pjSbs-footer-sticky">
					<div id="pjSbsServicesSelected_<?php echo $_GET['index'];?>" class="pjSbs-services-footer-title"><?php echo count($services);?> <?php count($services) != 1 ? __('front_services_selected') : __('front_service_selected');?></div><!-- /.pjSbs-services-footer-title -->
		
					<p><?php __('front_duration');?>: <span id="pjSbsDuration_<?php echo $_GET['index'];?>"><?php echo $duration;?></span></p>
		
					<p><?php __('front_price');?>: <span id="pjSbsPrice_<?php echo $_GET['index'];?>"><?php echo $price;?></span></p>
					
					<input type="hidden" name="duration" value="<?php echo $total_duration;?>">
					<input type="submit" class="btn btn-primary" value="<?php !empty($extra_arr) ? __('front_btn_choose_extras') : __('front_btn_select_date_time');?>">
				</div><!-- /.pjSbs-services-footer -->
					
				<?php
			}
		}else{
			?>
			<div class="pjSbs-services-body">
				<div class="pjSbsb-services-message"><?php __('front_no_services_found');?></div>
			</div><!-- /.pjSbs-services-body -->
			<?php
		}
		?>
	</form>
</div><!-- /.pjSbs-services -->
