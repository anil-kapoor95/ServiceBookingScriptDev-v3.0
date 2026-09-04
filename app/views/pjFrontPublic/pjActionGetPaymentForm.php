<div class="pjSbs-services">
	<div class="pjSbs-services-head">
		<div class="pjSbs-services-title"><?php __('front_thank_you');?></div><!-- /.pjSbs-services-title -->
	</div><!-- /.pjSbs-services-head -->
	<div class="pjSbs-services-body">
		<div class="pjSbsb-services-thanks">
			<?php
			if (isset($tpl['get']['payment_method']))
			{
				$status = __('front_booking_statuses', true);
				switch ($tpl['get']['payment_method'])
				{
					case 'paypal':
						?><p class="text-success text-center"><?php echo $status[2]; ?></p><?php
						if (pjObject::getPlugin('pjPaypal') !== NULL)
						{
							$controller->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionForm', 'params' => $tpl['params']));
						}
						break;
					case 'authorize':
						?><p class="text-success text-center"><?php echo $status[3]; ?></p><?php
						if (pjObject::getPlugin('pjAuthorize') !== NULL)
						{
							$controller->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionForm', 'params' => $tpl['params']));
						}
						break;
					case 'bank':
						?><p class="text-success text-center"><?php echo $status[1]; ?></p><?php
						break;
					case 'creditcard':
					case 'cash':
					default:
						?><p class="text-success text-center"><?php echo $status[1]; ?></p><?php
				}
			}
			?>
			<?php
			if($tpl['get']['payment_method'] == 'bank' || $tpl['get']['payment_method'] == 'creditcard' || $tpl['get']['payment_method'] == 'cash' || $tpl['option_arr']['o_payment_disable'] == 'Yes') 
			{
				?>
				<div class="col-sm-12 text-center">
					<input type="button" class="btn btn-default pjSbsBtnStartOver" value="<?php __('front_btn_start_over')?>" />
				</div>
				<?php
			} 
			?>
		</div>
		
	</div>
</div>