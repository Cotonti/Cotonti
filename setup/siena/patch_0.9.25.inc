<?php
/**
 * Update to v. 0.9.25
 */
defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

Cot::$db->update(
    Cot::$db->config,
    ['config_default' => '{MAINTITLE} - {DESCRIPTION} {SUBTITLE}'],
    "`config_owner` = 'core' AND `config_cat` = 'title' AND `config_name` = 'title_header_index'"
);