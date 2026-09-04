<?php
$selected_date_iso = $tpl['selected_date_iso'];
$option_arr = $tpl['option_arr'];

$services_duration = $tpl['services_duration'];
$services_duration_ts = $services_duration * 60;

$hours_before = (int) $option_arr['o_hours_before'];
$before_hour_ts =  $hours_before * 3600 + strtotime(date('Y-m-d H:00:00', time()));

$start_hour = strtotime($selected_date_iso . ' 00:00:00');
$end_hour = strtotime($selected_date_iso . ' 23:00:00');


?>
<select name="hour_iso" class="pj-form-field">
	<option value="">----</option>
	<?php
	if(!empty($tpl['wt_arr']))
	{
		$wt_start_ts = strtotime(date('Y-m-d H:00:00', $tpl['wt_arr']['start_ts']));
		$wt_end_ts = strtotime(date('Y-m-d H:00:00', $tpl['wt_arr']['end_ts']));
		for($i = $start_hour; $i <= $end_hour; $i+=3600)
		{
			$disabled_attr = NULL;
			if($i < $wt_start_ts || $i > $wt_end_ts)
			{
				$disabled_attr = ' disabled';
			}
			if(!empty($tpl['booking_arr']))
			{
				$first_booking = $tpl['booking_arr'][0];
				$first_booking_start_ts = strtotime(date('Y-m-d H:00:00', strtotime($first_booking['start_dt'])));
				
				if($i > $first_booking_start_ts - $services_duration_ts && $i < $first_booking_start_ts)
				{
					$disabled_attr = ' disabled';
				}else if($i == $first_booking_start_ts - $services_duration_ts && $services_duration > 60){
					$disabled_attr = ' disabled';
				}else if($i == $first_booking_start_ts && (int) $first_booking['duration'] > 55){
					$disabled_attr = ' disabled';
				}
				foreach($tpl['booking_arr'] as $k => $v)
				{
					$booking_start_ts = strtotime(date('Y-m-d H:00:00', strtotime($v['start_dt'])));
					$booking_end_ts = strtotime(date('Y-m-d H:00:00', strtotime($v['end_dt'])));
					if($i > $booking_start_ts && $i < $booking_end_ts)
					{
						$disabled_attr = ' disabled';
						break;
					}
					if(isset($tpl['booking_arr'][$k + 1]))
					{
						$next_booking_start_ts = strtotime(date('Y-m-d H:00:00', strtotime($tpl['booking_arr'][$k + 1]['start_dt'])));
						if($next_booking_start_ts - $booking_end_ts < $services_duration_ts)
						{
							if($i >= $booking_end_ts && $i < $next_booking_start_ts)
							{
								$disabled_attr = ' disabled';
								break;
							}
						}else{
							$middle_booking = $tpl['booking_arr'][$k + 1];
							$first_booking_start_ts = strtotime(date('Y-m-d H:00:00', strtotime($middle_booking['start_dt'])));
							if($i > $middle_booking_start_ts - $services_duration_ts && $i < $middle_booking_start_ts)
							{
								$disabled_attr = ' disabled';
							}else if($i == $middle_booking_start_ts - $services_duration_ts && $services_duration > 60){
								$disabled_attr = ' disabled';
							}else if($i == $middle_booking_start_ts && (int) $middle_booking['duration'] > 55){
								$disabled_attr = ' disabled';
							}
						}
					}
				}
			}
			if($i < $before_hour_ts && $tpl['booking_id'] == 0)
			{
				$disabled_attr = ' disabled';
			}
			?>
			<option value="<?php echo date('H', $i);?>"<?php echo isset($tpl['selected_hour_iso']) && $tpl['selected_hour_iso'] != NULL ? ($tpl['selected_hour_iso'] == date('H', $i) ? ' selected="selected"' : NULL) : NULL; ?><?php echo $disabled_attr;?>><?php echo date('H', $i);?></option>
			<?php
		}
	} 
	?>
</select>