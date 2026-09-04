<?php
$option_arr = $tpl['option_arr'];
$STORE = $_SESSION[$controller->defaultStore];
$FORM = @$_SESSION[$controller->defaultForm];
$services = isset($STORE['service_id']) ? $STORE['service_id'] : array();

$selected_date_iso = isset($STORE['date_iso']) ? $STORE['date_iso'] : NULl;
$selected_hour_iso = isset($STORE['hour_iso']) ? $STORE['hour_iso'] : NULl;
$selected_minute_iso = isset($STORE['minute_iso']) ? $STORE['minute_iso'] : NULl;
$services_duration = isset($STORE['duration']) ? $STORE['duration'] : 0;
$services_duration_ts = $services_duration * 60;

$selected_date_ts = strtotime($selected_date_iso . ' ' . $selected_hour_iso . ':' . $selected_minute_iso . ':00'); 
?>
<div class="pjSbs-services-form-title"><?php __('front_date_time');?></div><!-- /.pjSbs-services-form-title -->

<div class="pjSbs-services-prices">
	<div class="pjSbs-services-prices-row">
		<div class="pjSbs-service-title"><?php echo date($option_arr['o_date_format'], $selected_date_ts) . ', ' . date($option_arr['o_time_format'], $selected_date_ts);?></div><!-- /.pjSbs-service-title -->
	</div><!-- /.pjSbs-services-prices-row -->

	<?php
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
		<div class="pjSbs-services-prices-row">
			<div class="row">
				<div class="col-xs-9">
					<div class="pjSbs-service-title"><?php echo pjSanitize::html($v['title']);?></div><!-- /.pjSbs-service-title -->

					<div class="pjSbs-service-utilities">
						<em><i class="glyphicon glyphicon-time"></i> <strong><?php __('front_duration');?>:</strong> <?php echo join(" " , $duration_arr);?></em>
					</div>
				</div><!-- /.col-sm-9 -->

				<div class="col-xs-3">
					<strong class="pjSbs-price"><?php echo pjUtil::formatCurrencySign($v['price'], $option_arr['o_currency']);?></strong>
				</div><!-- /.col-sm-3 -->
			</div><!-- /.row -->
		</div><!-- /.pjSbs-services-prices-row -->
		<?php
	} 
	?>
	
	<div class="pjSbs-services-prices-row pjSbs-services-prices-total">
		<div class="row">
			<div class="col-xs-9">
				<div class="pjSbs-service-title"><small><?php __('front_subtotal');?></small></div><!-- /.pjSbs-service-title -->
			</div><!-- /.col-sm-9 -->

			<div class="col-xs-3">
				<strong class="pjSbs-price"><small><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['subtotal'], 2), $option_arr['o_currency']);?></small></strong>
			</div><!-- /.col-sm-3 -->
		</div><!-- /.row -->

		<div class="row">
			<div class="col-xs-9">
				<div class="pjSbs-service-title"><small><?php __('front_tax');?></small></div><!-- /.pjSbs-service-title -->
			</div><!-- /.col-sm-9 -->

			<div class="col-xs-3">
				<strong class="pjSbs-price"><small><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['tax'], 2), $option_arr['o_currency']);?></small></strong>
			</div><!-- /.col-sm-3 -->
		</div><!-- /.row -->

		<div class="row">
			<div class="col-xs-9">
				<div class="pjSbs-service-title"><?php __('front_total');?></div><!-- /.pjSbs-service-title -->
			</div><!-- /.col-sm-9 -->

			<div class="col-xs-3">
				<strong class="pjSbs-price"><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['total'], 2), $option_arr['o_currency']);?></strong>
			</div><!-- /.col-sm-3 -->
		</div><!-- /.row -->
		<div class="row">
			<div class="col-xs-9">
				<div class="pjSbs-service-title"><small><?php __('front_deposit');?></small></div><!-- /.pjSbs-service-title -->
			</div><!-- /.col-sm-9 -->

			<div class="col-xs-3">
				<strong class="pjSbs-price"><small><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['deposit'], 2), $option_arr['o_currency']);?></small></strong>
			</div><!-- /.col-sm-3 -->
		</div><!-- /.row -->
	</div><!-- /.pjSbs-services-prices-row -->
</div><!-- /.pjSbs-services-prices -->