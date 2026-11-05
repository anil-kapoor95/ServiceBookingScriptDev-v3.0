-- Vouchers: limit a voucher to selected services (or leave it for all services).
-- Run after update_2026_11_05_13_00_00.sql.
START TRANSACTION;

CREATE TABLE IF NOT EXISTS `vouchers_services` (
  `voucher_id` int(10) unsigned NOT NULL,
  `service_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`voucher_id`,`service_id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Labels (editable in the admin under Options > Languages / Labels, one value per language)
INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, n.`k`, n.`t`, n.`l`, 'script', NULL
FROM (
SELECT 'lblVoucherServices' AS `k`, 'backend' AS `t`, 'Label / Voucher services' AS `l`
UNION ALL SELECT 'lblVoucherServicesAll', 'backend', 'Label / Voucher valid for all services'
UNION ALL SELECT 'lblVoucherServicesSelected', 'backend', 'Label / Voucher valid for selected services only'
UNION ALL SELECT 'lblVoucherServicesHint', 'backend', 'Label / Voucher services hint'
UNION ALL SELECT 'lblVoucherServicesRequired', 'backend', 'Label / Voucher select at least one service'
UNION ALL SELECT 'lblVoucherServicesNone', 'backend', 'Label / Voucher services dropdown placeholder'
UNION ALL SELECT 'lblBookingVoucherService', 'backend', 'Label / Booking voucher not valid for the selected services'
UNION ALL SELECT 'apply_voucher_msg_ARRAY_103', 'arrays', 'apply_voucher_msg_ARRAY_103'
) n
WHERE NOT EXISTS (SELECT 1 FROM `fields` x WHERE x.`key` = n.`k`);

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', t.`txt`, 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
INNER JOIN (
SELECT 'lblVoucherServices' AS `k`, 'Services' AS `txt`
UNION ALL SELECT 'lblVoucherServicesAll', 'All services'
UNION ALL SELECT 'lblVoucherServicesSelected', 'Only selected services'
UNION ALL SELECT 'lblVoucherServicesHint', 'The discount is taken off the price of the selected services only (extras are not discounted). Choose "All services" to discount the whole booking.'
UNION ALL SELECT 'lblVoucherServicesRequired', 'Select at least one service.'
UNION ALL SELECT 'lblVoucherServicesNone', '-- Select services --'
UNION ALL SELECT 'lblBookingVoucherService', 'This voucher is not valid for the selected service(s).'
UNION ALL SELECT 'apply_voucher_msg_ARRAY_103', 'This voucher is not valid for the selected service(s).'
) t ON t.`k` = f.`key`
WHERE NOT EXISTS (SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = f.`id` AND m.`model` = 'pjField' AND m.`locale` = l.`locale` AND m.`field` = 'title');

-- labels are cached in the session; a new fields index makes every session reload them
UPDATE `options` SET `value` = MD5(CONCAT(RAND(), UUID())) WHERE `key` = 'o_fields_index';

COMMIT;
