/* r187 Universal tag system scheme */

-- Just tags alone, required for autocomplete
CREATE TABLE `sed_tags` (
	`tag` VARCHAR(255) NOT NULL,
	PRIMARY KEY(`tag`)
);

-- For tag references, search and other needs
CREATE TABLE `sed_tag_references` (
	`tag` VARCHAR(255) NOT NULL REFERENCES `sed_tags`(`tag`),
	`tag_item` INT NOT NULL,
	`tag_area` VARCHAR(50) NOT NULL DEFAULT 'pages',
	PRIMARY KEY (`tag`, `tag_area`, `tag_item`),
	KEY `tag_item`(`tag_item`),
	KEY `tag_area`(`tag_area`)
);