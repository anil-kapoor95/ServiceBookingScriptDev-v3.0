START TRANSACTION;

ALTER TABLE `services` ADD COLUMN `sort_order` int(10) unsigned NOT NULL DEFAULT 0 AFTER `category_id`;

UPDATE `services` SET `sort_order` = `id` WHERE `sort_order` = 0;

INSERT IGNORE INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES
(NULL, 'lblOrder', 'backend', 'Label / Order', 'script', NULL),
(NULL, 'lblOrderHint', 'backend', 'Label / Order hint', 'script', NULL);

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', t.`txt`, 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
INNER JOIN (
SELECT 'lblOrder' AS `k`, 'Order' AS `txt`
UNION ALL SELECT 'lblOrderHint', 'Services are shown in this order (1 = first). Leave empty to put it last.'
) t ON t.`k` = f.`key`;

COMMIT;