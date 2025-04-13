<?php
/**
 * Static and dynamic resource (e.g. HTML) strings. Can be overriden by skin files and other code.
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

$R['comments_code_edit'] = '<a href="{$edit_url}">' . Cot::$L['Edit'] . '</a> {$allowed_time}';

if (empty($R['icon_comments'])) {
    $R['icon_comments'] = '<img class="icon" src="' . Cot::$cfg['icons_dir'] . '/' . Cot::$cfg['defaulticons'] .
        '/16/comments.png" alt="' . Cot::$L['comments_comments'] . '" />';
}

$R['icon_comments_cnt'] = $R['icon_comments'] . ' ({$count})';

$R['comments_link'] = '<a href="{$url}" class="comments_link" title="' . Cot::$L['comments_comments'] . '">' .
    $R['icon_comments_cnt'] . '</a>';


