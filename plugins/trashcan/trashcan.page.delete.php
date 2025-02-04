<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.delete.done
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
 * @var array $pageData Deleting page data row. See \cot\modules\page\inc\PageService::delete()
 * @var array $pageDeletedMessage
 */

use cot\extensions\ExtensionsService;
use cot\modules\page\inc\PageDictionary;

defined('COT_CODE') or die('Wrong URL');

if (Cot::$cfg['plugin']['trashcan']['trash_page']) {
    global $lang;

    require_once cot_incfile('trashcan', 'plug');

    $tmpLang = null;
    if (!Cot::$cfg['forcedefaultlang'] && Cot::$cfg['defaultlang'] !== $lang) {
        $tmpLang = Cot::$L;
        $langFile = cot_langfile('comments', 'plug', Cot::$cfg['defaultlang'], Cot::$cfg['defaultlang']);
        if (!$langFile)  {
            $langFile = cot_langfile('comments', 'plug', 'en', 'en');
        }
        if ($langFile)  {
            include $langFile;
            Cot::$L = array_merge(Cot::$L, $L);
        }
    }

    // Add page to trash
    $trashcanId = cot_trash_put(
        PageDictionary::SOURCE_PAGE,
        Cot::$L['Page'] . " #" . $id . " " . $pageData['page_title'],
        $id, $pageData
    );

    $pageDeletedMessage['deleted'] = Cot::$L['page_deletedToTrash'];

    // ==============
    // @todo remove after implement https://github.com/Cotonti/Cotonti/issues/1826 for comments
    // And all it's comments
    if (ExtensionsService::getInstance()->isPluginActive('comments')) {
        require_once cot_incfile('comments', 'plug');

        $sql = Cot::$db->query(
            'SELECT * FROM ' . Cot::$db->quoteTableName(Cot::$db->com) .
            " WHERE com_area = '" . PageDictionary::SOURCE_PAGE . "' AND com_code = ?",
            [$id]
        );
        while ($comment = $sql->fetch()) {
            cot_trash_put(
                'comment',
                Cot::$L['comments_comment'] . " #" . $comment['com_id'] . " from page #" . $id,
                $comment['com_id'],
                $comment,
                $trashcanId
            );
        }
        $sql->closeCursor();
    }
    // /==============

    // And all it's translations
    if (ExtensionsService::getInstance()->isPluginActive('i18n')) {
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
                $trashcanId
            );
        }
        $sql->closeCursor();
    }
}
