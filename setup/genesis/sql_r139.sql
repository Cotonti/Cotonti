/* r193 Some speed up for page listings */
ALTER TABLE sed_structure ADD COLUMN structure_pagecount mediumint(8) NOT NULL default '0';