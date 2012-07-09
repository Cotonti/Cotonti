/* 0.9.2 Nested quote support */

DELETE FROM `cot_bbcode` WHERE `bbc_name` = 'quote';

INSERT INTO `cot_bbcode` (`bbc_name`, `bbc_mode`, `bbc_pattern`, `bbc_replacement`, `bbc_container`, `bbc_enabled`, `bbc_priority`, `bbc_plug`, `bbc_postrender`) VALUES
('quote', 'pcre', '\\[quote=(.+?)\\]', '<blockquote><strong>$1:</strong><hr />', 1, 1, 128, '', 0),
('quote', 'str', '[quote]', '<blockquote>', 1, 1, 128, '', 0),
('quote', 'str', '[/quote]', '</blockquote>', 1, 1, 128, '', 0);
