/* r1297 Index polls sql delete and extrafields upd */
UPDATE `cot_extra_fields` SET field_location = 'sed_users' WHERE field_location = 'users';
UPDATE `cot_extra_fields` SET field_location = 'sed_pages' WHERE field_location = 'pages';

DELETE FROM `cot_auth` WHERE auth_option = 'indexpolls';
DELETE FROM `cot_config` WHERE config_cat = 'indexpolls';
DELETE FROM `cot_plugins` WHERE pl_code = 'indexpolls';