<?php
$url   = isset($tpl['arr']['url']) ? $tpl['arr']['url'] : '';
$error = isset($tpl['arr']['error']) ? $tpl['arr']['error'] : '';
$retry = isset($tpl['arr']['return_url']) ? $tpl['arr']['return_url'] : '';
$t = function ($key, $fallback) {
	$v = __($key, true);
	return (is_string($v) && $v !== '' && $v !== $key) ? $v : $fallback;
};
$sid = isset($tpl['arr']['session_id']) ? $tpl['arr']['session_id'] : '';
$pk  = isset($tpl['arr']['api_key']) ? $tpl['arr']['api_key'] : '';
$ok = ($url !== '' || ($sid !== '' && $pk !== ''));
if ($ok)
{
	?>
	<div class="pjSbs-stripe text-center" id="pjSbsStripeBox" style="padding: 20px 0;">
		<p class="text-muted" style="margin-bottom: 14px;"><?php echo pjSanitize::html($t('front_stripe_redirecting', 'Redirecting you to Stripe\'s secure payment page...')); ?></p>
		<a href="<?php echo $url !== '' ? pjSanitize::html($url) : '#'; ?>" class="btn btn-primary pjSbsStripeBtn" id="pjSbsStripeBtn"><?php echo pjSanitize::html($t('front_stripe_pay_now', 'Pay now with Stripe')); ?></a>
	</div>
	<script type="text/javascript">
	(function () {
		var url = <?php echo json_encode($url); ?>,
			sid = <?php echo json_encode($sid); ?>,
			pk = <?php echo json_encode($pk); ?>;
		function go() {
			if (url) {
				try { window.top.location.href = url; } catch (e) { window.location.href = url; }
				return;
			}
			// no hosted URL returned: redirect with Stripe.js and the public key
			var s = document.createElement('script');
			s.src = 'https://js.stripe.com/v3/';
			s.onload = function () { Stripe(pk).redirectToCheckout({sessionId: sid}); };
			document.head.appendChild(s);
		}
		setTimeout(go, 400);
		var btn = document.getElementById('pjSbsStripeBtn');
		if (btn) { btn.onclick = function (e) { if (!url) { e.preventDefault(); go(); } }; }
	})();
	</script>
	<?php
} else {
	?>
	<div class="pjSbs-stripe text-center" id="pjSbsStripeBox" style="padding: 20px 0;">
		<p class="text-danger" style="margin-bottom: 6px;"><strong><?php echo pjSanitize::html($t('front_stripe_error', 'The online payment could not be started. Your booking has been saved - please contact us to complete the payment.')); ?></strong></p>
	</div>
	<?php
}
?>
