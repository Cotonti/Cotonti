<?php

declare(strict_types=1);

namespace cot\structure;

use Cot;
use cot\extensions\ExtensionsDictionary;
use cot\services\ItemService;
use cot\traits\GetInstanceTrait;
use Exception;
use InvalidArgumentException;
use Throwable;

defined('COT_CODE') or die('Wrong URL');

/**
 * Structure manipulation API
 * @package Structure
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
class StructureControlService
{
    use GetInstanceTrait;

    /**
     * Removes a category
     * @param int $id Category ID
     * @param bool $isModule TRUE for modules, FALSE for plugins
     * @return bool
     * @throws Throwable
     */
    public function delete(int $id, bool $isModule = true): bool
    {
        // $L, $Ls, $R are needed for hook includes
        global $L, $Ls, $R;

        /* === Hook === */
        foreach (cot_getextplugins('structure.delete') as $pl) {
            include $pl;
        }
        /* ===== */

        $category = StructureRepository::getInstance()->getById($id);
        if (!$category) {
            throw new InvalidArgumentException(Cot::$L['adm_structure_category_not_exists'] ?? 'Category not found');
        }

        if (
            $category['structure_count'] > 0
            || !empty(
                cot_structure_children(
                    $category['structure_area'],
                    $category['structure_code'],
                    true,
                    false,
                    false
                )
            )
        ) {
            throw new Exception(Cot::$L['adm_structure_category_not_empty'] ?? 'Category not empty');
        }

        try {
            Cot::$db->beginTransaction();

            /* === Hook === */
            foreach (cot_getextplugins('structure.delete.first') as $pl) {
                include $pl;
            }
            /* ===== */

            foreach (Cot::$extrafields[Cot::$db->structure] as $exfld) {
                if (isset($category['structure_' . $exfld['field_name']])) {
                    cot_extrafield_unlinkfiles($category['structure_' . $exfld['field_name']], $exfld);
                }
            }

            Cot::$db->delete(Cot::$db->structure, 'structure_id = ?', [$category['structure_id']]);
            Cot::$db->delete(
                Cot::$db->config,
                "config_cat = ? AND config_subcat = ? AND config_owner = '"
                . ExtensionsDictionary::TYPE_MODULE . "'",
                [$category['structure_area'], $category['structure_code']]
            );

            if ($isModule) {
                cot_auth_remove_item($category['structure_area'], $category['structure_code']);
            }

            $deleteFunction = 'cot_' . $category['structure_area'] . '_deletecat';
            if (function_exists($deleteFunction)) {
                $deleteFunction($category['structure_code']);
            }

            cot_log(
                "Structure. Deleted category #{$id}: '{$category['structure_area']}' - '{$category['structure_code']}'",
                'adm',
                'structure',
                'delete'
            );

            /* === Hook === */
            foreach (cot_getextplugins('structure.delete.done') as $pl) {
                include $pl;
            }
            /* ===== */

            ItemService::getInstance()->onDelete(StructureDictionary::SOURCE_CATEGORY, $id);

            Cot::$db->commit();
        } catch (Throwable $e) {
            Cot::$db->rollBack();
            throw $e;
        }

        unset(Cot::$structure[$category['structure_area']][$category['structure_code']]);
        if (Cot::$cache) {
            // @todo it is not efficient to flush the entire cache
            Cot::$cache->clear();
        }

        return true;
    }
}