<?php
/**
 * Forums topics control service
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\modules\forums\inc;

use Cot;
use cot\services\ItemService;
use cot\traits\GetInstanceTrait;
use Throwable;

defined('COT_CODE') or die('Wrong URL.');

class ForumsTopicsControlService
{
    use GetInstanceTrait;

    public function delete(int $id, string $logMessage = ''): bool
    {
        $topic = ForumsTopicsRepository::getInstance()->getById($id);
        if ($topic === null) {
            return false;
        }

        $result = $this->performDelete($topic);

        if ($result) {
            $message = 'Deleted topic #' . $id;
            if ($logMessage !== '') {
                $message .= ' (' . $logMessage . ')';
            }
            cot_log($message, 'forums', 'delete topic', 'done');
        }

        return $result;
    }

    /**
     * Perform topic deletion
     * @param array $topic
     * @return bool
     * @throws Throwable
     */
    public function performDelete(array $topic): bool
    {
        global $cfg, $L, $Ls, $R; // For hooks include

        $topicId = $topic['ft_id'];

        try {
            Cot::$db->beginTransaction();

            /* === Hook === */
            foreach (cot_getextplugins('forums.topic.delete') as $pl) {
                include $pl;
            }
            /* ===== */

            if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
                // @deprecated in 0.9.26
                /* === Hook === */
                foreach (cot_getextplugins('forums.functions.prunetopics') as $pl) {
                    include $pl;
                }
                /* ===== */
            }

            ForumsPostsControlService::getInstance()->deleteByTopicId($topicId);

            Cot::$db->delete(Cot::$db->forum_topics, 'ft_movedto = ?', $topicId);

            foreach (Cot::$extrafields[Cot::$db->forum_topics] as $exfld) {
                if (isset($topic['ft_' . $exfld['field_name']])) {
                    cot_extrafield_unlinkfiles($topic['ft_' . $exfld['field_name']], $exfld);
                }
            }

            $result = Cot::$db->delete(Cot::$db->forum_topics, 'ft_id = ?', $topicId);

            cot_forums_updateStructureCounters($topic['ft_cat']);

            /* === Hook === */
            foreach (cot_getextplugins('forums.topic.delete.done') as $pl) {
                include $pl;
            }
            /* ===== */

            ItemService::getInstance()->onDelete(ForumsDictionary::SOURCE_TOPIC, $topicId);

            Cot::$db->commit();
        } catch (Throwable $e) {
            Cot::$db->rollBack();
            throw $e;
        }

        return true;
    }
}