<?php
/**
 * Theme 11 segmented step tracker.
 * Expects $pjSbsStep (1-4) to be set by the including view, or $pjSbsAllDone = true
 * to render every step as completed (used on the final "booking submitted" screen).
 *
 * The 4 labels come from the script's own translation fields
 * (front_step_service / front_step_time / front_step_details /
 * front_step_confirm - see app/config/updates/theme11_dynamic_step_labels_*.sql),
 * so they follow the site's configured language and can be edited from the
 * admin like every other front-end label, instead of being hard-coded
 * English text here. The "?: " fallback only matters before that SQL has
 * been run (or on an older install missing the fields), so the tracker
 * never renders blank labels in the meantime.
 */
$pjSbsStepLabels = array(
	1 => __('front_step_service', true) ?: 'Service',
	2 => __('front_step_time', true) ?: 'Time',
	3 => __('front_step_details', true) ?: 'Details',
	4 => __('front_step_confirm', true) ?: 'Confirm',
);
/**
 * One universal line icon per step (inline SVG, drawn with "currentColor" so
 * the stylesheet / admin colour swatches recolour them). They are deliberately
 * business-agnostic so they suit any kind of service:
 *   1 Service = concierge bell, 2 Time = calendar + clock,
 *   3 Details = person, 4 Confirm = check in a circle.
 */
$pjSbsStepIcons = array(
	1 => '<rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/>',
	2 => '<path d="M16 14v2.2l1.6 1"/><path d="M16 2v4"/><path d="M21 7.5V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3.5"/><path d="M3 10h5"/><path d="M8 2v4"/><circle cx="16" cy="16" r="6"/>',
	3 => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
	4 => '<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>',
);
$pjSbsCurrentStep = isset($pjSbsStep) ? (int) $pjSbsStep : 1;
$pjSbsDoneAll = !empty($pjSbsAllDone);
?>
<div class="pjSbs11-steps">
	<?php for ($i = 1; $i <= 4; $i++) {
		$state = '';
		if ($pjSbsDoneAll || $i < $pjSbsCurrentStep) {
			$state = ' pjSbs11-step-done';
		} else if ($i == $pjSbsCurrentStep) {
			$state = ' pjSbs11-step-current';
		}
		?>
		<div class="pjSbs11-step<?php echo $state; ?>">
			<span class="pjSbs11-step-bar"></span>
			<span class="pjSbs11-step-icon"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><?php echo $pjSbsStepIcons[$i]; ?></svg></span>
			<span class="pjSbs11-step-label"><?php echo $pjSbsStepLabels[$i]; ?></span>
		</div>
		<?php
	} ?>
</div><!-- /.pjSbs11-steps -->
