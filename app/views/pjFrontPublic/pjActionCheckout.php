<div class="pjSbs-services">
	<form id="pjSbsCheckoutForm_<?php echo $_GET['index']?>" action="" method="post">
		<div class="pjSbs-services-head">
			<div class="pjSbs-services-title"><?php __('front_your_booking');?></div><!-- /.pjSbs-services-title -->
			
			<a href="#" class="pjSbs-btn-back pjSbsBackToDateTime"><span class="glyphicon glyphicon-share-alt"></span></a>
		</div><!-- /.pjSbs-services-head -->
		
		<div class="pjSbs-services-form">
			<?php include_once dirname(__FILE__) . '/elements/booking.php';?>

			<div class="pjSbs-services-form-title"><?php __('front_client_details');?></div><!-- /.pjSbs-services-form-title -->

			<input type="hidden" name="sbs_checkout" value="1" />
			<input type="hidden" name="subtotal" value="<?php echo $tpl['price_arr']['subtotal'];?>" />
			<input type="hidden" name="tax" value="<?php echo $tpl['price_arr']['tax'];?>" />
			<input type="hidden" name="total" value="<?php echo $tpl['price_arr']['total'];?>" />
			<input type="hidden" name="deposit" value="<?php echo $tpl['price_arr']['deposit'];?>" />
			
			<?php
			ob_start();
			$columns = 0;
			if (in_array((int) $tpl['option_arr']['o_bf_include_title'], array(2,3)))
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_title'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_title'] === 3) {?><span>*</span><?php }?></label>
	
						<select id="c_title" name="c_title" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_title'] === 3 ? ' required' : null;?>" data-msg-required="<?php __('pj_field_required'); ?>">
							<option value=""></option>
							<?php
							foreach(__('personal_titles', true) as $k => $v) 
							{
								?><option value="<?php echo $k;?>"<?php echo isset($FORM['c_title']) ? ($FORM['c_title'] == $k ? ' selected="selected"' : null) : null;?>><?php  echo $v;?></option><?php
							}
							?>
						</select>
						<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
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
						
						<input type="text" id="c_name" name="c_name" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_name'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$FORM['c_name']); ?>" data-msg-required="<?php __('pj_field_required'); ?>">
				    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
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
						
						<input type="text" id="c_email" name="c_email" class="form-control email<?php echo (int) $tpl['option_arr']['o_bf_include_email'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$FORM['c_email']); ?>" data-msg-required="<?php __('pj_field_required'); ?>" data-msg-email="<?php __('pj_email_validation'); ?>">
				    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
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
						
						<input type="text" id="c_phone" name="c_phone" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_phone'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$FORM['c_phone']); ?>" data-msg-required="<?php __('pj_field_required'); ?>">
				    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
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
						
						<input type="text" id="c_company" name="c_company" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_company'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$FORM['c_company']); ?>" data-msg-required="<?php __('pj_field_required'); ?>">
				    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
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
						
						<input type="text" id="c_address" name="c_address" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_address'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$FORM['c_address']); ?>" data-msg-required="<?php __('pj_field_required'); ?>">
				    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
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
						
						<input type="text" id="c_city" name="c_city" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_city'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$FORM['c_city']); ?>" data-msg-required="<?php __('pj_field_required'); ?>">
				    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
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
						
						<input type="text" id="c_state" name="c_state" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_state'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$FORM['c_state']); ?>" data-msg-required="<?php __('pj_field_required'); ?>">
				    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
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
						
						<input type="text" id="c_zip" name="c_zip" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_zip'] === 3 ? ' required' : NULL; ?>" value="<?php echo pjSanitize::html(@$FORM['c_zip']); ?>" data-msg-required="<?php __('pj_field_required'); ?>">
				    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
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
			if (in_array((int) $tpl['option_arr']['o_bf_include_country'], array(2,3)))
			{
				?>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php __('front_country'); ?> <?php if((int) $tpl['option_arr']['o_bf_include_country'] === 3) {?><span>*</span><?php }?></label>
	
						<select id="c_country" name="c_country" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_country'] === 3 ? ' required' : null;?>" data-msg-required="<?php __('pj_field_required'); ?>">
							<option value=""></option>
							<?php
							foreach($tpl['country_arr'] as $k => $v) 
							{
								?><option value="<?php echo $v['id'];?>"<?php echo isset($FORM['c_country']) ? ($FORM['c_country'] == $v['id'] ? ' selected="selected"' : null) : null;?>><?php  echo pjSanitize::html($v['country_title']);?></option><?php
							}
							?>
						</select>
						<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
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
							
							<textarea id="c_notes" name="c_notes" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_notes'] === 3 ? ' required' : NULL; ?>" style="height: 150px;" data-msg-required="<?php __('pj_field_required'); ?>"><?php echo pjSanitize::html(@$FORM['c_notes']); ?></textarea>
					    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div>
					</div>
				</div>
				<?php
			}

			if ($tpl['option_arr']['o_payment_disable'] == 'No')
			{
				?>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php __('front_payment_method')?></label>
							
							<select name="payment_method" class="form-control required" data-msg-required="<?php __('pj_field_required'); ?>">
								<option value="">-- <?php __('front_choose');?> --</option>
								<?php
								foreach (__('payment_methods', true) as $k => $v)
								{
									if ($tpl['option_arr']['o_allow_' . $k] === "Yes")
									{
										?><option value="<?php echo $k; ?>"<?php echo @$FORM['payment_method'] != $k ? NULL : ' selected="selected"'; ?>><?php echo $v; ?></option><?php
									}
								}
								?>
							</select>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div>
					</div><!-- /.col-sm-6 -->
					
					<div class="col-sm-6 pjSbsBankWrap" style="display: <?php echo @$FORM['payment_method'] != 'bank' ? 'none' : NULL; ?>">
						<div class="form-group">
							<label><?php __('front_bank_account')?></label>
							
							<div class="text-muted"><strong><?php echo !empty($tpl['option_arr']['o_bank_account']) ? nl2br(pjSanitize::html($tpl['option_arr']['o_bank_account'])) : ''; ?></strong></div>
						</div>
					</div><!-- /.col-sm-6 -->
				</div>
				
				<div class="row pjSbsCcWrap" style="display: <?php echo @$FORM['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php __('front_cc_type')?></label>
							
							<select name="cc_type" class="form-control required" data-msg-required="<?php __('pj_field_required'); ?>">
					    		<option value="">---</option>
					    		<?php
								foreach (__('cc_types', true) as $k => $v)
								{
									?><option value="<?php echo $k; ?>"<?php echo @$FORM['cc_type'] != $k ? NULL : ' selected="selected"'; ?>><?php echo $v; ?></option><?php
								}
								?>
					    	</select>
					    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php __('front_cc_num')?></label>
							
							<input type="text" name="cc_num" class="form-control required" value="<?php echo pjSanitize::html(@$FORM['cc_num']); ?>"  autocomplete="off" data-msg-required="<?php __('pj_field_required'); ?>"/>
					    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div>
					</div>
				</div>
				<div class="row pjSbsCcWrap" style="display: <?php echo @$FORM['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php __('front_cc_code')?></label>
							
							<input type="text" name="cc_code" class="form-control required" value="<?php echo pjSanitize::html(@$FORM['cc_code']); ?>"  autocomplete="off" data-msg-required="<?php __('pj_field_required'); ?>"/>
					    	<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php __('front_cc_exp')?></label>
							<div class="row">
								<div class="col-sm-7">
									<?php
									$rand = rand(1, 99999);
									$time = pjTime::factory()
										->attr('name', 'cc_exp_month')
										->attr('id', 'cc_exp_month_' . $rand)
										->attr('class', 'form-control required')
										->prop('format', 'F');
									if (isset($FORM['cc_exp_month']) && !is_null($FORM['cc_exp_month']))
									{
										$time->prop('selected', $FORM['cc_exp_month']);
									}
									echo $time->month();
									?>
								</div>
								<div class="col-sm-5">
									<?php
									$time = pjTime::factory()
										->attr('name', 'cc_exp_year')
										->attr('id', 'cc_exp_year_' . $rand)
										->attr('class', 'form-control required')
										->prop('left', 0)
										->prop('right', 10);
									if (isset($FORM['cc_exp_year']) && !is_null($FORM['cc_exp_year']))
									{
										$time->prop('selected', $FORM['cc_exp_year']);
									}
									echo $time->year();
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
			if (in_array((int) $tpl['option_arr']['o_bf_include_captcha'], array(3)))
			{
				?>
							
				<label><?php __('front_captcha'); ?> <span>*</span></label>
	
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<input type="text" name="captcha" class="form-control<?php echo (int) $tpl['option_arr']['o_bf_include_captcha'] === 3 ? ' required' : NULL; ?>" maxlength="6" autocomplete="off" data-msg-required="<?php __('pj_field_required'); ?>" data-msg-remote="<?php __('front_incorrect_captcha');?>">
  							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
					</div><!-- /.col-sm-6 -->
	
					<div class="col-sm-6">
						<div class="form-group">
							<img src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFrontEnd&amp;action=pjActionCaptcha&amp;rand=<?php echo rand(1, 99999); ?><?php echo isset($_GET['session_id']) ? '&session_id=' . $_GET['session_id'] : NULL;?>" alt="Captcha" style="vertical-align: middle" />
						</div><!-- /.form-group -->
					</div><!-- /.col-sm-6 -->
				</div><!-- /.row -->
				<?php
			}
			$terms = __('front_agree_with_terms', true);
			if(!empty($tpl['terms_conditions']))
			{
				$terms = str_replace("{STAG}", '<a href="#" class="pjTbModalTrigger" data-pj-toggle="modal" data-pj-target="#pjNcbTermModal" data-title="'.__('front_terms_title', true).'">', $terms);
				$terms = str_replace("{ETAG}", '</a>', $terms);
			}else{
				$terms = str_replace("{STAG}", '', $terms);
				$terms = str_replace("{ETAG}", '', $terms);
			} 
			?>

			<label><?php __('front_terms');?> <span>*</span></label>

			<div class="pjSbs-services-form-checkbox">
				<label>
					<input type="checkbox" name="terms" value="1" class="required" data-msg-required="<?php __('pj_field_required'); ?>">
			      	<?php echo $terms;?>
					<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
				</label>
			</div><!-- /.pjSbs-services-form-checkbox -->
		</div><!-- /.pjSbs-services-form -->

		<div class="pjSbs-services-footer pjSbs-services-footer-inline">
			<input type="submit" class="btn btn-primary" value="<?php __('front_btn_review')?>">
		</div><!-- /.pjSbs-services-footer -->
	</form>
</div><!-- /.pjSbs-services -->