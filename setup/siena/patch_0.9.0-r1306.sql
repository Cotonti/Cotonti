/* r1306 Move user_msn user_icq to extrafields */
INSERT INTO `cot_extra_fields` (`field_location`, `field_name`, `field_type`, `field_html`, `field_variants`, `field_default`, `field_required`, `field_parse`, `field_description`)
 VALUES
('cot_users', 'icq', 'input', '<input type="text" class="text" name="{$name}" value="{$value}"{$attrs} />{$error}', '', '', 0, 'Text', ''),
('cot_users', 'msn', 'input', '<input type="text" class="text" name="{$name}" value="{$value}"{$attrs} />{$error}', '', '', 0, 'Text', ''),
('cot_users', 'irc', 'input', '<input type="text" class="text" name="{$name}" value="{$value}"{$attrs} />{$error}', '', '', 0, 'Text', ''),
('cot_users', 'website', 'input', '<input type="text" class="text" name="{$name}" value="{$value}"{$attrs} />{$error}', '', '', 0, 'Text', ''),
('cot_users', 'location', 'input', '<input type="text" class="text" name="{$name}" value="{$value}"{$attrs} />{$error}', '', '', 0, 'Text', ''),
('cot_users', 'occupation', 'input', '<input type="text" class="text" name="{$name}" value="{$value}"{$attrs} />{$error}', '', '', 0, 'Text', '');
