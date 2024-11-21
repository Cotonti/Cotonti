<?php

declare(strict_types=1);

namespace cot\controllers;

use Cot;

defined('COT_CODE') or die('Wrong URL.');

/**
 * Main controller
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
class MainController extends BaseController
{
    /**
     * Obtain various data via ajax
     */
    public function actionGet(): string
    {
        $result = ['success' => true, 'data' => []];

        if (!is_array($_GET['data'])) {
            $dataToGet = cot_import('data', 'G', 'txt');
            $dataToGet = [$dataToGet];
        } else {
            $dataToGet = cot_import('data', 'G', 'ARR');
        }

        if (empty($dataToGet)) {
            cot_sendheaders('application/json', '200 OK', Cot::$sys['now']);
            return json_encode($result);
        }

        foreach ($dataToGet as $dataRow) {
            $dataRow = trim($dataRow);
            switch ($dataRow) {
                case 'x':
                    $result['data'][$dataRow] = Cot::$sys['xk'];
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

        cot_sendheaders('application/json', '200 OK', Cot::$sys['now']);

        return json_encode($result);
    }
}
