START TRANSACTION;

-- 1) Options
INSERT IGNORE INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_smtp_secure', 9, '|ssl|tls::', NULL, 'enum', 13, 1, NULL),
(1, 'o_smtp_auth', 9, 'LOGIN|PLAIN|CRAM-MD5::LOGIN', NULL, 'enum', 14, 1, NULL),
(1, 'o_from_email', 9, '', NULL, 'string', 15, 1, NULL),
(1, 'o_from_name', 9, '', NULL, 'string', 16, 1, NULL);

-- move the existing email options to the new tab (hidden from the General tab)
UPDATE `options` SET `tab_id` = 9 WHERE `key` IN ('o_send_email','o_smtp_host','o_smtp_port','o_smtp_user','o_smtp_pass');

-- 2) Labels
INSERT IGNORE INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES
(NULL, 'tabEmailSettings', 'backend', 'Tab / Email Settings', 'script', NULL),
(NULL, 'infoEmailSettingsTitle', 'backend', 'Email Settings / Info title', 'script', NULL),
(NULL, 'infoEmailSettingsBody', 'backend', 'Email Settings / Info body', 'script', NULL),
(NULL, 'error_titles_ARRAY_AO07', 'arrays', 'error_titles_ARRAY_AO07', 'script', NULL),
(NULL, 'error_bodies_ARRAY_AO07', 'arrays', 'error_bodies_ARRAY_AO07', 'script', NULL),
(NULL, 'error_titles_ARRAY_AO08', 'arrays', 'error_titles_ARRAY_AO08', 'script', NULL),
(NULL, 'error_bodies_ARRAY_AO08', 'arrays', 'error_bodies_ARRAY_AO08', 'script', NULL),
(NULL, 'opt_o_smtp_secure', 'backend', 'Options / SMTP security', 'script', NULL),
(NULL, 'opt_o_smtp_auth', 'backend', 'Options / SMTP authentication', 'script', NULL),
(NULL, 'opt_o_from_email', 'backend', 'Options / Sender email address', 'script', NULL),
(NULL, 'opt_o_from_name', 'backend', 'Options / Sender name', 'script', NULL),
(NULL, 'lblNone', 'backend', 'Label / None', 'script', NULL),
(NULL, 'btnTestConnection', 'backend', 'Button / Test connection', 'script', NULL),
(NULL, 'btnSendTestEmail', 'backend', 'Button / Send test email', 'script', NULL),
(NULL, 'btnSendEmail', 'backend', 'Button / Send email', 'script', NULL),
(NULL, 'lblEmailAddress', 'backend', 'Label / Email address', 'script', NULL),
(NULL, 'emailMsgEnterEmail', 'backend', 'Email test / Enter email', 'script', NULL),
(NULL, 'emailMsgEnterValidEmail', 'backend', 'Email test / Enter valid email', 'script', NULL),
(NULL, 'emailMsgEnterHostPort', 'backend', 'Email test / Enter host and port', 'script', NULL),
(NULL, 'emailMsgTesting', 'backend', 'Email test / Testing connection', 'script', NULL),
(NULL, 'emailMsgSending', 'backend', 'Email test / Sending', 'script', NULL),
(NULL, 'emailMsgConnOk', 'backend', 'Email test / Connection OK', 'script', NULL),
(NULL, 'emailMsgConnFail', 'backend', 'Email test / Connection failed', 'script', NULL),
(NULL, 'emailMsgSentOk', 'backend', 'Email test / Sent OK', 'script', NULL),
(NULL, 'emailMsgCheckInbox', 'backend', 'Email test / Check inbox', 'script', NULL),
(NULL, 'emailMsgSendFail', 'backend', 'Email test / Send failed', 'script', NULL),
(NULL, 'emailMsgUnexpected', 'backend', 'Email test / Unexpected response', 'script', NULL),
(NULL, 'emailMsgPrompt', 'backend', 'Email test / Prompt', 'script', NULL),
(NULL, 'emailTestModalTitle', 'backend', 'Email test / Dialog title', 'script', NULL),
(NULL, 'emailTestModalDesc', 'backend', 'Email test / Dialog description', 'script', NULL),
(NULL, 'emailSmtpPortHint', 'backend', 'Email test / SMTP port hint', 'script', NULL),
(NULL, 'emailTestSubject', 'backend', 'Email test / Test email subject', 'script', NULL),
(NULL, 'emailTestBody', 'backend', 'Email test / Test email body', 'script', NULL);

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Email Settings', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'tabEmailSettings';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Email Settings', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'infoEmailSettingsTitle';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Choose how the script sends emails: the built-in PHP mail() function or your own SMTP server. Use "Test connection" to check the SMTP details and "Send test email" to confirm that messages are delivered. The sender address and name are used for all emails sent by the script.', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'infoEmailSettingsBody';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Email settings updated', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'error_titles_ARRAY_AO07';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'All changes made to the email settings have been saved successfully.', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'error_bodies_ARRAY_AO07';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Email settings not saved', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'error_titles_ARRAY_AO08';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Please enter a valid SMTP host and port (when SMTP is selected) and a valid sender email address.', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'error_bodies_ARRAY_AO08';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'SMTP security', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'opt_o_smtp_secure';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'SMTP authentication', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'opt_o_smtp_auth';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Sender email address', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'opt_o_from_email';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Sender name', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'opt_o_from_name';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'None', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'lblNone';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Test connection', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'btnTestConnection';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Send test email', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'btnSendTestEmail';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Send email', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'btnSendEmail';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Email address', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'lblEmailAddress';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Please enter an email address.', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'emailMsgEnterEmail';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Please enter a valid email address.', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'emailMsgEnterValidEmail';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Please enter the SMTP host and port first.', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'emailMsgEnterHostPort';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Testing connection...', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'emailMsgTesting';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Sending test email...', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'emailMsgSending';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Connection successful. The SMTP server accepted the connection and login.', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'emailMsgConnOk';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Connection failed', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'emailMsgConnFail';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Test email sent successfully to {EMAIL}.', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'emailMsgSentOk';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Please check your inbox (and the spam folder).', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'emailMsgCheckInbox';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'The test email could not be sent', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'emailMsgSendFail';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Unexpected response from the server. Please try again.', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'emailMsgUnexpected';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Enter the email address that should receive the test email.', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'emailMsgPrompt';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Send test email', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'emailTestModalTitle';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'A test message will be sent using the settings currently shown in the form (they do not need to be saved first).', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'emailTestModalDesc';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Common ports: 25, 465 (SSL), 587 (TLS)', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'emailSmtpPortHint';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'Test email', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'emailTestSubject';

INSERT IGNORE INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT NULL, f.`id`, 'pjField', l.`locale`, 'title', 'This is a test email sent from the Email Settings page. If you can read this message, your email settings are working correctly.', 'script'
FROM `fields` f
CROSS JOIN (SELECT DISTINCT `locale` FROM `multi_lang` WHERE `model` = 'pjField' AND `locale` IS NOT NULL) l
WHERE f.`key` = 'emailTestBody';

-- 3) Refresh the cached label list so the new labels show up immediately.
UPDATE `options` SET `value` = MD5(RAND()) WHERE `key` = 'o_fields_index';


COMMIT;