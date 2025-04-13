<?php
/**
 * Comments system for Cotonti
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\plugins\comments\controllers;

use Cot;
use cot\controllers\BaseController;
use cot\plugins\comments\controllers\actions\CreateAction;
use cot\plugins\comments\controllers\actions\DeleteAction;
use cot\plugins\comments\controllers\actions\DisplayAction;
use cot\plugins\comments\controllers\actions\EditAction;

defined('COT_CODE') or die('Wrong URL');

class IndexController extends BaseController
{
    public static function actions(): array
    {
        return [
            'add' => CreateAction::class,
            'delete' => DeleteAction::class,
            'display' => DisplayAction::class,
            'edit' => EditAction::class,
        ];
    }

    /**
     * Like cot_get_messages('', 'error'), but separately for each 'message source'.
     * @return array<string, list<string>>
     * @see cot_get_messages()
     * @todo move to Message (of Flash) service
     */
    public function getErrors(): array
    {
        if (
            empty($_SESSION['cot_messages'][Cot::$sys['site_id']])
            || !is_array($_SESSION['cot_messages'][Cot::$sys['site_id']])
        ) {
            return [];
        }

        $result = [];
        foreach ($_SESSION['cot_messages'][Cot::$sys['site_id']] as $source => $messages) {
            foreach ($messages as $message) {
                if ($message['class'] === 'error') {
                    $result[$source][] = $message['text'];
                }
            }
        }

        cot_clear_messages('', 'error');

        return $result;
    }

    public function errorResult(array $errors): string
    {
        return $this->result(['success' => false, 'errors' => $errors]);
    }

    public function successResult(string $message): string
    {
        return $this->result(['success' => true, 'message' => $message]);
    }

    private function result($data): string
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

        return json_encode($data);
    }
}