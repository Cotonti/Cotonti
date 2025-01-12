<?php
/**
 * @package PM
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\modules\pm\services;

use Cot;
use cot\traits\GetInstanceTrait;
use cot\users\UsersHelper;
use cot\users\UsersRepository;

class PrivateMessageService
{
    use GetInstanceTrait;

    /**
     * Send private message to user
     * @return int|false Message ID or FALSE in fail
     */
    public function send(int $to, string $subject, string $text, ?int $from = null, int $fromState = 0)
    {
        if (empty($to)) {
            return false;
        }

        $usersRepository = UsersRepository::getInstance();

        $toUser = $usersRepository->getById($to);
        if (empty($toUser)) {
            return false;
        }

        $toUserGroups = cot_getUserGroupIds($to, $toUser['user_maingrp']);
        if (in_array(COT_GROUP_BANNED, $toUserGroups)) {
            return false;
        }
        if (
            count($toUserGroups) === 1
            && in_array($toUserGroups[0], [COT_GROUP_DEFAULT, COT_GROUP_GUESTS])
        ) {
            return false;
        }

        if (empty($from)) {
            $from = Cot::$usr['id'];
        }

        $fromUser = $usersRepository->getById($from);
        if (empty($fromUser)) {
            return false;
        }

        $fromName = UsersHelper::getInstance()->getFullName($fromUser);

        $pm = [
            'pm_title' => $subject,
            'pm_date' => Cot::$sys['now'],
            'pm_text' => $text,
            'pm_fromstate' => $fromState,
            'pm_fromuserid' => $from,
            'pm_fromuser' => $fromName,
            'pm_touserid' => $to,
            'pm_tostate' => 0,
        ];

        /* === Hook === */
        foreach (cot_getextplugins('pm.send.query') as $pl) {
            include $pl;
        }
        /* ===== */

        $result = Cot::$db->insert(Cot::$db->pm, $pm);
        if (!$result) {
            return false;
        }
        $pm['pm_id'] = $id = Cot::$db->lastInsertId();

        Cot::$db->update(Cot::$db->users, ['user_newpm' => '1'], "user_id=:userId", ['userId' => $to]);

        $notificationService = PrivateMessageNotificationsService::getInstance();

        // @todo Email notifications without message text should not be sent too often.
        if (
            Cot::$cfg['pm']['allownotifications']
            && $toUser['user_pmnotify']
            && !in_array(COT_GROUP_INACTIVE, $toUserGroups)
        ) {
            $notificationService->newPmEmail($toUser, $fromUser, $pm);
        }

        if (Cot::$cfg['pm']['allowPopUpNotifications']) {
            $notificationService->newPmToast($toUser, $fromUser, $pm);
        }

        $statsEnabled = function_exists('cot_stat_inc');
        if ($statsEnabled) {
            // Total private messages in DB
            cot_stat_inc('totalpms');
        }

        /* === Hook === */
        foreach (cot_getextplugins('pm.send.done') as $pl) {
            include $pl;
        }
        /* ===== */

        return $id;
    }
}