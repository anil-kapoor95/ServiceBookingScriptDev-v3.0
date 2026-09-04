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
	
	?>
	
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminTime&amp;action=pjActionIndex"><?php __('menuDefaultWorkingTime'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminTime&amp;action=pjActionCustom"><?php __('menuCustomWorkingTime'); ?></a></li>
		</ul>
	</div>
	<?php pjUtil::printNotice(__('infoDefaultWTimeTitle', true), __('infoDefaultWTimeDesc', true)); ?>
	<form id="frmDefaultWTime" action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminTime&amp;action=pjActionIndex" method="post" class="form">
		<input type="hidden" name="working_time" value="1" />
		<input type="hidden" name="csrf_token" value="<?php echo pjAppController::getCsrfToken(); ?>" />
		<?php
		if(!empty($tpl['wt_arr']))
		{ 
			?>
			<input type="hidden" name="id" value="<?php echo $tpl['wt_arr']['id']?>" />
			<?php
		} 
		?>
		<table class="pj-table" cellpadding="0" cellspacing="0" style="width: 100%;">
			<thead>
				<tr>
					<th><?php __('time_day'); ?></th>
					<th><?php __('time_is'); ?></th>
					<th><?php __('time_from'); ?></th>
					<th><?php __('time_to'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			$i = 1;
			$days = __('days', true);
			$w_days = array(
				'monday' => $days[1],
				'tuesday' => $days[2],
				'wednesday' => $days[3],
				'thursday' => $days[4],
				'friday' => $days[5],
				'saturday' => $days[6],
				'sunday' => $days[0]
			);
			$show_period = 'false';
			if(strpos($tpl['option_arr']['o_time_format'], 'a') > -1 || strpos($tpl['option_arr']['o_time_format'], 'A') > -1)
			{
				$show_period = 'true';
			}
			foreach ($w_days as $k => $day)
			{
				$from = null;
				$to = null;
				if (isset($tpl['wt_arr'][$k.'_from']) && !empty($tpl['wt_arr'][$k.'_from']) && isset($tpl['wt_arr'][$k.'_to']) && !empty($tpl['wt_arr'][$k.'_to']))
				{
					$from = date('H:i', strtotime($tpl['wt_arr'][$k.'_from']));
					$to = date('H:i', strtotime($tpl['wt_arr'][$k.'_to']));
					
					if($show_period == 'true')
					{
						$from = date('h:i A', strtotime($tpl['wt_arr'][$k.'_from']));
						$to = date('h:i A', strtotime($tpl['wt_arr'][$k.'_to']));
					}
					
					$checked = NULL;
					$dayoff_class = NULL;
						
					if ($tpl['wt_arr'][$k.'_dayoff'] == 'T')
					{
						$checked = ' checked="checked"';
						$dayoff_class = ' tsDayOff';
					}
				} else {
					$from = NULL;
					$to = NULL;

					$checked = NULL;
				}
				?>
				<tr class="<?php echo ($i % 2 !== 0 ? 'odd' : 'even'); ?>" data-day="<?php echo $k;?>">
					<td><?php echo $day; ?></td>
					<td class="align_center"><input type="checkbox" class="working_day" name="<?php echo $k; ?>_dayoff" value="T"<?php echo $checked; ?> /></td>
					<td>
						<p class="w130 tsWorkingDay_<?php echo $k;?><?php echo $dayoff_class;?>">
							<span class="inline-block">
								<input name="<?php echo $k?>_from" data-day="<?php echo $k?>" value="<?php echo $from;?>" class="pj-timepicker pj-form-field w80" readonly="readonly"/>
							</span>
						</p>
					</td>
					<td>
						<p class="w130 tsWorkingDay_<?php echo $k;?><?php echo $dayoff_class;?>">
							<span class="inline-block">
								<input name="<?php echo $k?>_to" data-day="<?php echo $k?>" value="<?php echo $to;?>" class="pj-timepicker pj-form-field w80" readonly="readonly"/>								
							</span>
						</p>
					</td>
				</tr>
				<?php
				$i++;
			}
			?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="6"><input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" /></td>
				</tr>
			</tfoot>
		</table>
	</form>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.showperiod = <?php echo $show_period; ?>;
	</script>
	<?php
}
?>