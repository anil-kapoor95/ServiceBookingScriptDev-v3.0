<div class="pjSbs-services">
	<form id="pjSbsPreviewForm_<?php echo $_GET['index']?>" action="" method="post">
		<div class="pjSbs-services-head">
			<div class="pjSbs-services-title"><?php __('front_your_booking');?></div><!-- /.pjSbs-services-title -->
			
			<a href="#" class="pjSbs-btn-back pjSbsBackToCheckout"><span class="glyphicon glyphicon-share-alt"></span></a>
		</div><!-- /.pjSbs-services-head -->
		
		<div class="pjSbs-services-form">
			<?php include_once dirname(__FILE__) . '/elements/booking.php';?>

			<div class="pjSbs-services-form-title"><?php __('front_client_details');?></div><!-- /.pjSbs-services-form-title -->

			<input type="hidden" name="sbs_preview" value="1" />
			
			<?php
			ob_start();
			$columns = 1;
			if (in_array((int) $tpl['option_arr']['o_bf_include_title'], array(2,3)))
			{
				$personal_titles = __('personal_titles', true);
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_title'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_title'] === 3) {?><span>*</span><?php }?></label>
						<div class="text-muted"><?php echo $personal_titles[$FORM['c_title']];?></div>						
					</div><!-- /.form-group -->
				</div>
				<?php
				$columns++;
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_name'], array(2,3)))
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_name'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_name'] === 3) {?><span>*</span><?php }?></label>
						<div class="text-muted"><?php echo pjSanitize::html(@$FORM['c_name']); ?></div>	
					</div>
				</div>
				<?php
				$columns++;
				if($columns == 2)
				{
					$field_content = ob_get_contents();
					ob_end_clean();
					?>
					<div class="row"><?php echo $field_content;?></div>
					<?php
					$columns = 0;
					ob_start();
				}
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_email'], array(2,3)))
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_email'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_email'] === 3) {?><span>*</span><?php }?></label>
						
						<div class="text-muted"><?php echo pjSanitize::html(@$FORM['c_email']); ?></div>	
					</div>
				</div>
				<?php
				$columns++;
				if($columns == 2)
				{
					$field_content = ob_get_contents();
					ob_end_clean();
					?>
					<div class="row"><?php echo $field_content;?></div>
					<?php
					$columns = 0;
					ob_start();
				}
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_phone'], array(2,3)))
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_phone'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_phone'] === 3) {?><span>*</span><?php }?></label>
						
						<div class="text-muted"><?php echo pjSanitize::html(@$FORM['c_phone']); ?></div>	
					</div>
				</div>
				<?php
				$columns++;
				if($columns == 2)
				{
					$field_content = ob_get_contents();
					ob_end_clean();
					?>
					<div class="row"><?php echo $field_content;?></div>
					<?php
					$columns = 0;
					ob_start();
				}
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_company'], array(2,3)))
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_company'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_company'] === 3) {?><span>*</span><?php }?></label>
						
						<div class="text-muted"><?php echo pjSanitize::html(@$FORM['c_company']); ?></div>	
					</div>
				</div>
				<?php
				$columns++;
				if($columns == 2)
				{
					$field_content = ob_get_contents();
					ob_end_clean();
					?>
					<div class="row"><?php echo $field_content;?></div>
					<?php
					$columns = 0;
					ob_start();
				}
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_address'], array(2,3)))
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_address'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_address'] === 3) {?><span>*</span><?php }?></label>
						
						<div class="text-muted"><?php echo pjSanitize::html(@$FORM['c_address']); ?></div>	
					</div>
				</div>
				<?php
				$columns++;
				if($columns == 2)
				{
					$field_content = ob_get_contents();
					ob_end_clean();
					?>
					<div class="row"><?php echo $field_content;?></div>
					<?php
					$columns = 0;
					ob_start();
				}
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_city'], array(2,3)))
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_city'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_city'] === 3) {?><span>*</span><?php }?></label>
						
						<div class="text-muted"><?php echo pjSanitize::html(@$FORM['c_city']); ?></div>	
					</div>
				</div>
				<?php
				$columns++;
				if($columns == 2)
				{
					$field_content = ob_get_contents();
					ob_end_clean();
					?>
					<div class="row"><?php echo $field_content;?></div>
					<?php
					$columns = 0;
					ob_start();
				}
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_state'], array(2,3)))
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_state'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_state'] === 3) {?><span>*</span><?php }?></label>
						
						<div class="text-muted"><?php echo pjSanitize::html(@$FORM['c_state']); ?></div>	
					</div>
				</div>
				<?php
				$columns++;
				if($columns == 2)
				{
					$field_content = ob_get_contents();
					ob_end_clean();
					?>
					<div class="row"><?php echo $field_content;?></div>
					<?php
					$columns = 0;
					ob_start();
				}
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_state'], array(2,3)))
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_zip'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_zip'] === 3) {?><span>*</span><?php }?></label>
						
						<div class="text-muted"><?php echo pjSanitize::html(@$FORM['c_zip']); ?></div>	
					</div>
				</div>
				<?php
				$columns++;
				if($columns == 2)
				{
					$field_content = ob_get_contents();
					ob_end_clean();
					?>
					<div class="row"><?php echo $field_content;?></div>
					<?php
					$columns = 0;
					ob_start();
				}
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_country'], array(2,3)) && isset($FORM['c_country']) && (int) $FORM['c_country'] > 0)
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_country'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_country'] === 3) {?><span>*</span><?php }?></label>
	
						<div class="text-muted"><?php echo $tpl['country_arr']['country_title'];?></div>
					</div><!-- /.form-group -->
				</div>
				<?php
				$columns++;
			}
			$field_content = ob_get_contents();
			ob_end_clean();
			if(!empty($field_content))
			{
				?>
				<div class="row"><?php echo $field_content;?></div>
				<?php
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_notes'], array(2,3)))
			{
				?>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label><?php __('front_notes'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_notes'] === 3) {?><span>*</span><?php }?></label>
							
							<div class="text-muted"><?php echo nl2br(pjSanitize::html(@$FORM['c_notes'])); ?></div>	
						</div>
					</div>
				</div>
				<?php
			}

			if ($tpl['option_arr']['o_payment_disable'] == 'No')
			{
				$payment_methods = __('payment_methods', true);
				$cc_types = __('cc_types', true);
				?>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php __('front_payment_method')?></label>
							
							<div class="text-muted"><?php echo $payment_methods[$FORM['payment_method']]; ?></div>
						</div>
					</div><!-- /.col-sm-6 -->
					
					<div class="col-sm-6 pjSbsBankWrap" style="display: <?php echo @$FORM['payment_method'] != 'bank' ? 'none' : NULL; ?>">
						<div class="form-group">
							<label><?php __('front_bank_account')?></label>
							
							<div class="text-muted"><strong><?php echo nl2br(pjSanitize::html($tpl['option_arr']['o_bank_account'])); ?></strong></div>
						</div>
					</div><!-- /.col-sm-6 -->
				</div>
				
				<div class="row pjSbsCcWrap" style="display: <?php echo @$FORM['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php __('front_cc_type')?></label>
							
							<div class="text-muted"><?php echo $cc_types[$FORM['cc_type']]; ?></div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php __('front_cc_num')?></label>
							
							<div class="text-muted"><?php echo pjSanitize::html(@$FORM['cc_num']); ?></div>
						</div>
					</div>
				</div>
				<div class="row pjSbsCcWrap" style="display: <?php echo @$FORM['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php __('front_cc_code')?></label>
							
							<div class="text-muted"><?php echo pjSanitize::html(@$FORM['cc_code']); ?></div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php __('front_cc_exp')?></label>
							<div class="row">
								<div class="col-sm-7">
									<div class="text-muted"><?php echo pjSanitize::html(@$FORM['cc_exp_month']); ?></div>
								</div>
								<div class="col-sm-5">
									<div class="text-muted"><?php echo pjSanitize::html(@$FORM['cc_exp_year']); ?></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
			?>
		</div><!-- /.pjSbs-services-form -->

		<div class="pjSbs-services-footer pjSbs-services-footer-inline">
			<input type="submit" class="btn btn-primary" value="<?php __('front_btn_confirm')?>">
		</div><!-- /.pjSbs-services-footer -->
	</form>
	
</div><!-- /.pjSbs-services -->