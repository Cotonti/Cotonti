/* r1297 Index polls sql delete and extrafields upd */
UPDATE `cot_extra_fields` SET field_location = 'users' WHERE field_location = 'cot_users';
UPDATE `cot_extra_fields` SET field_location = 'pages' WHERE field_location = 'cot_pages';

DELETE FROM `cot_auth` WHERE auth_option = 'indexpolls';
DELETE FROM `cot_config` WHERE config_cat = 'indexpolls';
DELETE FROM `cot_plugins` WHERE pl_code = 'indexpolls';