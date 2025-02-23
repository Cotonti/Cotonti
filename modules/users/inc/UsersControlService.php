<?php
/**
 * Users control service
 *
 * @package Users
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\modules\users\inc;

use Cot;
use cot\extensions\ExtensionsDictionary;
use cot\extensions\ExtensionsService;
use cot\modules\page\inc\PageDictionary;
use cot\modules\page\inc\PageRepository;
use cot\services\ItemService;
use cot\traits\GetInstanceTrait;
use Throwable;

class UsersControlService
{
    use GetInstanceTrait;

    /**
     * Delete user
     * @param int $id User ID
     * @param bool $deletePfs Delete user's personal files
     * @param array $userData User data
     * @return bool
     */
    function delete(int $id, bool $deletePfs = true, array $userData = []): bool
    {
        // For included files
        global $L, $R, $Ls;

        if ($id <= 0) {
            return false;
        }

        if (empty($userData)) {
            $userData = PageRepository::getInstance()->getById($id);
        }
        if (empty($userData)) {
            return false;
        }

        try {
            Cot::$db->beginTransaction();

            /* === Hook === */
            foreach (cot_getextplugins('users.delete') as $pl) {
                include $pl;
            }
            /* ===== */

            Cot::$db->delete(Cot::$db->users, 'user_id = :userId', ['userId' => $id]);
            Cot::$db->delete(Cot::$db->groups_users, 'gru_userid = :userId', ['userId' => $id]);

            foreach(Cot::$extrafields[Cot::$db->users] as $exfld) {
                cot_extrafield_unlinkfiles($userData['user_' . $exfld['field_name']], $exfld);
            }

            if ($deletePfs && ExtensionsService::getInstance()->isModuleActive('pfs')) {
                require_once cot_incfile('pfs', ExtensionsDictionary::TYPE_MODULE);
                cot_pfs_deleteall($id);
            }

            /* === Hook === */
            foreach (cot_getextplugins('users.delete.done') as $pl) {
                include $pl;
            }
            /* ===== */

            ItemService::getInstance()->onDelete(PageDictionary::SOURCE_PAGE, $id);

            Cot::$db->commit();
        } catch (Throwable $e) {
            Cot::$db->rollBack();
            throw $e;
        }

        cot_log("Deleted user #" . $id, 'users', 'delete', 'done');

        return true;
    }
}