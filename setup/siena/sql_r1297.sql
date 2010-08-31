/* r1297 Index polls sql delete and extrafields upd */
UPDATE `sed_extra_fields` SET field_location = 'users' WHERE field_location = 'sed_users';
UPDATE `sed_extra_fields` SET field_location = 'pages' WHERE field_location = 'sed_pages';

DELETE FROM `sed_auth` WHERE auth_option = 'indexpolls';
DELETE FROM `sed_config` WHERE config_cat = 'indexpolls';
DELETE FROM `sed_plugins` WHERE pl_code = 'indexpolls';