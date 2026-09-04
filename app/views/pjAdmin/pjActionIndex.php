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
}else{
	?>
	<div class="dashboard_header">
		<div class="item">
			<div class="stat bookings">
				<div class="info">
					<abbr><?php echo $tpl['cnt_bookings_today'];?></abbr>
					<label><?php (int)$tpl['cnt_bookings_today'] != 1 ? __('dash_bookings_made_today') : __('dash_booking_made_today');?></label>
				</div>
			</div>
		</div>
		<div class="item">
			<div class="stat bookings">
				<div class="info">
					<abbr><?php echo $tpl['cnt_services_today'];?></abbr>
					<label><?php (int)$tpl['cnt_services_today'] != 1 ? __('dash_services_completed_today') : __('dash_service_completed_today');?></label>
				</div>
			</div>
		</div>
		<div class="item">
			<div class="stat bookings">
				<div class="info">
					<abbr><?php echo $tpl['cnt_bookings'];?></abbr>
					<label><?php __('dash_total_bookings');?></label>
				</div>
			</div>
		</div>
	</div>
	
	<div class="dashboard_box">
		<div class="dashboard_top">
			<div class="dashboard_column_top"><?php __('dash_latest_bookings');?></div>
			<div class="dashboard_column_top"><?php __('dash_today_bookings');?> <span class="float_right r10">(<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionPrint" target="_blank"><?php __('dash_print')?></a>)</span></div>
			<div class="dashboard_column_top"><?php __('dash_quick_links');?></div>
		</div>
		<div class="dashboard_middle">
			<div class="dashboard_column">
				<div class="dashboard_list dashboard_latest_list">
					<?php
					if(count($tpl['latest_arr']) > 0)
					{
						foreach($tpl['latest_arr'] as $v)
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
							?>
							<div class="dashboard_row">
								<label><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate&amp;id=<?php echo $v['id']?>"><?php echo pjSanitize::html($v['c_name']);?></a></label>
								<label><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['start_dt'])) . ' ' . date($tpl['option_arr']['o_time_format'], strtotime($v['start_dt']));?></label>
								<label><?php echo join(" ", $duration_arr);?></label>
							</div>
							<?php
						}
					}else{
						?>
						<div class="dashboard_row">
							<label><span><?php __('dash_no_bookings_found');?></span></label>
						</div>
						<?php
					} 
					?>
					<div class="dashboard_row">
						<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="float_left pj-form r10">
							<input type="hidden" name="controller" value="pjAdminBookings" />
							<input type="hidden" name="action" value="pjActionIndex" />
							<input type="submit" class="pj-button" value="<?php __('dash_all_bookings'); ?>" />
						</form>
					</div>
				</div>
			</div>
			
			<div class="dashboard_column">
				<div class="dashboard_list dashboard_latest_list">
					<?php
					if(count($tpl['today_arr']) > 0)
					{
						foreach($tpl['today_arr'] as $v)
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
							?>
							<div class="dashboard_row">
								<label><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate&amp;id=<?php echo $v['id']?>"><?php echo pjSanitize::html($v['c_name']);?></a></label>
								<label><?php echo pjSanitize::html($v['c_email']);?></label>
								<label><span><?php __('dash_time');?></span>: <?php echo date($tpl['option_arr']['o_time_format'], strtotime($v['start_dt']));?> / <?php echo join(" ", $duration_arr);?></label>
							</div>
							<?php
						}
					}else{
						?>
						<div class="dashboard_row">
							<label><span><?php __('dash_no_bookings_found');?></span></label>
						</div>
						<?php
					} 
					?>
				</div>
			</div>
			<div class="dashboard_column">
				<div class="dashboard_list dashboard_latest_list">
					<div class="dashboard_row">
						<label class="block fs14"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionCreate"><?php __('dash_add_booking');?></a></label>
						<label class="block fs14"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminServices&amp;action=pjActionCreate"><?php __('dash_add_service');?></a></label>
						<label class="block fs14">&nbsp;</label>
						<label class="block fs14"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionExport"><?php __('dash_export_bookings');?></a></label>
						<label class="block fs14"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminTime&amp;action=pjActionCustom"><?php __('dash_custom_working_time');?></a></label>
						<label class="block fs14">&nbsp;</label>
						<label class="block fs14"><a href="preview.php" target="_blank"><?php __('dash_preview');?></a></label>
					</div>
				</div>
			</div>
		</div>
		<div class="dashboard_bottom"></div>
	</div>
	
	<div class="clear_left t20 overflow">
		<div class="float_left black t30 t20"><span class="gray"><?php echo ucfirst(__('lblDashLastLogin', true)); ?>:</span> <?php echo pjUtil::formatDate(date('Y-m-d', strtotime($_SESSION[$controller->defaultUser]['last_login'])), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ', ' . pjUtil::formatTime(date('H:i:s', strtotime($_SESSION[$controller->defaultUser]['last_login'])), 'H:i:s', $tpl['option_arr']['o_time_format']); ?></div>
		<div class="float_right overflow">
		<?php
		list($hour, $day, $other) = explode("_", date("H:i_l_F d, Y"));
		$days = __('days', true, false);
		?>
			<div class="dashboard_date">
				<abbr><?php echo $days[date('w')]; ?></abbr>
				<?php echo pjUtil::formatDate(date('Y-m-d'), 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>
			</div>
			<div class="dashboard_hour"><?php echo date($tpl['option_arr']['o_time_format'], time()); ?></div>
		</div>
	</div>
	<?php
}
?>