<?php
/**
 * Comments system for Cotonti
 * Admin index action
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\plugins\comments\controllers\admin\actions;

use Cot;
use cot\controllers\BaseAction;
use cot\exceptions\ForbiddenHttpException;
use cot\extensions\ExtensionsDictionary;
use cot\modules\users\inc\UsersRepository;
use cot\plugins\comments\controllers\admin\IndexController;
use cot\plugins\comments\inc\CommentsDtoRepository;
use XTemplate;

defined('COT_CODE') or die('Wrong URL');

/**
 * @property-read IndexController $controller
 */
class IndexAction extends BaseAction
{
    public function run(): string
    {
        global $L, $R, $Ls;

        require_once cot_incfile('comments', ExtensionsDictionary::TYPE_PLUGIN);

        [$auth['read'], $auth['write'], $auth['admin']] = cot_auth(ExtensionsDictionary::TYPE_PLUGIN, 'comments');

        if (!$auth['admin']) {
            throw new ForbiddenHttpException();
        }

        $perPage = (Cot::$cfg['maxrowsperpage'] && is_numeric(Cot::$cfg['maxrowsperpage']) && Cot::$cfg['maxrowsperpage'] > 0)
            ? (int) Cot::$cfg['maxrowsperpage']
            : 15;

        [$currentPage, $offset, $durl] = cot_import_pagenav('d', $perPage);

        $queryCondition = [];
        $queryParams = [];

        /* === Hook  === */
        foreach (cot_getextplugins('comments.admin.first') as $pl) {
            include $pl;
        }
        /* ===== */

        $totalItems = Cot::$db->countRows(Cot::$db->com);

        $pagination = cot_pagenav(
            'admin',
            ['m' => 'comments'],
            $offset,
            $totalItems,
            $perPage,
            'd',
            '',
            Cot::$cfg['jquery'] && Cot::$cfg['turnajax'],
        );

        if ($currentPage > $pagination['total']) {
            $urlParams = ['m' => 'comments'];
            if ($currentPage > 2) {
                $urlParams['d'] = $currentPage - 1;
            }
            cot_redirect(cot_url('admin', $urlParams, '', true));
         }

        $t = new XTemplate(cot_tplfile('comments.admin', ExtensionsDictionary::TYPE_PLUGIN, true));

        $t->assign([
            'COMMENTS_CONFIG_URL' => cot_url(
                'admin',
                ['m' => 'config', 'n' => 'edit', 'o' => ExtensionsDictionary::TYPE_PLUGIN, 'p' => 'comments']),
            'COMMENTS_IS_AJAX' => COT_AJAX && (Cot::$env['ext'] === 'comments'),
        ]);
        $t->assign(cot_generatePaginationTags($pagination));

        if ($totalItems === 0 || $pagination['onpage'] === 0) {
            $t->parse('MAIN');
            return $t->text('MAIN');
        }

        $commentsList = CommentsDtoRepository::getInstance()
            ->getDtoByCondition($queryCondition, $queryParams, 'com_id DESC', $perPage, $offset, true);


        $authorsIds = [];
        foreach ($commentsList as $key => $comment) {
            $authorId = $comment->data['com_authorid'];
            if ($authorId > 0 && !in_array($authorId, $authorsIds)) {
                $authorsIds[] = $authorId;
            }
        }

        $authors = [];
        if (class_exists(UsersRepository::class)) {
            $authors = UsersRepository::getInstance()->getByIds($authorsIds);
        }

        /* === Hook  === */
        foreach (cot_getextplugins('comments.admin.first') as $pl) {
            include $pl;
        }
        /* ===== */

        // Come back url params
        $currentUrlParams = ['m' => 'comments'];
        if ($currentPage > 1) {
            $currentUrlParams['d'] = $currentPage;
        }
        $currentRouteParamsEncoded = base64_encode(serialize(['admin', $currentUrlParams]));

        /* === Hook - Part1 : Set === */
        $extPlugins = cot_getextplugins('comments.admin.loop');
        /* ===== */

        $i = $offset;
        $kk = 0;
        foreach ($commentsList as $comment) {
            if ($comment->data['com_authorid'] > 0) {
                $author = !empty($authors[$comment->data['com_authorid']])
                    ? cot_build_user(
                        $authors[$comment->data['com_authorid']]['user_id'],
                        $authors[$comment->data['com_authorid']]['user_name']
                    )
                    : Cot::$L['Deleted'];
            } else {
                // Comment from guest
                $author = htmlspecialchars($comment->data['com_author']);
            }

            $editUrl = cot_url(
                'comments',
                ['a' => 'edit', 'id' => $comment->data['com_id'], 'cb' => $currentRouteParamsEncoded]
            );

            $deleteUrl =  cot_url(
                'comments',
                [
                    'a' => 'delete',
                    'id' => $comment->data['com_id'],
                    'cb' => $currentRouteParamsEncoded,
                    'x' => Cot::$sys['xk'],
                ],
            );
            $deleteConfirmUrl = cot_confirm_url($deleteUrl, 'comments', 'comments_confirm_delete');
            $ipSearch = cot_build_ipsearch($comment->data['com_authorip']);

            $title = $comment->getTitleHtml();
            if (empty($title)) {
                $title = $comment->title;
            }

            $t->assign([
                'COMMENTS_ROW_ID' => $comment->data['com_id'],
                'COMMENTS_ROW_TITLE' => $comment->getTitleHtml(),
                'COMMENTS_ROW_URL' => $comment->url,
                'COMMENTS_ROW_AUTHOR' => $author,
                'COMMENTS_ROW_TEXT' => cot_parse($comment->data['com_text'], Cot::$cfg['plugin']['comments']['markup']),
                'COMMENTS_ROW_DATE' => cot_date('datetime_medium', $comment->data['com_date']),
                'COMMENTS_ROW_DATE_STAMP' => $comment->data['com_date'],
                'COMMENTS_ROW_EDIT_URL' => $editUrl,
                'COMMENTS_ROW_EDIT' => cot_rc_link($editUrl, Cot::$L['Edit']),
                'COMMENTS_ROW_DELETE_URL' => $deleteConfirmUrl,
                'COMMENTS_ROW_DELETE' => cot_rc_link($deleteConfirmUrl, Cot::$L['Delete'], 'class="confirmLink"'),
                'COMMENTS_ROW_AUTHOR_IP' => $ipSearch,
                'COMMENTS_ROW_NUM' => $kk,
            ]);

            // Extrafields
            if (!empty(Cot::$extrafields[Cot::$db->com])) {
                foreach (Cot::$extrafields[Cot::$db->com] as $extraField) {
                    $tag = mb_strtoupper($extraField['field_name']);
                    $fieldTitle = cot_extrafield_title($extraField, 'comments_');

                    $t->assign([
                        'COMMENTS_ROW_' . $tag . '_TITLE' => $fieldTitle,
                        'COMMENTS_ROW_' . $tag => cot_build_extrafields_data(
                            'comments',
                            $extraField,
                            $comment->data['com_' . $extraField['field_name']]
                        ),
                        'COMMENTS_ROW_' . $tag . '_VALUE' => $comment->data['com_' . $extraField['field_name']]
                    ]);
                }
            }

            $t->assign(
                cot_generate_usertags(
                    $authors[$comment->data['com_authorid']] ?? null,
                    'COMMENTS_ROW_AUTHOR_',
                    htmlspecialchars($comment->data['com_author'])
                )
            );

            /* === Hook - Part2 : Include === */
            foreach ($extPlugins as $pl) {
                include $pl;
            }
            /* ===== */

            $t->parse('MAIN.COMMENTS_ROW');
        }


        cot_display_messages($t);

        /* === Hook  === */
        foreach (cot_getextplugins('comments.admin.tags') as $pl) {
            include $pl;
        }
        /* ===== */

        $t->parse('MAIN');
        return $t->text('MAIN');
    }
}