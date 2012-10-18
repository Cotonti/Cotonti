/* r155 Fix pfs bbcode */
UPDATE `sed_bbcode` SET `bbc_replacement` = '<strong><a href="datas/users/$1">$1</a></strong>' WHERE `bbc_id` = 64;