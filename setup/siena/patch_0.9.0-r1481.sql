/* r1481 Move hidden groups feature from core to plugin */
INSERT IGNORE INTO `cot_plugins` (`pl_hook`, `pl_code`, `pl_part`, `pl_title`, `pl_file`, `pl_order`, `pl_active`, `pl_module`) VALUES
('admin.users.update', 'hiddengroups', 'admin.users.update', 'Hidden Groups', './plugins/hiddengroups/hiddengroups.admin.users.update.php', 10, 1, 0),
('admin.users.edit.tags', 'hiddengroups', 'admin.users.edit.tags', 'Hidden Groups', './plugins/hiddengroups/hiddengroups.admin.users.edit.tags.php', 10, 1, 0),
('admin.users.row.tags', 'hiddengroups', 'admin.users.row.tags', 'Hidden Groups', './plugins/hiddengroups/hiddengroups.admin.users.row.tags.php', 10, 1, 0),
('admin.users.add.tags', 'hiddengroups', 'admin.users.add.tags', 'Hidden Groups', './plugins/hiddengroups/hiddengroups.admin.users.add.tags.php', 10, 1, 0),
('admin.users.add', 'hiddengroups', 'admin.users.add', 'Hidden Groups', './plugins/hiddengroups/hiddengroups.admin.users.add.php', 10, 1, 0);

INSERT IGNORE INTO `cot_auth` (`auth_groupid`, `auth_code`, `auth_option`, `auth_rights`, `auth_rights_lock`, `auth_setbyuserid`) VALUES
(5, 'plug', 'hiddengroups', 255, 255, 1),
(2, 'plug', 'hiddengroups', 0, 254, 1),
(3, 'plug', 'hiddengroups', 0, 255, 1),
(4, 'plug', 'hiddengroups', 0, 126, 1),
(1, 'plug', 'hiddengroups', 0, 254, 1);

INSERT INTO `cot_updates` (`upd_param`, `upd_value`) VALUES
('hiddengroups.ver', '0.9.0');