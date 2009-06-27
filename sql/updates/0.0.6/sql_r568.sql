/* r568 Enable comments/ratings for structure */
ALTER TABLE sed_structure ADD COLUMN structure_comments tinyint(1) NOT NULL default '1';
ALTER TABLE sed_structure ADD COLUMN structure_ratings tinyint(1) NOT NULL default '1';