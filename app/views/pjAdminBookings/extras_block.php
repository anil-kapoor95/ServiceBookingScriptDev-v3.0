<?php
/* Extras of a booking (add / edit forms). Ticked extras are added to the subtotal and the duration by pjAdminBookings.js */
if (!empty($tpl['extra_arr']))
{
	?>
	<p class="b5" style="margin-top: 10px;"><strong><?php __('lblExtras'); ?></strong></p>
	<table class="pj-table" style="width: 100%; margin-bottom: 10px;">
		<thead>
			<tr>
				<th><?php __('lblTitle');?></th>
				<th><?php __('lblDuration');?></th>
				<th><?php __('lblPrice');?></th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($tpl['extra_arr'] as $v)
			{
				?>
				<tr>
					<td><?php echo pjSanitize::html($v['title']);?></td>
					<td><?php echo (int) $v['duration']; ?> <?php __('lblMinutes');?></td>
					<td><?php echo pjUtil::formatCurrencySign($v['price'], $tpl['option_arr']['o_currency']);?></td>
					<td><input type="checkbox" id="extra_id_<?php echo $v['id'];?>" name="extra_id[<?php echo $v['id'];?>]" value="<?php echo $v['price'];?>"<?php echo !empty($v['booked']) ? ' checked="checked"' : NULL;?> data-duration="<?php echo (int) $v['duration'];?>" class="pjSbsExtraCheckbox"/></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	<?php
}
?>
