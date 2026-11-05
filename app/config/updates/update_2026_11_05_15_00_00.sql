START TRANSACTION;

INSERT IGNORE INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'lblBookingVoucherNeedData', 'backend', 'Label / Booking voucher needs services, date and time', 'script', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'lblBookingVoucherNeedData');
INSERT IGNORE INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
SELECT NULL, 'lblBookingVoucherCapped', 'backend', 'Label / Booking voucher discount limited to the sub-total', 'script', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `fields` WHERE `key` = 'lblBookingVoucherCapped');

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', t.`txt`, 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
INNER JOIN (
SELECT 'lblBookingVoucherNeedData' AS `k`, 'Select the service(s), date and time first, then the voucher can be applied.' AS `txt`
UNION ALL SELECT 'lblBookingVoucherCapped', 'The discount is limited to the sub-total.'
) t ON t.`k` = f.`key`
WHERE NOT EXISTS (SELECT 1 FROM `multi_lang` m WHERE m.`foreign_id` = f.`id` AND m.`model` = 'pjField' AND m.`locale` = l.`locale` AND m.`field` = 'title');

-- labels are cached in the session; a new fields index makes every session reload them
UPDATE `options` SET `value` = MD5(CONCAT(RAND(), UUID())) WHERE `key` = 'o_fields_index';

COMMIT;
