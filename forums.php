<?php
/**
 * Forums root-level redirector for backwards compatibility
 *
 * @package Cotonti
 * @version 0.9.4
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 * @deprecated Deprecated since Cotonti Siena
 */

$_GET['e'] = 'forums';

require 'index.php';

?>