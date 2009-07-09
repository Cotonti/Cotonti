/* r801 search plugin configuration update */
INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES
('plug', 'search', '8', 'addfields', 1, '', '', 'Additional pages fields for search, separated by commas. Example "page_extra1,page_extra2,page_key"'),
('plug', 'search', '6', 'showtext_ext', 3, '1', '', 'Show text in result for extended search'),
('plug', 'search', '5', 'showtext', 3, '1', '', 'Show text in result for general search'),
('plug', 'search', '4', 'maxitems_ext', 2, '100', '15,30,50,80,100,150,200,300', 'Maximum results lines for extended search'),
('plug', 'search', '1', 'maxwords', 2, '5', '3,5,8,10', 'Maximum search words'),
('plug', 'search', '2', 'maxsigns', 2, '40', '20,30,40,50,60,70,80', 'Maximum signs in query'),
('plug', 'search', '2', 'minsigns', 2, '3', '2,3,4,5', 'Min. signs in query'),
('plug', 'search', '3', 'maxitems', 2, '50', '15,30,50,80,100,150,200', 'Maximum results lines for general search');