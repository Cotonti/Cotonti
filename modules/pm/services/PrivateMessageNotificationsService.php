<?php
/**
 * @package PM
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\modules\pm\services;

use Cot;
use cot\serverEvents\ServerEventService;
use cot\traits\GetInstanceTrait;
use cot\users\UsersHelper;
use XTemplate;

class PrivateMessageNotificationsService
{
    use GetInstanceTrait;

    /**
     * New PM notifications by email
     * @param array{pm_id: int, pm_title: string, pm_text: string} $message
     */
    public function newPmEmail(array $toUser, array $fromUser, array $message): bool
    {
        // Notification body on recipient's language
        $tmpLang = null;
        if (!Cot::$cfg['forcedefaultlang'] && Cot::$cfg['defaultlang'] !== $toUser['user_lang']) {
            $tmpLang = Cot::$L;
            $loc = LangService::load('thanks', 'plug', $toUser['user_lang']);
            Cot::$L = array_merge($loc, Cot::$L);
        }

        $subject = Cot::$L['pm_notifytitle'];

        $usersHelper = UsersHelper::getInstance();

        $toUserName = htmlspecialchars($usersHelper->getFullName($toUser));
        $fromUserName = htmlspecialchars($usersHelper->getFullName($fromUser));

        $body = sprintf(
            Cot::$L['pm_notify'],
            $toUserName,
            $fromUserName,
            cot_absoluteUrl('pm', '', '', true)
        );

        cot_mail($toUser['user_email'], $subject, $body);

        if ($tmpLang !== null) {
            Cot::$L = $tmpLang;
        }

        $statsEnabled = function_exists('cot_stat_inc');
        // Total email PM notifications sent
        if ($statsEnabled) {
            cot_stat_inc('totalmailpmnot');
        }

        return true;
    }

    /**
     * Send front end new PM notification (toast)
     * @param array{pm_id: int, pm_title: string, pm_text: string} $message
     */
    public function newPmToast(array $toUser, array $fromUser, array $message): bool
    {
        // Notification body on recipient's language
        $tmpLang = null;
        if (!Cot::$cfg['forcedefaultlang'] && Cot::$cfg['defaultlang'] !== $toUser['user_lang']) {
            $tmpLang = Cot::$L;
            $loc = LangService::load('thanks', 'plug', $toUser['user_lang']);
            Cot::$L = array_merge($loc, Cot::$L);
        }

        $t = new XTemplate(cot_tplfile(['pm', 'popUpNotification']));
        $t->assign(cot_generate_usertags($toUser, 'TO_USER_'));
        $t->assign(cot_generate_usertags($fromUser, 'FROM_USER_'));
        $t->assign([
            'TITLE' => $message['pm_title'],
            'TEXT' => strip_tags($message['pm_text']),
        ]);

        $t->parse();
        $text = $t->text();

        $data = [
            'fromUser' => [
                'name' => $fromUser['user_name'],
                'fullName' => htmlspecialchars(UsersHelper::getInstance()->getFullName($fromUser)),
                'url' => cot_url(
                    'users',
                    ['m' => 'details', 'id' => $fromUser['user_id'], 'u' => $fromUser['user_name']]
                ),
            ],
            //'title' => $message['pm_title'],
            'text' => $text,
            'url' => cot_url('pm', ['m' => 'message', 'id' => $message['pm_id']]),
            'L' => [
                'newMessage' => Cot::$L['pm_newMessage'],
                'from' => Cot::$L['pm_from'],
            ],
            // @todo message date and time in $toUser timezone
        ];

        ServerEventService::getInstance()->createForUser($toUser['user_id'], 'newPm', $data);

        if ($tmpLang !== null) {
            Cot::$L = $tmpLang;
        }

        return true;
    }
}