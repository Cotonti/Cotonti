<?php
/**
 * Page control service
 *
 * @package Page
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\modules\page\inc;

use Cot;
use cot\services\ItemService;
use cot\traits\GetInstanceTrait;
use Throwable;

defined('COT_CODE') or die('Wrong URL.');

class PageControlService
{
    use GetInstanceTrait;

    /**
     * Removes a page from the CMS.
     * @param int $id Page ID
     * @param array $pageData Page data
     * @return bool|string "deleted" message on success, FALSE on error
     */
    function delete(int $id, array $pageData = [])
    {
        if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
            // @deprecated in 0.9.26
            // $L, $Ls, $R are needed for hook includes
            global $L, $Ls, $R;
        }

        if ($id <= 0) {
            return false;
        }

        if (empty($pageData)) {
            $pageData = PageRepository::getInstance()->getById($id);
        }
        if (empty($pageData)) {
            return false;
        }

        if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
            // @deprecated in 0.9.26
            $rpage = $pageData;
        }

        try {
            Cot::$db->beginTransaction();

            foreach (Cot::$extrafields[Cot::$db->pages] as $exfld) {
                if (isset($pageData['page_' . $exfld['field_name']])) {
                    cot_extrafield_unlinkfiles($pageData['page_' . $exfld['field_name']], $exfld);
                }
            }

            Cot::$db->delete(Cot::$db->pages, 'page_id = ?', $id);
            cot_log("Deleted page #" . $id, 'page', 'delete', 'done');

            cot_page_updateStructureCounters($pageData['page_cat']);

            $pageDeletedMessage = ['deleted' => Cot::$L['page_deleted']];

            if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
                // @deprecated in 0.9.26
                /* === Hook === */
                foreach (cot_getextplugins('page.edit.delete.done') as $pl) {
                    include $pl;
                }
                /* ===== */
            }

            /* === Hook === */
            foreach (cot_getextplugins('page.delete.done') as $pl) {
                include $pl;
            }
            /* ===== */

            ItemService::getInstance()->onDelete(PageDictionary::SOURCE_PAGE, $id);

            Cot::$db->commit();
        } catch (Throwable $e) {
            Cot::$db->rollBack();
            throw $e;
        }

        if (Cot::$cache) {
            if (Cot::$cfg['cache_page']) {
                Cot::$cache->static->clearByUri(cot_page_url($pageData));
                Cot::$cache->static->clearByUri(cot_url('page', ['c' => $pageData['page_cat']]));
            }
            if (Cot::$cfg['cache_index']) {
                Cot::$cache->static->clear('index');
            }
        }

        return is_array($pageDeletedMessage) ? implode('; ', $pageDeletedMessage) : $pageDeletedMessage;
    }
}