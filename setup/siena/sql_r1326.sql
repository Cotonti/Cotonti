/* r1326 Enable Users display for Guests by default */
UPDATE `cot_auth` SET auth_rights = 1 WHERE auth_groupid = 1 AND auth_code = 'users' AND auth_option = 'a';