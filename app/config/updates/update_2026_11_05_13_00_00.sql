START TRANSACTION;

CREATE TABLE IF NOT EXISTS `vouchers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `type` enum('amount','percent') NOT NULL DEFAULT 'amount',
  `discount` decimal(9,2) unsigned NOT NULL DEFAULT '0.00',
  `valid` enum('fixed','period','recurring') NOT NULL DEFAULT 'fixed',
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `time_from` time DEFAULT NULL,
  `time_to` time DEFAULT NULL,
  `every` enum('monday','tuesday','wednesday','thursday','friday','saturday','sunday') DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `bookings` ADD COLUMN `voucher_code` varchar(255) DEFAULT NULL AFTER `subtotal`;
ALTER TABLE `bookings` ADD COLUMN `discount` decimal(9,2) unsigned NOT NULL DEFAULT '0.00' AFTER `voucher_code`;

-- Labels (editable in the admin under Options > Languages / Labels, one value per language)
INSERT IGNORE INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES
(NULL, 'menuVouchers', 'backend', 'Menu / Vouchers', 'script', NULL),
(NULL, 'btnAddVoucher', 'backend', 'Button / Add voucher', 'script', NULL),
(NULL, 'infoVouchersTitle', 'backend', 'Infobox / Voucher list title', 'script', NULL),
(NULL, 'infoVouchersDesc', 'backend', 'Infobox / Voucher list description', 'script', NULL),
(NULL, 'infoAddVoucherTitle', 'backend', 'Infobox / Add voucher title', 'script', NULL),
(NULL, 'infoAddVoucherDesc', 'backend', 'Infobox / Add voucher description', 'script', NULL),
(NULL, 'infoUpdateVoucherTitle', 'backend', 'Infobox / Update voucher title', 'script', NULL),
(NULL, 'infoUpdateVoucherDesc', 'backend', 'Infobox / Update voucher description', 'script', NULL),
(NULL, 'infoVoucherAddedTitle', 'backend', 'Infobox / Voucher added title', 'script', NULL),
(NULL, 'infoVoucherAddedDesc', 'backend', 'Infobox / Voucher added description', 'script', NULL),
(NULL, 'infoVoucherUpdatedTitle', 'backend', 'Infobox / Voucher updated title', 'script', NULL),
(NULL, 'infoVoucherUpdatedDesc', 'backend', 'Infobox / Voucher updated description', 'script', NULL),
(NULL, 'infoVoucherFailedTitle', 'backend', 'Infobox / Voucher failed title', 'script', NULL),
(NULL, 'infoVoucherFailedDesc', 'backend', 'Infobox / Voucher failed description', 'script', NULL),
(NULL, 'infoVoucherExistsTitle', 'backend', 'Infobox / Voucher code exists title', 'script', NULL),
(NULL, 'infoVoucherExistsDesc', 'backend', 'Infobox / Voucher code exists description', 'script', NULL),
(NULL, 'infoVoucherNotFoundTitle', 'backend', 'Infobox / Voucher not found title', 'script', NULL),
(NULL, 'infoVoucherNotFoundDesc', 'backend', 'Infobox / Voucher not found description', 'script', NULL),
(NULL, 'lblVoucherCode', 'backend', 'Label / Voucher code', 'script', NULL),
(NULL, 'lblVoucherDiscount', 'backend', 'Label / Voucher discount', 'script', NULL),
(NULL, 'lblVoucherType', 'backend', 'Label / Voucher discount type', 'script', NULL),
(NULL, 'lblVoucherValid', 'backend', 'Label / Voucher valid', 'script', NULL),
(NULL, 'lblVoucherDate', 'backend', 'Label / Voucher date', 'script', NULL),
(NULL, 'lblVoucherDateFrom', 'backend', 'Label / Voucher date from', 'script', NULL),
(NULL, 'lblVoucherDateTo', 'backend', 'Label / Voucher date to', 'script', NULL),
(NULL, 'lblVoucherTimeFrom', 'backend', 'Label / Voucher time from', 'script', NULL),
(NULL, 'lblVoucherTimeTo', 'backend', 'Label / Voucher time to', 'script', NULL),
(NULL, 'lblVoucherEvery', 'backend', 'Label / Voucher every (weekday)', 'script', NULL),
(NULL, 'lblVoucherStartTime', 'backend', 'Label / Voucher start time', 'script', NULL),
(NULL, 'lblVoucherEndTime', 'backend', 'Label / Voucher end time', 'script', NULL),
(NULL, 'lblVoucherCodeExists', 'backend', 'Label / Voucher code exists (validation)', 'script', NULL),
(NULL, 'lblVoucherDiscountInvalid', 'backend', 'Label / Voucher discount over 100% (validation)', 'script', NULL),
(NULL, 'lblVoucherDiscountMin', 'backend', 'Label / Voucher discount must be above 0 (validation)', 'script', NULL),
(NULL, 'lblVoucherValidateTime', 'backend', 'Label / Voucher end time before start (validation)', 'script', NULL),
(NULL, 'lblVoucherValidateDateTime', 'backend', 'Label / Voucher end date before start (validation)', 'script', NULL),
(NULL, 'lblVoucherChoose', 'backend', 'Label / Voucher choose', 'script', NULL),
(NULL, 'lblBookingVoucherCode', 'backend', 'Label / Booking voucher code', 'script', NULL),
(NULL, 'lblBookingDiscount', 'backend', 'Label / Booking discount', 'script', NULL),
(NULL, 'lblBookingVoucherInvalid', 'backend', 'Label / Booking voucher not valid for the booking date', 'script', NULL),
(NULL, 'front_voucher', 'frontend', 'Front-end / Voucher (box title)', 'script', NULL),
(NULL, 'front_voucher_code', 'frontend', 'Front-end / Voucher code', 'script', NULL),
(NULL, 'front_voucher_toggle', 'frontend', 'Front-end / Voucher link (Theme 11)', 'script', NULL),
(NULL, 'front_btn_apply', 'frontend', 'Front-end / Apply button', 'script', NULL),
(NULL, 'front_btn_remove', 'frontend', 'Front-end / Remove button', 'script', NULL),
(NULL, 'front_discount', 'frontend', 'Front-end / Discount (summary line)', 'script', NULL),
(NULL, 'apply_voucher_msg_ARRAY_100', 'arrays', 'apply_voucher_msg_ARRAY_100', 'script', NULL),
(NULL, 'apply_voucher_msg_ARRAY_101', 'arrays', 'apply_voucher_msg_ARRAY_101', 'script', NULL),
(NULL, 'apply_voucher_msg_ARRAY_200', 'arrays', 'apply_voucher_msg_ARRAY_200', 'script', NULL),
(NULL, 'voucher_types_ARRAY_amount', 'arrays', 'voucher_types_ARRAY_amount', 'script', NULL),
(NULL, 'voucher_types_ARRAY_percent', 'arrays', 'voucher_types_ARRAY_percent', 'script', NULL),
(NULL, 'voucher_valids_ARRAY_fixed', 'arrays', 'voucher_valids_ARRAY_fixed', 'script', NULL),
(NULL, 'voucher_valids_ARRAY_period', 'arrays', 'voucher_valids_ARRAY_period', 'script', NULL),
(NULL, 'voucher_valids_ARRAY_recurring', 'arrays', 'voucher_valids_ARRAY_recurring', 'script', NULL);

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', t.`txt`, 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
INNER JOIN (
SELECT 'menuVouchers' AS `k`, 'Vouchers' AS `txt`
UNION ALL SELECT 'btnAddVoucher', '+ Add voucher'
UNION ALL SELECT 'infoVouchersTitle', 'Vouchers'
UNION ALL SELECT 'infoVouchersDesc', 'Vouchers give customers a discount on a booking when they type the code on the booking form. To add a voucher click on the "Add voucher" button. To edit or delete a voucher use the icons on the corresponding row.'
UNION ALL SELECT 'infoAddVoucherTitle', 'Add voucher'
UNION ALL SELECT 'infoAddVoucherDesc', 'Fill in the form below to add a voucher code and its discount. A voucher can be valid for a specific date, a day of the week or a date range. The date and time of the booking decide whether the voucher is accepted.'
UNION ALL SELECT 'infoUpdateVoucherTitle', 'Update voucher'
UNION ALL SELECT 'infoUpdateVoucherDesc', 'Make any changes you want in the form below and click Save.'
UNION ALL SELECT 'infoVoucherAddedTitle', 'Voucher added'
UNION ALL SELECT 'infoVoucherAddedDesc', 'A new voucher has been added to the list.'
UNION ALL SELECT 'infoVoucherUpdatedTitle', 'Voucher updated'
UNION ALL SELECT 'infoVoucherUpdatedDesc', 'All changes made to the voucher have been saved.'
UNION ALL SELECT 'infoVoucherFailedTitle', 'Voucher not saved'
UNION ALL SELECT 'infoVoucherFailedDesc', 'The voucher could not be saved. Please check the code, the discount and the validity dates and times, and try again.'
UNION ALL SELECT 'infoVoucherExistsTitle', 'Voucher code already used'
UNION ALL SELECT 'infoVoucherExistsDesc', 'Another voucher already has this code. Please choose a different code.'
UNION ALL SELECT 'infoVoucherNotFoundTitle', 'Voucher not found'
UNION ALL SELECT 'infoVoucherNotFoundDesc', 'The voucher you are looking for is missing.'
UNION ALL SELECT 'lblVoucherCode', 'Voucher code'
UNION ALL SELECT 'lblVoucherDiscount', 'Discount'
UNION ALL SELECT 'lblVoucherType', 'Type'
UNION ALL SELECT 'lblVoucherValid', 'Valid'
UNION ALL SELECT 'lblVoucherDate', 'Date'
UNION ALL SELECT 'lblVoucherDateFrom', 'Date from'
UNION ALL SELECT 'lblVoucherDateTo', 'Date to'
UNION ALL SELECT 'lblVoucherTimeFrom', 'Time from'
UNION ALL SELECT 'lblVoucherTimeTo', 'Time to'
UNION ALL SELECT 'lblVoucherEvery', 'Every'
UNION ALL SELECT 'lblVoucherStartTime', 'Start time'
UNION ALL SELECT 'lblVoucherEndTime', 'End time'
UNION ALL SELECT 'lblVoucherCodeExists', 'The voucher code is already used.'
UNION ALL SELECT 'lblVoucherDiscountInvalid', 'Discount can not exceed 100%.'
UNION ALL SELECT 'lblVoucherDiscountMin', 'Discount must be greater than 0.'
UNION ALL SELECT 'lblVoucherValidateTime', 'End time must be later than start time.'
UNION ALL SELECT 'lblVoucherValidateDateTime', 'End date and time must be later than start date and time.'
UNION ALL SELECT 'lblVoucherChoose', '-- Choose --'
UNION ALL SELECT 'lblBookingVoucherCode', 'Voucher code'
UNION ALL SELECT 'lblBookingDiscount', 'Discount'
UNION ALL SELECT 'lblBookingVoucherInvalid', 'This voucher code is not valid for the selected date and time.'
UNION ALL SELECT 'front_voucher', 'Voucher'
UNION ALL SELECT 'front_voucher_code', 'Voucher code'
UNION ALL SELECT 'front_voucher_toggle', 'Have a voucher code?'
UNION ALL SELECT 'front_btn_apply', 'Apply'
UNION ALL SELECT 'front_btn_remove', 'Remove'
UNION ALL SELECT 'front_discount', 'Discount'
UNION ALL SELECT 'apply_voucher_msg_ARRAY_100', 'Voucher code is missing.'
UNION ALL SELECT 'apply_voucher_msg_ARRAY_101', 'Voucher code is not valid.'
UNION ALL SELECT 'apply_voucher_msg_ARRAY_200', 'Voucher code is applied.'
UNION ALL SELECT 'voucher_types_ARRAY_amount', 'Amount'
UNION ALL SELECT 'voucher_types_ARRAY_percent', 'Percent'
UNION ALL SELECT 'voucher_valids_ARRAY_fixed', 'Fixed'
UNION ALL SELECT 'voucher_valids_ARRAY_period', 'Period'
UNION ALL SELECT 'voucher_valids_ARRAY_recurring', 'Recurring'
) t ON t.`k` = f.`key`;

-- e-mail tokens {VoucherCode} and {Discount} are listed in the "available tokens" help text
UPDATE `multi_lang` m
INNER JOIN `fields` f ON f.`id` = m.`foreign_id` AND m.`model` = 'pjField' AND m.`field` = 'title'
SET m.`content` = REPLACE(m.`content`, '<label>{Deposit}</label>', '<label>{Deposit}</label><label>{VoucherCode}</label><label>{Discount}</label>')
WHERE f.`key` = 'lblAvailableTokens' AND m.`content` NOT LIKE '%{VoucherCode}%';

-- New labels are cached in the session; changing the fields index makes every session reload them.
UPDATE `options` SET `value` = MD5(CONCAT(RAND(), UUID())) WHERE `key` = 'o_fields_index';

COMMIT;
