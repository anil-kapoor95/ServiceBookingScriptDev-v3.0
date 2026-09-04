<?php
$selected_date_iso = $tpl['selected_date_iso'];
$option_arr = $tpl['option_arr'];

$selected_hour_iso = $tpl['selected_hour_iso'];
$services_duration = $tpl['services_duration'];
$services_duration_ts = $services_duration * 60;

$hours_before = (int) $option_arr['o_hours_before'];
$before_minute_ts = $hours_before * 3600 + strtotime(date('Y-m-d H:i:00', time()));

$start_minute = strtotime($selected_date_iso . ' 00:00:00');
$end_minute = strtotime($selected_date_iso . ' 00:55:00');
if($selected_hour_iso != NULL)
{
	$start_minute = strtotime($selected_date_iso . ' ' . $selected_hour_iso . ':00:00');
	$end_minute = strtotime($selected_date_iso . ' ' . $selected_hour_iso . ':55:00');
}
?>
<select name="minute_iso" class="pj-form-field">
	<option value="">----</option>
	<?php
	if($selected_hour_iso != NULL)
	{
		for($i = $start_minute; $i <= $end_minute; $i+=300)
		{
			$disabled_attr = NULL;
			if($i < $tpl['wt_arr']['start_ts'] || $i > $tpl['wt_arr']['end_ts'])
			{
				$disabled_attr = ' disabled';
			}
			if(!empty($tpl['booking_arr']))
			{
				$first_booking = $tpl['booking_arr'][0];
				$first_booking_start_ts = strtotime($first_booking['start_dt']);
				$first_booking_end_ts = strtotime($first_booking['end_dt']);
				
				if($i > $first_booking_start_ts - $services_duration_ts && $i <= $first_booking_start_ts)
				{
					$disabled_attr = ' disabled';
				}
				foreach($tpl['booking_arr'] as $k => $v)
				{
					$booking_start_ts = strtotime($v['start_dt']);
					$booking_end_ts = strtotime($v['end_dt']);
					if($i > $booking_start_ts && $i <= $booking_end_ts)
					{
						$disabled_attr = ' disabled';
						break;
					}
					if(isset($tpl['booking_arr'][$k + 1]))
					{
						$next_booking_start_ts = strtotime($tpl['booking_arr'][$k + 1]['start_dt']);
						if($next_booking_start_ts - $booking_end_ts < $services_duration_ts)
						{
							if($i > $booking_end_ts && $i <= $next_booking_start_ts)
							{
								$disabled_attr = ' disabled';
								break;
							}
						}else{
							$middle_booking = $tpl['booking_arr'][$k + 1];
							$middle_booking_start_ts = strtotime($middle_booking['start_dt']);
							if($i > $middle_booking_start_ts - $services_duration_ts && $i <= $middle_booking_start_ts)
							{
								$disabled_attr = ' disabled';
							}
						}
					}
				}
			}
			if($i < $before_minute_ts)
			{
				$disabled_attr = ' disabled';
			}
			?>
			<option value="<?php echo date('i', $i);?>"<?php echo isset($tpl['selected_minute_iso']) ? ($tpl['selected_minute_iso'] == date('i', $i) ? ' selected="selected"' : NULL) : NULL; ?><?php echo $disabled_attr;?>><?php echo date('i', $i);?></option>
			<?php
		}
	}
	?>
</select>