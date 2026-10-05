START TRANSACTION;

INSERT IGNORE INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES
(NULL, 't11_legend', 'backend', 'Label / Theme 11 colours - t11_legend', 'script', NULL),
(NULL, 't11_quick_palettes', 'backend', 'Label / Theme 11 colours - t11_quick_palettes', 'script', NULL),
(NULL, 't11_tab_general', 'backend', 'Label / Theme 11 colours - t11_tab_general', 'script', NULL),
(NULL, 't11_tab_fonts', 'backend', 'Label / Theme 11 colours - t11_tab_fonts', 'script', NULL),
(NULL, 't11_tab_buttons', 'backend', 'Label / Theme 11 colours - t11_tab_buttons', 'script', NULL),
(NULL, 't11_tab_list', 'backend', 'Label / Theme 11 colours - t11_tab_list', 'script', NULL),
(NULL, 't11_lbl_header', 'backend', 'Label / Theme 11 colours - t11_lbl_header', 'script', NULL),
(NULL, 't11_lbl_page', 'backend', 'Label / Theme 11 colours - t11_lbl_page', 'script', NULL),
(NULL, 't11_lbl_card', 'backend', 'Label / Theme 11 colours - t11_lbl_card', 'script', NULL),
(NULL, 't11_lbl_text_heading', 'backend', 'Label / Theme 11 colours - t11_lbl_text_heading', 'script', NULL),
(NULL, 't11_lbl_text_body', 'backend', 'Label / Theme 11 colours - t11_lbl_text_body', 'script', NULL),
(NULL, 't11_lbl_text_accent', 'backend', 'Label / Theme 11 colours - t11_lbl_text_accent', 'script', NULL),
(NULL, 't11_lbl_button', 'backend', 'Label / Theme 11 colours - t11_lbl_button', 'script', NULL),
(NULL, 't11_lbl_button_hover', 'backend', 'Label / Theme 11 colours - t11_lbl_button_hover', 'script', NULL),
(NULL, 't11_lbl_list_active', 'backend', 'Label / Theme 11 colours - t11_lbl_list_active', 'script', NULL),
(NULL, 't11_lbl_list_inactive', 'backend', 'Label / Theme 11 colours - t11_lbl_list_inactive', 'script', NULL),
(NULL, 't11_hint_header', 'backend', 'Label / Theme 11 colours - t11_hint_header', 'script', NULL),
(NULL, 't11_hint_page', 'backend', 'Label / Theme 11 colours - t11_hint_page', 'script', NULL),
(NULL, 't11_hint_card', 'backend', 'Label / Theme 11 colours - t11_hint_card', 'script', NULL),
(NULL, 't11_hint_text_heading', 'backend', 'Label / Theme 11 colours - t11_hint_text_heading', 'script', NULL),
(NULL, 't11_hint_text_body', 'backend', 'Label / Theme 11 colours - t11_hint_text_body', 'script', NULL),
(NULL, 't11_hint_text_accent', 'backend', 'Label / Theme 11 colours - t11_hint_text_accent', 'script', NULL),
(NULL, 't11_hint_button', 'backend', 'Label / Theme 11 colours - t11_hint_button', 'script', NULL),
(NULL, 't11_hint_button_hover', 'backend', 'Label / Theme 11 colours - t11_hint_button_hover', 'script', NULL),
(NULL, 't11_hint_list_active', 'backend', 'Label / Theme 11 colours - t11_hint_list_active', 'script', NULL),
(NULL, 't11_hint_list_inactive', 'backend', 'Label / Theme 11 colours - t11_hint_list_inactive', 'script', NULL),
(NULL, 't11_click_to_change', 'backend', 'Label / Theme 11 colours - t11_click_to_change', 'script', NULL),
(NULL, 't11_btn_save', 'backend', 'Label / Theme 11 colours - t11_btn_save', 'script', NULL),
(NULL, 't11_btn_reset', 'backend', 'Label / Theme 11 colours - t11_btn_reset', 'script', NULL),
(NULL, 't11_note', 'backend', 'Label / Theme 11 colours - t11_note', 'script', NULL),
(NULL, 't11_live_preview', 'backend', 'Label / Theme 11 colours - t11_live_preview', 'script', NULL),
(NULL, 't11_live_preview_badge', 'backend', 'Label / Theme 11 colours - t11_live_preview_badge', 'script', NULL),
(NULL, 't11_read_good', 'backend', 'Label / Theme 11 colours - t11_read_good', 'script', NULL),
(NULL, 't11_read_low', 'backend', 'Label / Theme 11 colours - t11_read_low', 'script', NULL),
(NULL, 't11_read_poor', 'backend', 'Label / Theme 11 colours - t11_read_poor', 'script', NULL),
(NULL, 't11_read_title', 'backend', 'Label / Theme 11 colours - t11_read_title', 'script', NULL),
(NULL, 't11_pal_sage', 'backend', 'Label / Theme 11 colours - t11_pal_sage', 'script', NULL),
(NULL, 't11_pal_ocean', 'backend', 'Label / Theme 11 colours - t11_pal_ocean', 'script', NULL),
(NULL, 't11_pal_lavender', 'backend', 'Label / Theme 11 colours - t11_pal_lavender', 'script', NULL),
(NULL, 't11_pal_coral', 'backend', 'Label / Theme 11 colours - t11_pal_coral', 'script', NULL),
(NULL, 't11_pal_forest', 'backend', 'Label / Theme 11 colours - t11_pal_forest', 'script', NULL),
(NULL, 't11_pal_gold', 'backend', 'Label / Theme 11 colours - t11_pal_gold', 'script', NULL),
(NULL, 't11_pal_rose', 'backend', 'Label / Theme 11 colours - t11_pal_rose', 'script', NULL),
(NULL, 't11_pal_mint', 'backend', 'Label / Theme 11 colours - t11_pal_mint', 'script', NULL);

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Theme 11 colours', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_legend';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Quick palettes', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_quick_palettes';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'General', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_tab_general';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Fonts', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_tab_fonts';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Buttons', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_tab_buttons';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'List', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_tab_list';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Header / accent colour', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_lbl_header';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Page background', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_lbl_page';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Card background', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_lbl_card';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Heading text colour', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_lbl_text_heading';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Body / description text colour', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_lbl_text_body';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Accent text colour (duration, price, links)', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_lbl_text_accent';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Button colour', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_lbl_button';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Button hover colour', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_lbl_button_hover';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Active / selected colour', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_lbl_list_active';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Inactive colour', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_lbl_list_inactive';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Step bar, icons, links, active elements', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_hint_header';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Behind the whole booking widget', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_hint_page';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Service cards and summary cards', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_hint_card';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Service names and titles', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_hint_text_heading';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Descriptions and small labels', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_hint_text_body';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Duration, price and links', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_hint_text_accent';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Main buttons (white text)', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_hint_button';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'When the mouse is over a button', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_hint_button_hover';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Chosen service, date and time', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_hint_list_active';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Unselected icon circles and step bars', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_hint_list_inactive';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'click to change', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_click_to_change';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Save colours', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_btn_save';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Reset to default', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_btn_reset';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Picking a quick palette or changing a colour only previews it here. Press “Save colours” to apply it to the booking widget. The readability badge shows text contrast (4.5 or higher is good).', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_note';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Live preview', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_live_preview';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'updates as you edit', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_live_preview_badge';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Good', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_read_good';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Low', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_read_low';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Poor', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_read_poor';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Readability (WCAG): 4.5 or higher is good', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_read_title';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Sage', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_pal_sage';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Ocean Breeze', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_pal_ocean';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Lavender Dream', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_pal_lavender';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Sunset Coral (default)', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_pal_coral';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Forest Emerald', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_pal_forest';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Midnight Gold', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_pal_gold';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Rose Blush', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_pal_rose';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Aqua Mint', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 't11_pal_mint';


INSERT IGNORE INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES
(NULL, 'pj_greater_than_zero', 'backend', 'Validation / pj greater than zero', 'script', NULL),
(NULL, 'pj_phone_validation', 'backend', 'Validation / pj phone validation', 'script', NULL);

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Please enter a value greater than 0.', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'pj_greater_than_zero';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Please enter a valid phone number.', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'pj_phone_validation';

COMMIT;