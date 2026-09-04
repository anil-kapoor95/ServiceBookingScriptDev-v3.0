<?php
$option_arr = $tpl['option_arr']; 
$STORE = @$_SESSION[$controller->defaultStore];
$services = isset($STORE['service_id']) ? $STORE['service_id'] : array();
?>
<div class="pjSbs-services">
	<form id="pjSbsServiceForm_<?php echo $_GET['index']?>" action="" method="post">
		<div class="pjSbs-services-head">
			<div class="pjSbs-services-title"><?php __('front_select_services');?></div><!-- /.pjSbs-services-title -->
		</div><!-- /.pjSbs-services-head -->

		<?php
		if(!empty($tpl['arr']))
		{ 
			?>
			<div class="pjSbs-services-body">
				<?php
				
				$subtotal = 0;
				$total_duration = 0;
				foreach($tpl['arr'] as $k => $v)
				{ 
					$temp_arr = pjUtil::convertToHoursMins((int) $v['duration']);
					$duration_arr = array();
					if((int) $temp_arr['hours'] > 0)
					{
						$duration_arr[] = $temp_arr['hours']. ' ' . ($temp_arr['hours'] != 1 ? __('front_hours', true) : __('front_hour', true));
					}
					if((int) $temp_arr['minutes'] > 0)
					{
						$duration_arr[] = $temp_arr['minutes'] . ' '. ($temp_arr['minutes'] != 1 ? __('front_minutes', true) : __('front_minute', true));
					} 
					if(array_key_exists($v['id'], $services))
					{
						$subtotal += $v['price'];
						$total_duration += (int) $v['duration'];
					}
					?>
					<label class="pjSbs-service<?php echo array_key_exists($v['id'], $services) ? ' active' : NULL;?><?php echo !empty($services) && $k+1 == count($tpl['arr']) ? ' pjSbs-service-last-child' : '';?>">
						<i class="pjSbs-ico-check"></i>
		
						<span class="pjSbs-service-title"><?php echo pjSanitize::html($v['title']);?></span>
		
						<span class="pjSbs-service-desc"><?php echo !empty($v['description']) ? nl2br(pjSanitize::html($v['description'])) : '';?></span>
		
						<span class="pjSbs-service-utilities">
							<em><i class="glyphicon glyphicon-time"></i> <strong><?php __('front_duration');?>:</strong> <?php echo join(" ", $duration_arr);?></em>
		
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
				$temp_arr = pjUtil::convertToHoursMins($total_duration);
				$duration_arr = array();
				if((int) $temp_arr['hours'] > 0)
				{
					$duration_arr[] = $temp_arr['hours']. ' ' . ($temp_arr['hours'] != 1 ? __('front_hours', true) : __('front_hour', true));
				}
				if((int) $temp_arr['minutes'] > 0)
				{
					$duration_arr[] = $temp_arr['minutes'] . ' '. ($temp_arr['minutes'] != 1 ? __('front_minutes', true) : __('front_minute', true));
				}
				$duration = join(" ", $duration_arr);
				$price = pjUtil::formatCurrencySign(number_format($subtotal, 2), $option_arr['o_currency']);
				?>
				<div class="pjSbs-services-footer">
					<div id="pjSbsServicesSelected_<?php echo $_GET['index'];?>" class="pjSbs-services-footer-title"><?php echo count($services);?> <?php count($services) != 1 ? __('front_services_selected') : __('front_service_selected');?></div><!-- /.pjSbs-services-footer-title -->
		
					<p><?php __('front_duration');?>: <span id="pjSbsDuration_<?php echo $_GET['index'];?>"><?php echo $duration;?></span></p>
		
					<p><?php __('front_price');?>: <span id="pjSbsPrice_<?php echo $_GET['index'];?>"><?php echo $price;?></span></p>
					
					<input type="hidden" name="duration" value="<?php echo $total_duration;?>">
					<input type="submit" class="btn btn-primary" value="<?php __('front_btn_select_date_time');?>">
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