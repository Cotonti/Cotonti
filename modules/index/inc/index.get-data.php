<?php
/**
 * Temporary implementation of a system controller to obtain various data via ajax
 *
 * @todo move to system controller when it will be implemented
 */

defined('COT_CODE') or die('Wrong URL.');

//throw new Exception('asadsas');
//cot_die(true, true);

$result = ['success' => true, 'data' => []];

if (!is_array($_GET['data'])) {
    $dataToGet = cot_import('data', 'G', 'txt');
    $dataToGet = [$dataToGet];
} else {
    $dataToGet = cot_import('data', 'G', 'ARR');
}

if (empty($dataToGet)) {
    cot_sendheaders('application/json', '200 OK', \Cot::$sys['now']);
    echo json_encode($result);
    exit();
}

foreach ($dataToGet as $dataRow) {
    $dataRow = trim($dataRow);
    switch ($dataRow) {
        case 'x':
            $result['data'][$dataRow] = \Cot::$sys['xk'];
            break;

        case 'captcha':
            $result['data'][$dataRow] = cot_captcha_generate();
            break;
    }
}

/* === Hook === */
foreach (cot_getextplugins('system.get-data') as $pl) {
    include $pl;
}
/* ============ */

cot_sendheaders('application/json', '200 OK', \Cot::$sys['now']);
echo json_encode($result);
exit();