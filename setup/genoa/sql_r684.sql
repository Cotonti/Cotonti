/* r684 Increase lengths */
ALTER TABLE sed_forum_topics CHANGE ft_title ft_title VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE sed_forum_topics CHANGE ft_desc ft_desc VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE sed_forum_sections CHANGE fs_lt_title fs_lt_title VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE sed_forum_structure CHANGE fn_title fn_title VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE sed_pfs_folders CHANGE pff_title pff_title VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE sed_pm CHANGE pm_title pm_title VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';