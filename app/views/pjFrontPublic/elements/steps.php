<?php
/**
 * Theme 11 segmented step tracker.
 * Expects $pjSbsStep (1-4, or 'extras') to be set by the including view, or $pjSbsAllDone = true
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
 *   1 Service = clipboard list, 2 Time = calendar + clock,
 *   3 Details = person, 4 Confirm = check in a circle,
 *   extras = plus sign in a circle.
 */
$pjSbsStepIcons = array(
	1 => '<rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/>',
	2 => '<path d="M16 14v2.2l1.6 1"/><path d="M16 2v4"/><path d="M21 7.5V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3.5"/><path d="M3 10h5"/><path d="M8 2v4"/><circle cx="16" cy="16" r="6"/>',
	3 => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
	4 => '<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>',
	'extras' => '<circle cx="12" cy="12" r="10"/><path d="M12 8v8"/><path d="M8 12h8"/>',
);
$pjSbsCurrentStep = isset($pjSbsStep) ? $pjSbsStep : 1;
$pjSbsDoneAll = !empty($pjSbsAllDone);

/**
 * "Extras" is an extra step of the tracker (label front_step_extras) as soon as the
 * site offers at least one active extra on an active service. The step is only
 * visited when the chosen services have extras; otherwise it is shown as passed.
 */
$pjSbsExtrasStep = false;
if (class_exists('pjExtraModel') && class_exists('pjServiceExtraModel'))
{
	$pjSbsExtrasStep = pjExtraModel::factory()
		->where('t1.status', 'T')
		->where("(t1.id IN (SELECT `TSE`.extra_id FROM `".pjServiceExtraModel::factory()->getTable()."` AS `TSE` INNER JOIN `".pjServiceModel::factory()->getTable()."` AS `TSS` ON `TSS`.id = `TSE`.service_id AND `TSS`.status = 'T'))")
		->findCount()->getData() > 0;
}
$pjSbsStepList = array(1);
if ($pjSbsExtrasStep)
{
	$pjSbsStepList[] = 'extras';
	$pjSbsStepLabels['extras'] = __('front_step_extras', true) ?: 'Extras';
}
$pjSbsStepList[] = 2;
$pjSbsStepList[] = 3;
$pjSbsStepList[] = 4;
// position (1-based) of the current step inside the list
$pjSbsCurrentPos = array_search($pjSbsCurrentStep, $pjSbsStepList, true);
$pjSbsCurrentPos = $pjSbsCurrentPos === false ? 1 : $pjSbsCurrentPos + 1;
?>
<div class="pjSbs11-steps<?php echo count($pjSbsStepList) > 4 ? ' pjSbs11-steps-5' : NULL; ?>">
	<?php foreach ($pjSbsStepList as $pos => $key) {
		$i = $pos + 1;
		$state = '';
		if ($pjSbsDoneAll || $i < $pjSbsCurrentPos) {
			$state = ' pjSbs11-step-done';
		} else if ($i == $pjSbsCurrentPos) {
			$state = ' pjSbs11-step-current';
		}
		?>
		<div class="pjSbs11-step<?php echo $state; ?>">
			<span class="pjSbs11-step-bar"></span>
			<span class="pjSbs11-step-icon"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><?php echo $pjSbsStepIcons[$key]; ?></svg></span>
			<span class="pjSbs11-step-label"><?php echo $pjSbsStepLabels[$key]; ?></span>
		</div>
		<?php
	} ?>
</div><!-- /.pjSbs11-steps -->
