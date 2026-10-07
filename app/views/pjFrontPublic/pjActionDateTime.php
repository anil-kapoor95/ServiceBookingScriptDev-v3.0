<?php
$option_arr = $tpl['option_arr']; 
$STORE = $_SESSION[$controller->defaultStore];
$services = isset($STORE['service_id']) ? $STORE['service_id'] : array();
$selected_date_iso = isset($STORE['date_iso']) ? $STORE['date_iso'] : NULl;
$selected_hour_iso = isset($STORE['hour_iso']) ? $STORE['hour_iso'] : NULl;
$selected_minute_iso = isset($STORE['minute_iso']) ? $STORE['minute_iso'] : NULl;
$services_duration = isset($STORE['duration']) ? $STORE['duration'] : 0;
$services_duration_ts = $services_duration * 60;

$hours_before = (int) $option_arr['o_hours_before'];

$before_hour_ts =  $hours_before * 3600 + strtotime(date('Y-m-d H:00:00', time()));
$before_minute_ts = $hours_before * 3600 + strtotime(date('Y-m-d H:i:00', time()));

$week_start_ts = null;
$week_end_ts = null;

$pjSbsStep = 2;
include_once dirname(__FILE__) . '/elements/steps.php';
?>
<div class="pjSbs-services">
	<form id="pjSbsDateTimeForm_<?php echo $_GET['index']?>" action="" method="post">
		<div class="pjSbs-services-head">
			<div class="pjSbs-services-title"><?php __('front_date_time');?></div><!-- /.pjSbs-services-title -->
			
			<a href="#" class="pjSbs-btn-back <?php echo !empty($tpl['has_extras']) ? 'pjSbsBackToExtras' : 'pjSbsBackToServices';?>"><span class="glyphicon glyphicon-share-alt"></span></a>
		</div><!-- /.pjSbs-services-head -->
		
		<div class="pjSbs-services-body">
			<div class="pjSbs-calendar-pick-holder">
				<div class="owl-carousel pjSbs-calendar-pick">
					<?php
					$i = 1;
					$day_short_names = __('day_short_names', true);
					$short_months = __('short_months', true);
					foreach($tpl['week_arr'] as $date => $is_date_off)
					{ 
						$date_ts = strtotime($date);
						$week_day = date('w', $date_ts);
						$month = date('n', $date_ts);
						if($i == 1)
						{
							$week_start_ts = $date_ts;
						}
						if($i == 7)
						{
							$week_end_ts = $date_ts;
						}
						$is_active = false;
						if($selected_date_iso != NULL && $date == $selected_date_iso)
						{
							$is_active = true;
						}
						$disabled = false;
						if($is_date_off == true)
						{
							$disabled = true;
						}else{
							if($date < date('Y-m-d'))
							{
								$disabled = true;
							}
						}
						?>
						<div id="pjSbsDate_<?php echo $i;?>" class="pjSbs-date<?php echo $disabled==true ? ' disabled' : ($is_active == true ? " active" : NULL);?>" data-date="<?php echo $date;?>">
							<div class="pjSbs-date-day"><?php echo strtoupper($day_short_names[$week_day]);?></div><!-- /.pjSbs-date-day -->
		
							<div class="pjSbs-date-number"><?php echo date('d', $date_ts);?></div><!-- /.pjSbs-date-number -->
		
							<div class="pjSbs-date-month"><?php echo $short_months[$month];?></div><!-- /.pjSbs-date-month -->
						</div><!-- /.pjSbs-date -->
						<?php
						$i++;
					} 
					?>
					
				</div>
				<div class="pjSbs-owl-nav">
					<div id="pjSbsWeekPrev_<?php echo $_GET['index'];?>" class="pjSbs-owl-prev" data-start_iso="<?php echo date('Y-m-d', $week_start_ts - (86400 *7)); ?>" data-end_iso="<?php echo date('Y-m-d', $week_start_ts - 86400); ?>"></div>
					<div id="pjSbsWeekNext_<?php echo $_GET['index'];?>" class="pjSbs-owl-next" data-start_iso="<?php echo date('Y-m-d', $week_end_ts + 86400); ?>" data-end_iso="<?php echo date('Y-m-d', $week_end_ts + (86400 * 7)); ?>"></div>
				</div>
			</div>
			<?php
			if(empty($tpl['wt_arr']))
			{
				?>
				<div class="pjSbs-not-available">
					<img src="<?php echo PJ_INSTALL_URL . PJ_IMG_PATH?>frontend/date-time-clock.png" alt="">
	
					<p><?php __('front_unavailable_date');?></p>
				</div><!-- /.not-available -->
				<?php
			} else{
				$start_hour = strtotime($selected_date_iso . ' 00:00:00');
				$end_hour = strtotime($selected_date_iso . ' 23:00:00');
				
				$start_minute = strtotime($selected_date_iso . ' 00:00:00');
				$end_minute = strtotime($selected_date_iso . ' 00:55:00');
				if($selected_hour_iso != NULL)
				{
					$start_minute = strtotime($selected_date_iso . ' ' . $selected_hour_iso . ':00:00');
					$end_minute = strtotime($selected_date_iso . ' ' . $selected_hour_iso . ':55:00');
				}
				?>
				<div class="pjSbs-available-times">
					<div class="pjSbs-available-times-title"><?php __('front_available_times');?></div><!-- /.pjSbs-available-times-title -->
					
					<div class="row">
						<div class="col-sm-8">
							<div class="pjSbs-available-hours">
							
								<div class="pjSbs-time-grid">
											
									<div class="pjSbs-heading"><?php __('front_hour');?></div>
										
									<div class="pjSbs-body">
										<div id="pjSbsPeriod_<?php echo $_GET['index'];?>" class="pjSbs-period">
											<span>AM</span>
											<span>PM</span>
										</div>
										<div class="pjSbs-hours">
											<?php
											$wt_start_ts = strtotime(date('Y-m-d H:00:00', $tpl['wt_arr']['start_ts']));
											$wt_end_ts = strtotime(date('Y-m-d H:00:00', $tpl['wt_arr']['end_ts']));
											
											$selected_hour_ts = NULL;
											if($selected_hour_iso != NULL)
											{
												$selected_hour_ts = strtotime($selected_date_iso . ' ' . $selected_hour_iso . ':00:00');
											}
											for($i = $start_hour; $i <= $end_hour; $i+=3600)
											{
												$disabled_class = NULL;
												if($i < $wt_start_ts || $i > $wt_end_ts)
												{
													$disabled_class = ' pjSbs-disabled';
												}
												if(!empty($tpl['booking_arr']))
												{
													$first_booking = $tpl['booking_arr'][0];
													$first_booking_start_ts = strtotime(date('Y-m-d H:00:00', strtotime($first_booking['start_dt'])));
													
													if($i > $first_booking_start_ts - $services_duration_ts && $i < $first_booking_start_ts)
													{
														$disabled_class = ' pjSbs-disabled';
													}else if($i == $first_booking_start_ts - $services_duration_ts && $services_duration > 60){
														$disabled_class = ' pjSbs-disabled';
													}else if($i == $first_booking_start_ts && (int) $first_booking['duration'] > 55){
														$disabled_class = ' pjSbs-disabled';
													}
													foreach($tpl['booking_arr'] as $k => $v)
													{
														$booking_start_ts = strtotime(date('Y-m-d H:00:00', strtotime($v['start_dt'])));
														$booking_end_ts = strtotime(date('Y-m-d H:00:00', strtotime($v['end_dt'])));
														if($i >= $booking_start_ts && $i <= $booking_end_ts)
														{
															$disabled_class = ' pjSbs-disabled';
															break;
														}
														if(isset($tpl['booking_arr'][$k + 1]))
														{
															$next_booking_start_ts = strtotime(date('Y-m-d H:00:00', strtotime($tpl['booking_arr'][$k + 1]['start_dt'])));
															if($next_booking_start_ts - $booking_end_ts < $services_duration_ts)
															{
																if($i >= $booking_end_ts && $i <= $next_booking_start_ts)
																{
																    $disabled_class = ' pjSbs-disabled ' . date('Y-m-d H:is', $i) . '==' . date('Y-m-d H:is', $booking_end_ts);
																	break;
																}
															}else{
																$middle_booking = $tpl['booking_arr'][$k + 1];
																$first_booking_start_ts = strtotime(date('Y-m-d H:00:00', strtotime($middle_booking['start_dt'])));
																if($i >= $middle_booking_start_ts - $services_duration_ts && $i <= $middle_booking_start_ts)
																{
																	$disabled_class = ' pjSbs-disabled';
																}else if($i == $middle_booking_start_ts - $services_duration_ts && $services_duration > 60){
																	$disabled_class = ' pjSbs-disabled';
																}else if($i == $middle_booking_start_ts && (int) $middle_booking['duration'] > 55){
																	$disabled_class = ' pjSbs-disabled';
																}
															}
														}
													}
												}
												if($i < $before_hour_ts)
												{
													$disabled_class = ' pjSbs-disabled';
												}
												?>
												<div class="pjSbs-hour-cell"><span class="pjSbsSetHour<?php echo $selected_hour_ts != NULL ? ($i == $selected_hour_ts ? ' active' : NULL) : NULL; ?><?php echo $disabled_class; ?>" data-hour="<?php echo date('H', $i);?>"><?php echo date('h', $i);?></span></div>
												<?php
											} 
											?>
										</div>
									</div>
								</div>
							</div><!-- /.pjSbs-available-hours -->
						</div><!-- /.col-sm-8 -->
					
						<div class="col-sm-4">
							<div class="pjSbs-available-minutes">
								<div class="pjSbs-available-hours">
									<div class="pjSbs-time-grid">
											
										<div class="pjSbs-heading"><?php __('front_minutes');?></div>
										<div class="pjSbs-body">
											<?php
											for($i = $start_minute; $i <= $end_minute; $i+=300)
											{
												$disabled_class = NULL;
												if($i < $tpl['wt_arr']['start_ts'] || $i > $tpl['wt_arr']['end_ts'])
												{
													$disabled_class = ' pjSbs-disabled';
												}
												if(!empty($tpl['booking_arr']))
												{
													$first_booking = $tpl['booking_arr'][0];
													$first_booking_start_ts = strtotime($first_booking['start_dt']);
													$first_booking_end_ts = strtotime($first_booking['end_dt']);
												
													if($i > $first_booking_start_ts - $services_duration_ts && $i <= $first_booking_start_ts)
													{
														$disabled_class = ' pjSbs-disabled';
													}
													foreach($tpl['booking_arr'] as $k => $v)
													{
														$booking_start_ts = strtotime($v['start_dt']);
														$booking_end_ts = strtotime($v['end_dt']);
														if($i > $booking_start_ts && $i <= $booking_end_ts)
														{
															$disabled_class = ' pjSbs-disabled';
															break;
														}
														if(isset($tpl['booking_arr'][$k + 1]))
														{
															$next_booking_start_ts = strtotime($tpl['booking_arr'][$k + 1]['start_dt']);
															if($next_booking_start_ts - $booking_end_ts < $services_duration_ts)
															{
																if($i > $booking_end_ts && $i <= $next_booking_start_ts)
																{
																	$disabled_class = ' pjSbs-disabled';
																	break;
																}
															}else{
																$middle_booking = $tpl['booking_arr'][$k + 1];
																$middle_booking_start_ts = strtotime($middle_booking['start_dt']);
																if($i > $middle_booking_start_ts - $services_duration_ts && $i <= $middle_booking_start_ts)
																{
																	$disabled_class = ' pjSbs-disabled';
																}
															}
														}
													}
												}
												if($i < ($before_minute_ts))
												{
													$disabled_class = ' pjSbs-disabled';
												}
												?>
												<div class="pjSbs-minute">
													<span class="pjSbsSetMinute<?php echo $selected_minute_iso != NULL ? (date('i', $i) == $selected_minute_iso ? ' active' : NULL) : NULL; ?><?php echo $disabled_class; ?>" data-minute="<?php echo date('i', $i);?>" ><?php echo date('i', $i);?></span>
												</div>
												<?php
											} 
											?>
										</div>
									</div>
									
								</div><!-- /.pjSbs-available-hours -->
							</div><!-- /.pjSbs-available-minutes -->
						</div><!-- /.col-sm-4 -->
					</div><!-- /.row -->
				</div><!-- /.pjSbs-available-times -->
				<?php
			}
			?>
			
		</div>
		<?php
		if(!empty($tpl['wt_arr']) && $selected_date_iso != NULL && $selected_hour_iso != NULL && $selected_minute_iso != NULL)
		{ 
			$selected_date_ts = strtotime($selected_date_iso . ' ' . $selected_hour_iso . ':' . $selected_minute_iso . ':00');
			?>
			<div class="pjSbs-services-footer">
				<div class="pjSbs-services-footer-title"><?php __('front_selected_date_time');?></div><!-- /.pjSbs-services-footer-title -->
	
				<p><?php echo date($option_arr['o_date_format'], $selected_date_ts) . ', ' . date($option_arr['o_time_format'], $selected_date_ts);?></p>
	
				<input type="submit" class="btn btn-primary" value="<?php __('front_btn_book_now');?>">
			</div><!-- /.pjSbs-services-footer -->
			<?php
		}
		?>	
	</form>
</div><!-- /.pjSbs-services -->