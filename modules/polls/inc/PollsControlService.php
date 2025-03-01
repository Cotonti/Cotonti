<?php
/**
 * Polls control service
 * @package Polls
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\modules\polls\inc;

use Cot;
use cot\services\ItemService;
use cot\traits\GetInstanceTrait;
use Throwable;

defined('COT_CODE') or die('Wrong URL.');

class PollsControlService
{
    use GetInstanceTrait;

    public function __construct()
    {
        Cot::$db->registerTable('polls');
        Cot::$db->registerTable('polls_options');
        Cot::$db->registerTable('polls_voters');
    }

    /**
     * Removes a poll
     * @param int $id Poll ID
     * @return bool
     */
    function delete(int $id): bool
    {
        $poll = PollsRepository::getInstance()->getById($id);
        if ($poll === null) {
            return false;
        }

        return $this->performDelete($poll);
    }

    /**
     * Removes comments associated with an item
     *
     * @param string $source
     * @param string $sourceId
     * @return int
     */
    public function deleteBySourceId(string $source, string $sourceId): int
    {
        $polls = PollsRepository::getInstance()->getBySourceId($source, $sourceId);

        $result = 0;
        foreach ($polls as $poll) {
            if ($this->performDelete($poll)) {
                $result++;
            }
        }

        return $result;
    }

    /**
     * Perform topic deletion
     * @param array $poll
     * @return bool
     */
    private function performDelete(array $poll): bool
    {
        global $cfg, $L, $Ls, $R; // For hooks include

        $pollId = $poll['poll_id'];

        $params = ['pollId' => $pollId];

        try {
            Cot::$db->beginTransaction();

            Cot::$db->delete(Cot::$db->polls_voters, 'pv_pollid = :pollId', $params);
            Cot::$db->delete(Cot::$db->polls_options, 'po_pollid = :pollId', $params);
            Cot::$db->delete(Cot::$db->polls, 'poll_id = :pollId', $params);

            /* === Hook === */
            foreach (cot_getextplugins('poll.delete.done') as $pl) {
                include $pl;
            }
            /* ===== */

            ItemService::getInstance()->onDelete(PollsDictionary::SOURCE_POLL, $pollId);

            Cot::$db->commit();
        } catch (Throwable $e) {
            Cot::$db->rollBack();
            throw $e;
        }

        return true;
    }
}