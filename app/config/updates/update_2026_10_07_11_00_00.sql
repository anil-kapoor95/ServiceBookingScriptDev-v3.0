START TRANSACTION;

-- CREATE TABLE IF NOT EXISTS `service_categories` (
--   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
--   `status` enum('T','F') NOT NULL DEFAULT 'T',
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- CREATE TABLE IF NOT EXISTS `extras` (
--   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
--   `price` decimal(9,2) DEFAULT NULL,
--   `duration` int(10) NOT NULL DEFAULT '0',
--   `status` enum('T','F') NOT NULL DEFAULT 'T',
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- CREATE TABLE IF NOT EXISTS `services_extras` (
--   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
--   `service_id` int(10) unsigned DEFAULT NULL,
--   `extra_id` int(10) unsigned DEFAULT NULL,
--   PRIMARY KEY (`id`),
--   UNIQUE KEY `service_extra` (`service_id`,`extra_id`),
--   KEY `extra_id` (`extra_id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- CREATE TABLE IF NOT EXISTS `bookings_extras` (
--   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
--   `booking_id` int(10) unsigned DEFAULT NULL,
--   `extra_id` int(10) unsigned DEFAULT NULL,
--   `title` varchar(255) DEFAULT NULL,
--   `price` decimal(9,2) DEFAULT NULL,
--   `duration` int(10) NOT NULL DEFAULT '0',
--   PRIMARY KEY (`id`),
--   UNIQUE KEY `booking_extra` (`booking_id`,`extra_id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ALTER TABLE `services` ADD COLUMN `category_id` int(10) unsigned DEFAULT NULL AFTER `id`, ADD KEY `category_id` (`category_id`);

-- -- default category for the services that already exist (only when no category exists yet)
-- INSERT INTO `service_categories` (`id`, `status`)
-- SELECT NULL, 'T' FROM `fields` WHERE `key` = 'front_btn_confirming'
-- AND NOT EXISTS (SELECT 1 FROM `service_categories`);

-- INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
-- SELECT NULL, c.`id`, 'pjServiceCategory', l.`locale`, 'title', 'General', 'data'
-- FROM `service_categories` c
-- CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
-- WHERE c.`id` = (SELECT MIN(`id`) FROM `service_categories`);

-- UPDATE `services` SET `category_id` = (SELECT MIN(`id`) FROM `service_categories`) WHERE `category_id` IS NULL;

-- -- labels
-- INSERT IGNORE INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES
-- (NULL, 'menuCategories', 'backend', 'Menu / Categories', 'script', NULL),
-- (NULL, 'menuExtras', 'backend', 'Menu / Extras', 'script', NULL),
-- (NULL, 'lblCategory', 'backend', 'Label / Category', 'script', NULL),
-- (NULL, 'lblExtras', 'backend', 'Label / Extras', 'script', NULL),
-- (NULL, 'lblServicesCount', 'backend', 'Label / Services (count column)', 'script', NULL),
-- (NULL, 'lblAllCategories', 'backend', 'Label / All categories', 'script', NULL),
-- (NULL, 'lblNoCategoriesYet', 'backend', 'Label / No categories yet', 'script', NULL),
-- (NULL, 'lblNoExtrasYet', 'backend', 'Label / No extras yet', 'script', NULL),
-- (NULL, 'lblExtrasHint', 'backend', 'Label / Extras hint on the service form', 'script', NULL),
-- (NULL, 'btnAddCategory', 'backend', 'Button / Add category', 'script', NULL),
-- (NULL, 'btnAddExtra', 'backend', 'Button / Add extra', 'script', NULL),
-- (NULL, 'infoCategoriesTitle', 'backend', 'Infobox / List of categories', 'script', NULL),
-- (NULL, 'infoCategoriesDesc', 'backend', 'Infobox / List of categories (text)', 'script', NULL),
-- (NULL, 'infoAddCategoryTitle', 'backend', 'Infobox / Add category', 'script', NULL),
-- (NULL, 'infoAddCategoryDesc', 'backend', 'Infobox / Add category (text)', 'script', NULL),
-- (NULL, 'infoUpdateCategoryTitle', 'backend', 'Infobox / Update category', 'script', NULL),
-- (NULL, 'infoUpdateCategoryDesc', 'backend', 'Infobox / Update category (text)', 'script', NULL),
-- (NULL, 'infoExtrasTitle', 'backend', 'Infobox / List of extras', 'script', NULL),
-- (NULL, 'infoExtrasDesc', 'backend', 'Infobox / List of extras (text)', 'script', NULL),
-- (NULL, 'infoAddExtraTitle', 'backend', 'Infobox / Add extra', 'script', NULL),
-- (NULL, 'infoAddExtraDesc', 'backend', 'Infobox / Add extra (text)', 'script', NULL),
-- (NULL, 'infoUpdateExtraTitle', 'backend', 'Infobox / Update extra', 'script', NULL),
-- (NULL, 'infoUpdateExtraDesc', 'backend', 'Infobox / Update extra (text)', 'script', NULL),
-- (NULL, 'infoCategoryAddedTitle', 'backend', 'Infobox / Category added', 'script', NULL),
-- (NULL, 'infoCategoryAddedDesc', 'backend', 'Infobox / Category added (text)', 'script', NULL),
-- (NULL, 'infoCategoryUpdatedTitle', 'backend', 'Infobox / Category updated', 'script', NULL),
-- (NULL, 'infoCategoryUpdatedDesc', 'backend', 'Infobox / Category updated (text)', 'script', NULL),
-- (NULL, 'infoCategoryFailedTitle', 'backend', 'Infobox / Category not saved', 'script', NULL),
-- (NULL, 'infoCategoryFailedDesc', 'backend', 'Infobox / Category not saved (text)', 'script', NULL),
-- (NULL, 'infoExtraAddedTitle', 'backend', 'Infobox / Extra added', 'script', NULL),
-- (NULL, 'infoExtraAddedDesc', 'backend', 'Infobox / Extra added (text)', 'script', NULL),
-- (NULL, 'infoExtraUpdatedTitle', 'backend', 'Infobox / Extra updated', 'script', NULL),
-- (NULL, 'infoExtraUpdatedDesc', 'backend', 'Infobox / Extra updated (text)', 'script', NULL),
-- (NULL, 'infoExtraFailedTitle', 'backend', 'Infobox / Extra not saved', 'script', NULL),
-- (NULL, 'infoExtraFailedDesc', 'backend', 'Infobox / Extra not saved (text)', 'script', NULL),
-- (NULL, 'msgCategoryInUse', 'backend', 'Message / Category still has services', 'script', NULL),
-- (NULL, 'front_all_categories', 'frontend', 'Front-end / Category filter - All', 'script', NULL),
-- (NULL, 'front_extras', 'frontend', 'Front-end / Extras heading', 'script', NULL),
-- (NULL, 'front_extra', 'frontend', 'Front-end / Extra (summary line prefix)', 'script', NULL),
-- (NULL, 'front_extra_selected', 'frontend', 'Front-end / extra (selected count, singular)', 'script', NULL),
-- (NULL, 'front_extras_selected', 'frontend', 'Front-end / extras (selected count, plural)', 'script', NULL);

-- INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
-- SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', t.`txt`, 'script'
-- FROM `fields` f
-- CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
-- INNER JOIN (
-- SELECT 'menuCategories' AS `k`, 'Categories' AS `txt`
-- UNION ALL SELECT 'menuExtras', 'Extras'
-- UNION ALL SELECT 'lblCategory', 'Category'
-- UNION ALL SELECT 'lblExtras', 'Extras'
-- UNION ALL SELECT 'lblServicesCount', 'Services'
-- UNION ALL SELECT 'lblAllCategories', 'All categories'
-- UNION ALL SELECT 'lblNoCategoriesYet', 'No categories yet - add one first'
-- UNION ALL SELECT 'lblNoExtrasYet', 'No extras yet - add one first'
-- UNION ALL SELECT 'lblExtrasHint', 'Tick the extras customers can add to this service.'
-- UNION ALL SELECT 'btnAddCategory', '+ Add category'
-- UNION ALL SELECT 'btnAddExtra', '+ Add extra'
-- UNION ALL SELECT 'infoCategoriesTitle', 'List of categories'
-- UNION ALL SELECT 'infoCategoriesDesc', 'Group your services into categories. Customers can filter the services by category on the booking form. A category that still has services cannot be deleted.'
-- UNION ALL SELECT 'infoAddCategoryTitle', 'Add category'
-- UNION ALL SELECT 'infoAddCategoryDesc', 'Fill in the form below and click "Save" to add a new category.'
-- UNION ALL SELECT 'infoUpdateCategoryTitle', 'Update category'
-- UNION ALL SELECT 'infoUpdateCategoryDesc', 'Update the form below and click "Save" to change the category.'
-- UNION ALL SELECT 'infoExtrasTitle', 'List of extras'
-- UNION ALL SELECT 'infoExtrasDesc', 'Extras are optional add-ons customers can pick together with a service. Create them here, then tick the ones that apply on each service.'
-- UNION ALL SELECT 'infoAddExtraTitle', 'Add extra'
-- UNION ALL SELECT 'infoAddExtraDesc', 'Fill in the form below and click "Save" to add a new extra. Its price and duration are added to the booking.'
-- UNION ALL SELECT 'infoUpdateExtraTitle', 'Update extra'
-- UNION ALL SELECT 'infoUpdateExtraDesc', 'Update the form below and click "Save" to change the extra.'
-- UNION ALL SELECT 'infoCategoryAddedTitle', 'Category added!'
-- UNION ALL SELECT 'infoCategoryAddedDesc', 'The new category has been added.'
-- UNION ALL SELECT 'infoCategoryUpdatedTitle', 'Category updated!'
-- UNION ALL SELECT 'infoCategoryUpdatedDesc', 'The category has been updated.'
-- UNION ALL SELECT 'infoCategoryFailedTitle', 'Category not saved!'
-- UNION ALL SELECT 'infoCategoryFailedDesc', 'The category could not be saved or was not found.'
-- UNION ALL SELECT 'infoExtraAddedTitle', 'Extra added!'
-- UNION ALL SELECT 'infoExtraAddedDesc', 'The new extra has been added.'
-- UNION ALL SELECT 'infoExtraUpdatedTitle', 'Extra updated!'
-- UNION ALL SELECT 'infoExtraUpdatedDesc', 'The extra has been updated.'
-- UNION ALL SELECT 'infoExtraFailedTitle', 'Extra not saved!'
-- UNION ALL SELECT 'infoExtraFailedDesc', 'The extra could not be saved or was not found.'
-- UNION ALL SELECT 'msgCategoryInUse', 'This category still has services. Move or delete those services first.'
-- UNION ALL SELECT 'front_all_categories', 'All'
-- UNION ALL SELECT 'front_extras', 'Extras'
-- UNION ALL SELECT 'front_extra', 'Extra'
-- UNION ALL SELECT 'front_extra_selected', 'extra'
-- UNION ALL SELECT 'front_extras_selected', 'extras'
-- ) t ON t.`k` = f.`key`;

-- INSERT IGNORE INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES
-- (NULL, 'front_select_extras', 'frontend', 'Front-end / Extras step title', 'script', NULL),
-- (NULL, 'front_btn_choose_extras', 'frontend', 'Button / Choose Extras', 'script', NULL);

-- INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
-- SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', t.`txt`, 'script'
-- FROM `fields` f
-- CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
-- INNER JOIN (
-- SELECT 'front_select_extras' AS `k`, 'Select Extras' AS `txt`
-- UNION ALL SELECT 'front_btn_choose_extras', 'Choose Extras'
-- ) t ON t.`k` = f.`key`;

-- INSERT IGNORE INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES
-- (NULL, 'front_select_extras', 'frontend', 'Front-end / Extras step title', 'script', NULL),
-- (NULL, 'front_btn_choose_extras', 'frontend', 'Button / Choose Extras', 'script', NULL),
-- (NULL, 'front_step_extras', 'frontend', 'Front-end / Progress step - Extras', 'script', NULL);

-- INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
-- SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', t.`txt`, 'script'
-- FROM `fields` f
-- CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
-- INNER JOIN (
-- SELECT 'front_select_extras' AS `k`, 'Select Extras' AS `txt`
-- UNION ALL SELECT 'front_btn_choose_extras', 'Choose Extras'
-- UNION ALL SELECT 'front_step_extras', 'Extras'
-- ) t ON t.`k` = f.`key`;

-- INSERT IGNORE INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES
-- (NULL, 'front_select_extras', 'frontend', 'Front-end / Extras step title', 'script', NULL),
-- (NULL, 'front_btn_choose_extras', 'frontend', 'Button / Choose Extras', 'script', NULL),
-- (NULL, 'front_step_extras', 'frontend', 'Front-end / Progress step - Extras', 'script', NULL),
-- (NULL, 'lblSelectExtras', 'backend', 'Label / Select extras (dropdown placeholder)', 'script', NULL);

-- INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
-- SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', t.`txt`, 'script'
-- FROM `fields` f
-- CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
-- INNER JOIN (
-- SELECT 'front_select_extras' AS `k`, 'Select Extras' AS `txt`
-- UNION ALL SELECT 'front_btn_choose_extras', 'Choose Extras'
-- UNION ALL SELECT 'front_step_extras', 'Extras'
-- UNION ALL SELECT 'lblSelectExtras', 'Select extras'
-- ) t ON t.`k` = f.`key`;

INSERT IGNORE INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES
(NULL, 'front_select_extras', 'frontend', 'Front-end / Extras step title', 'script', NULL),
(NULL, 'front_btn_choose_extras', 'frontend', 'Button / Choose Extras', 'script', NULL),
(NULL, 'front_step_extras', 'frontend', 'Front-end / Progress step - Extras', 'script', NULL),
(NULL, 'lblSelectExtras', 'backend', 'Label / Select extras (dropdown placeholder)', 'script', NULL),
(NULL, 'lblCannotDelete', 'backend', 'Label / Cannot delete (popup title)', 'script', NULL),
(NULL, 'btnOk', 'backend', 'Button OK', 'script', NULL);

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', t.`txt`, 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
INNER JOIN (
SELECT 'front_select_extras' AS `k`, 'Select Extras' AS `txt`
UNION ALL SELECT 'front_btn_choose_extras', 'Choose Extras'
UNION ALL SELECT 'front_step_extras', 'Extras'
UNION ALL SELECT 'lblSelectExtras', 'Select extras'
UNION ALL SELECT 'lblCannotDelete', 'Cannot delete'
UNION ALL SELECT 'btnOk', 'OK'
) t ON t.`k` = f.`key`;

COMMIT;