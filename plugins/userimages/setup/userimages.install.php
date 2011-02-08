<?php

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('userimages', 'plug');

cot_userimages_config_add('avatar', 100, 100, 'fit', true);
cot_userimages_config_add('photo', 200, 300, 'fit', true);

?>