START TRANSACTION;

INSERT IGNORE INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`)
VALUES
-- General
(1, 'o_theme11_header', NULL, '#e8836b', 'Theme 11 / Header / accent colour', 'string', NULL, 0, NULL),
(1, 'o_theme11_page',   NULL, '#fbf0ea', 'Theme 11 / Page background', 'string', NULL, 0, NULL),
(1, 'o_theme11_card',   NULL, '#f4dccf', 'Theme 11 / Card background', 'string', NULL, 0, NULL),
-- Fonts
(1, 'o_theme11_text_heading', NULL, '#4a2a22', 'Theme 11 / Heading text colour', 'string', NULL, 0, NULL),
(1, 'o_theme11_text_body',    NULL, '#7b5b51', 'Theme 11 / Body text colour', 'string', NULL, 0, NULL),
(1, 'o_theme11_text_accent',  NULL, '#ae3c22', 'Theme 11 / Accent text colour', 'string', NULL, 0, NULL),
-- Buttons
(1, 'o_theme11_button',       NULL, '#c94e33', 'Theme 11 / Button colour', 'string', NULL, 0, NULL),
(1, 'o_theme11_button_hover', NULL, '#b73f24', 'Theme 11 / Button hover colour', 'string', NULL, 0, NULL),
-- List (service cards, date & time)
(1, 'o_theme11_list_active',   NULL, '#dc775f', 'Theme 11 / List active colour', 'string', NULL, 0, NULL),
(1, 'o_theme11_list_inactive', NULL, '#f0d3c6', 'Theme 11 / List inactive colour', 'string', NULL, 0, NULL);


-- 2) Language content for everything the new UI prints.
INSERT IGNORE INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES
(NULL, 'infoTheme11ColorsTitle', 'backend', 'Info / Theme 11 colours title', 'script', NULL),
(NULL, 'infoTheme11ColorsDesc', 'backend', 'Info / Theme 11 colours description', 'script', NULL),
(NULL, 'btnTheme11ColorsSave', 'backend', 'Button / Save theme 11 colours', 'script', NULL),
(NULL, 'lblTheme11ColorGroupHeader', 'backend', 'Label / Theme 11 colour group - Header', 'script', NULL),
(NULL, 'lblTheme11ColorGroupButtons', 'backend', 'Label / Theme 11 colour group - Buttons & links', 'script', NULL),
(NULL, 'lblTheme11ColorGroupPage', 'backend', 'Label / Theme 11 colour group - Page', 'script', NULL),
(NULL, 'lblTheme11ColorHeaderBg', 'backend', 'Label / Theme 11 colour - Header background', 'script', NULL),
(NULL, 'lblTheme11ColorHeaderText', 'backend', 'Label / Theme 11 colour - Header text', 'script', NULL),
(NULL, 'lblTheme11ColorPrimary', 'backend', 'Label / Theme 11 colour - Primary (buttons)', 'script', NULL),
(NULL, 'lblTheme11ColorPrimaryText', 'backend', 'Label / Theme 11 colour - Primary button text', 'script', NULL),
(NULL, 'lblTheme11ColorSuccess', 'backend', 'Label / Theme 11 colour - Success / completed step', 'script', NULL),
(NULL, 'lblTheme11ColorLink', 'backend', 'Label / Theme 11 colour - Links', 'script', NULL),
(NULL, 'lblTheme11ColorBodyBg', 'backend', 'Label / Theme 11 colour - Page background', 'script', NULL),
(NULL, 'lblTheme11ColorCardBg', 'backend', 'Label / Theme 11 colour - Card background', 'script', NULL),
(NULL, 'lblTheme11ColorText', 'backend', 'Label / Theme 11 colour - Text', 'script', NULL),
(NULL, 'lblTheme11ColorMuted', 'backend', 'Label / Theme 11 colour - Muted text', 'script', NULL),
(NULL, 'lblTheme11ColorBorder', 'backend', 'Label / Theme 11 colour - Borders', 'script', NULL),
(NULL, 'error_titles_ARRAY_AO06', 'arrays', 'error_titles_ARRAY_AO06', 'script', NULL),
(NULL, 'error_bodies_ARRAY_AO06', 'arrays', 'error_bodies_ARRAY_AO06', 'script', NULL),
(NULL, 'menuRequestCustomization', 'backend', 'Menu / Request customization', 'script', NULL),
(NULL, 'option_themes_ARRAY_11', 'arrays', 'option_themes_ARRAY_11', 'script', NULL);

-- locale 1 is used below because every existing row in `multi_lang`
-- for this script's own stock language content uses locale 1 - if you run
-- the admin panel in more than one language, duplicate the relevant
-- INSERT below once per additional locale id.
SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'infoTheme11ColorsTitle' LIMIT 1);
INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES (NULL, @id, 'pjField', 1, 'title', 'Theme 11 colours', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'infoTheme11ColorsDesc' LIMIT 1);
INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES (NULL, @id, 'pjField', 1, 'title', 'Pick the colours your Theme 11 booking widget uses. Leave a field as-is to keep its default.', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'btnTheme11ColorsSave' LIMIT 1);
INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES (NULL, @id, 'pjField', 1, 'title', 'Save colours', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'lblTheme11ColorGroupHeader' LIMIT 1);
INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES (NULL, @id, 'pjField', 1, 'title', 'Header', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'lblTheme11ColorGroupButtons' LIMIT 1);
INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES (NULL, @id, 'pjField', 1, 'title', 'Buttons &amp; links', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'lblTheme11ColorGroupPage' LIMIT 1);
INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES (NULL, @id, 'pjField', 1, 'title', 'Page', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'lblTheme11ColorHeaderBg' LIMIT 1);
INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES (NULL, @id, 'pjField', 1, 'title', 'Header background', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'lblTheme11ColorHeaderText' LIMIT 1);
INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES (NULL, @id, 'pjField', 1, 'title', 'Header text', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'lblTheme11ColorPrimary' LIMIT 1);
INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES (NULL, @id, 'pjField', 1, 'title', 'Primary (buttons, active step)', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'lblTheme11ColorPrimaryText' LIMIT 1);
INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES (NULL, @id, 'pjField', 1, 'title', 'Primary button text', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'lblTheme11ColorSuccess' LIMIT 1);
INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES (NULL, @id, 'pjField', 1, 'title', 'Success / completed step', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'lblTheme11ColorLink' LIMIT 1);
INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES (NULL, @id, 'pjField', 1, 'title', 'Links', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'lblTheme11ColorBodyBg' LIMIT 1);
INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES (NULL, @id, 'pjField', 1, 'title', 'Page background', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'lblTheme11ColorCardBg' LIMIT 1);
INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES (NULL, @id, 'pjField', 1, 'title', 'Card background', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'lblTheme11ColorText' LIMIT 1);
INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES (NULL, @id, 'pjField', 1, 'title', 'Text', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'lblTheme11ColorMuted' LIMIT 1);
INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES (NULL, @id, 'pjField', 1, 'title', 'Muted text', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'lblTheme11ColorBorder' LIMIT 1);
INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES (NULL, @id, 'pjField', 1, 'title', 'Borders', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'error_titles_ARRAY_AO06' LIMIT 1);
INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES (NULL, @id, 'pjField', 1, 'title', 'Theme 11 colours updated', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'error_bodies_ARRAY_AO06' LIMIT 1);
INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES (NULL, @id, 'pjField', 1, 'title', 'All changes made to the Theme 11 colours have been saved successfully.', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'menuRequestCustomization' LIMIT 1);
INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES (NULL, @id, 'pjField', 1, 'title', 'Request customization', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = 'option_themes_ARRAY_11' LIMIT 1);
INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES (NULL, @id, 'pjField', 1, 'title', 'Theme 11', 'script');

INSERT IGNORE INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`)
VALUES
(NULL, 'front_step_service', 'frontend', 'Label / Step tracker - Service', 'plugin', NULL),
(NULL, 'front_step_time', 'frontend', 'Label / Step tracker - Time', 'plugin', NULL),
(NULL, 'front_step_details', 'frontend', 'Label / Step tracker - Details', 'plugin', NULL),
(NULL, 'front_step_confirm', 'frontend', 'Label / Step tracker - Confirm', 'plugin', NULL);

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, `id`, 'pjField', 1, 'title', 'Service', 'data' FROM `fields` WHERE `key` = 'front_step_service';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, `id`, 'pjField', 1, 'title', 'Time', 'data' FROM `fields` WHERE `key` = 'front_step_time';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, `id`, 'pjField', 1, 'title', 'Details', 'data' FROM `fields` WHERE `key` = 'front_step_details';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, `id`, 'pjField', 1, 'title', 'Confirm', 'data' FROM `fields` WHERE `key` = 'front_step_confirm';

UPDATE `options`
SET `value` = 'theme1|theme2|theme3|theme4|theme5|theme6|theme7|theme8|theme9|theme10|theme11::theme11'
WHERE `key` = 'o_theme';

COMMIT;