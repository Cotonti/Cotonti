/* More indexes for speed */
ALTER TABLE sed_pages ADD INDEX (page_alias), ADD INDEX (page_state), ADD INDEX (page_date);
ALTER TABLE sed_structure ADD INDEX (structure_path);
ALTER TABLE sed_users ADD INDEX (user_password), ADD INDEX (user_regdate);
ALTER TABLE sed_forum_topics ADD INDEX (ft_movedto);
ALTER TABLE sed_forum_posts ADD INDEX (fp_updated), ADD INDEX (fp_posterid), ADD INDEX (fp_sectionid);
ALTER TABLE sed_online ADD INDEX (online_userid), ADD INDEX (online_name);

/* Size limitation removal */
ALTER TABLE sed_auth MODIFY auth_code VARCHAR(255), MODIFY auth_option VARCHAR(255);
ALTER TABLE sed_pages MODIFY page_cat VARCHAR(255), MODIFY page_alias VARCHAR(255);
ALTER TABLE sed_structure MODIFY structure_code VARCHAR(255);

/* Rendered cache support */
ALTER TABLE sed_forum_posts ADD fp_html TEXT NOT NULL DEFAULT '';
ALTER TABLE sed_pages ADD page_html TEXT NOT NULL DEFAULT '';
ALTER TABLE sed_pm ADD pm_html TEXT NOT NULL DEFAULT '';
ALTER TABLE sed_users ADD user_html TEXT NOT NULL DEFAULT '';
