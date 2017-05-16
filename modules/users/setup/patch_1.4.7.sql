/* https://github.com/Cotonti/Cotonti/issues/1574 */
UPDATE `cot_auth` SET `auth_rights_lock`=128  WHERE `auth_groupid`=2 AND auth_code NOT IN('admin', 'structure', 'message');
