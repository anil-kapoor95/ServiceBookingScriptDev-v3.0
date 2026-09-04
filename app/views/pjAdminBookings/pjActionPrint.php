<div style="margin-bottom: 6px; font-weight: bold;"><?php echo date($tpl['option_arr']['o_date_format'], time());?></div>
<div style="margin: 0 auto; width: 100%;">
	<table class="table" cellspacing="2" cellpadding="5" style="width: 100%">
		<thead>
			<tr>
				<th><?php __('lblResvName')?></th>
				<th><?php __('lblResvEmail')?></th>
				<th><?php __('lblResvPhone')?></th>
				<th><?php __('lblStartTime')?></th>
				<th><?php __('lblServices')?></th>
				<th><?php __('lblDuration')?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			if(!empty($tpl['arr'])) 
			{
				$name_titles = __('personal_titles', true, false);
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
					?>
					<tr>
						<td style="vertical-align: top;"><?php echo pjSanitize::html($v['c_name']);?></td>
						<td style="vertical-align: top;"><?php echo pjSanitize::html($v['c_email']);?></td>
						<td style="vertical-align: top;"><?php echo pjSanitize::html($v['c_phone']);?></td>
						<td style="vertical-align: top;"><?php echo date($tpl['option_arr']['o_time_format'], strtotime($v['start_dt']));?></td>
						<td style="vertical-align: top;"><?php echo $v['services'];?></td>
						<td style="vertical-align: top;"><?php echo join(" ", $duration_arr);?></td>
					</tr>
					<?php
				}
			}else{
				?><tr><td colspan="6"><?php __('dash_no_bookings_found');?></td></tr><?php
			}
			?>
		</tbody>
	</table>
</div>