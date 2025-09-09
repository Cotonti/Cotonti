<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=ajax
[END_COT_EXT]
==================== */

/**
 * AJAX handler for autocompletion
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('tags', 'plug');

$result = ['results' => []];

$q = mb_strtolower(cot_import('q', 'G', 'TXT'));
if ($q === null || $q === '') {
    echo json_encode($result);
    return;
}
$term = mb_strtolower($q);
$term = urldecode($term);

$minLength = 1;

if (!$term || mb_strlen($term) < $minLength) {
    echo json_encode($result);
    return;
}

$tags = [];
$sql = Cot::$db->query('SELECT tag FROM ' . Cot::$db->tags . ' WHERE tag LIKE ?', [$term . '%']);
while ($row = $sql->fetch()) {
    $tags[] = ['id' => $row['tag'], 'text' => $row['tag']];
}
$sql->closeCursor();

if (!empty($tags)) {
    $result['results'] = $tags;
}

cot_sendheaders('application/json');
echo json_encode($result);

