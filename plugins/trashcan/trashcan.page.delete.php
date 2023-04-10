<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.edit.delete.done
Order=7
[END_COT_EXT]
==================== */

/**
 * Trashcan delete page
 *
 * @package TrashCan
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var int $id Deleting page id
 * @var array $rpage Deleting page data row
 */

defined('COT_CODE') or die('Wrong URL');

if (Cot::$cfg['plugin']['trashcan']['trash_page']) {
    // We are inside cot_page_delete() function, so need some globals
    global $trash_types, $db_trash, $db_x, $L, $Ls, $R;

    require_once cot_incfile('trashcan', 'plug');

    // Add page to trash
    $parentTrashId = cot_trash_put(
        'page',
        Cot::$L['Page'] . " #" . $id . " " . $rpage['page_title'],
        $id, $rpage
    );

    // And all it's comments
    if (cot_plugin_active('comments')) {
        require_once cot_incfile('comments', 'plug');

        $sql = Cot::$db->query(
            'SELECT * FROM ' . Cot::$db->quoteTableName(Cot::$db->com) .
            " WHERE com_area = 'page' AND com_code = ?",
            [$id]
        );
        while ($comment = $sql->fetch()) {
            cot_trash_put(
                'comment',
                Cot::$L['comments_comment'] . " #" . $comment['com_id'] . " from page #" . $id,
                $comment['com_id'],
                $comment,
                $parentTrashId
            );
        }
        $sql->closeCursor();
    }

    // And all it's translations
    if (cot_plugin_active('i18n')) {
        require_once cot_incfile('i18n', 'plug');

        $sql = Cot::$db->query(
            'SELECT * FROM ' . Cot::$db->quoteTableName(Cot::$db->i18n_pages) . ' WHERE ipage_id = ?',
            [$id]
        );
        while ($row = $sql->fetch()) {
            cot_trash_put(
                'i18n_page',
                Cot::$L['i18n_translation'] . " #" . $row['ipage_id'] . " for page #" . $id,
                $row['ipage_id'],
                $row,
                $parentTrashId
            );
        }
        $sql->closeCursor();
    }
}
