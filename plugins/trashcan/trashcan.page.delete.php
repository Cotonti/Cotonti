<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.delete.first
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
 * @var array $pageData Deleting page data row. See \cot\modules\page\inc\PageControlService::delete()
 * @var array $pageDeletedMessage
 */

use cot\extensions\ExtensionsDictionary;
use cot\extensions\ExtensionsService;
use cot\modules\page\inc\PageDictionary;
use cot\plugins\trashcan\inc\TrashcanService;

defined('COT_CODE') or die('Wrong URL');

if (!Cot::$cfg['plugin']['trashcan']['trash_page']) {
    return;
}

global $lang;

require_once cot_incfile('trashcan', 'plug');

$tmpLang = null;
if (!Cot::$cfg['forcedefaultlang'] && Cot::$cfg['defaultlang'] !== $lang) {
    $tmpLang = Cot::$L;
    $langFile = cot_langfile(
        'page',
        ExtensionsDictionary::TYPE_MODULE,
        Cot::$cfg['defaultlang'],
        Cot::$cfg['defaultlang']
    );
    if (!$langFile)  {
        $langFile = cot_langfile('page', ExtensionsDictionary::TYPE_MODULE, 'en', 'en');
    }
    if ($langFile)  {
        include $langFile;
        Cot::$L = array_merge(Cot::$L, $L);
    }
}

$trashcan = TrashcanService::getInstance();

// Add page to trash
$pageData['page_alias'] = $pageData['page_alias'] . '_deleted_' . $id;
$trashcanId = $trashcan->put(
    PageDictionary::SOURCE_PAGE,
    Cot::$L['Page'] . " #" . $id . " " . $pageData['page_title'],
    (string) $id,
    $pageData
);

$pageDeletedMessage['deleted'] = Cot::$L['page_deletedToTrash'];

// And all it's translations
if (ExtensionsService::getInstance()->isPluginActive('i18n')) {
    require_once cot_incfile('i18n', 'plug');

    $sql = Cot::$db->query(
        'SELECT * FROM ' . Cot::$db->quoteTableName(Cot::$db->i18n_pages) . ' WHERE ipage_id = ?',
        [$id]
    );
    while ($row = $sql->fetch()) {
        // @todo title on site's default language
        $trashcan->put(
            'i18n_page',
            Cot::$L['i18n_translation'] . " #" . $row['ipage_id'] . " for page #" . $id,
            (string) $row['ipage_id'],
            $row,
            $trashcanId
        );
    }
    $sql->closeCursor();
}

if ($tmpLang !== null) {
    Cot::$L = $tmpLang;
}
