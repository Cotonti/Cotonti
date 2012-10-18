/* r1056 split conf parametrs from page section to structure section */
INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES
('core', 'rss', '05', 'rss_pagemaxsymbols', 1, '', '', 'Pages. Cut element description longer than N symbols'),
('core', 'rss', '06', 'rss_commentmaxsymbols', 1, '', '', 'Comments. Cut element description longer than N symbols'),
('core', 'rss', '07', 'rss_postmaxsymbols', 1, '', '', 'Posts. Cut element description longer than N symbols');