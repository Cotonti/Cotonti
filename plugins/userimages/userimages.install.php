<?php

defined('COT_CODE') or die('Wrong URL');

$config_options = array(
	array(
		'name' => 'avatar',
		'type' => COT_CONFIG_TYPE_HIDDEN,
		'default' => '100x100xfit'
	),
	array(
		'name' => 'photo',
		'type' => COT_CONFIG_TYPE_HIDDEN,
		'default' => '200x300xfit'
	)
);
cot_config_add('userimages', $config_options);

?>