/* r143 t#105, Forum topic preview */
ALTER TABLE sed_forum_topics ADD COLUMN ft_preview varchar(128) NOT NULL default '';