/* r751 MEDIUMTEXT page text expansion */
ALTER TABLE `sed_pages` MODIFY `page_text` MEDIUMTEXT collate utf8_unicode_ci;
ALTER TABLE `sed_pages` MODIFY `page_html` MEDIUMTEXT collate utf8_unicode_ci;