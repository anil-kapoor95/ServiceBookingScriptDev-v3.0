START TRANSACTION;

-- 1) payment method enums
ALTER TABLE `bookings` MODIFY `payment_method` enum('paypal','authorize','creditcard','cash','bank','stripe') DEFAULT NULL;
ALTER TABLE `bookings_payments` MODIFY `payment_method` enum('paypal','authorize','creditcard','cash','bank','stripe') DEFAULT NULL;

-- 2) pjStripe plugin table (payment log / idempotency)
CREATE TABLE IF NOT EXISTS `plugin_stripe` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `foreign_id` int(10) unsigned DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `payment_intent` varchar(255) DEFAULT NULL,
  `event_id` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) unsigned DEFAULT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `dt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`),
  KEY `foreign_id` (`foreign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 3) options (Options > Bookings tab)
INSERT IGNORE INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_allow_stripe', 2, 'Yes|No::No', 'Yes|No', 'enum', 24, 1, NULL),
(1, 'o_stripe_secret_key', 2, '', NULL, 'string', 25, 1, NULL),
(1, 'o_stripe_api_key', 2, '', NULL, 'string', 26, 1, NULL);

-- 4) labels
INSERT IGNORE INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES
(NULL, 'payment_methods_ARRAY_stripe', 'arrays', 'payment_methods_ARRAY_stripe', 'script', NULL),
(NULL, 'opt_o_allow_stripe', 'backend', 'Options / Allow payments with Stripe', 'script', NULL),
(NULL, 'opt_o_stripe_secret_key', 'backend', 'Options / Stripe secret key', 'script', NULL),
(NULL, 'opt_o_stripe_api_key', 'backend', 'Options / Stripe public key', 'script', NULL),
(NULL, 'front_stripe_booking_made', 'frontend', 'Front / Stripe booking made', 'script', NULL),
(NULL, 'front_stripe_redirecting', 'frontend', 'Front / Stripe redirecting', 'script', NULL),
(NULL, 'front_stripe_pay_now', 'frontend', 'Front / Stripe pay now', 'script', NULL),
(NULL, 'front_stripe_late_payment', 'frontend', 'Message / Stripe payment for a cancelled booking', 'script', NULL),
(NULL, 'front_stripe_error', 'frontend', 'Front / Stripe error', 'script', NULL);

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Credit / debit card (Stripe)', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'payment_methods_ARRAY_stripe';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Allow payments with Stripe checkout', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'opt_o_allow_stripe';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Stripe secret key', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'opt_o_stripe_secret_key';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Stripe public key', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'opt_o_stripe_api_key';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Your booking has been made. You will be redirected to Stripe.', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'front_stripe_booking_made';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Redirecting you to Stripe secure payment page...', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'front_stripe_redirecting';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Pay now with Stripe', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'front_stripe_pay_now';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Your payment was received, but this booking had already been cancelled. Please contact us about your payment.', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'front_stripe_late_payment';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'The online payment could not be started. Your booking has been saved - please contact us to complete the payment.', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'front_stripe_error';

INSERT IGNORE INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES
(NULL, 'front_btn_confirming', 'frontend', 'Button / Confirming', 'script', NULL);

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Confirming...', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'front_btn_confirming';


UPDATE `multi_lang` SET `content` = 'URL for the web page where your clients will be redirected after PayPal, Stripe or Authorize.Net payment' WHERE `model` = 'pjField' AND `field` = 'title' AND `foreign_id` IN (SELECT `id` FROM `fields` WHERE `key` = 'opt_o_thankyou_page') AND `content` IN ('URL for the web page where your clients will be redirected after PayPal or Authorize.Net payment', 'URL for the web page where your clients will be redirected after PayPal or Authorize.net payment');

UPDATE `fields` SET `label` = 'Options / URL for the web page where your clients will be redirected after PayPal, Stripe or Authorize.net payment' WHERE `key` = 'opt_o_thankyou_page';

COMMIT;