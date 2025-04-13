<?php
/**
 * Recent comments widget
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\plugins\comments\inc;

use Cot;
use cot\modules\users\inc\UsersRepository;

class RecentCommentsWidget extends BaseCommentsWidget
{
    /**
     * @var string
     */
    public $template = 'comments.recent.widget';

    public $limit = 10;

    public $linkToAdmin = true;

    /**
     * @todo ajax pagination
     */
    public function run(): string
    {
        if (!$this->auth['read']) {
            return '';
        }

        $currentUrlExtension = Cot::$env['ext'];
        $currentUrlParams = $_GET;
        unset($currentUrlParams['e']);
        if (isset($_GET['rwr'])) {
            unset($currentUrlParams['rwr']);
        }
        $currentRouteParamsEncoded = base64_encode(serialize([$currentUrlExtension, $currentUrlParams]));

        $queryCondition = [];
        $queryParams = [];

        $limit = $this->limit;
        $offset = 0;

        $t = $this->getTemplate();

        $totalItems = Cot::$db->countRows(Cot::$db->com);

        $t->assign([
            'TOTAL_ENTRIES' => $totalItems,
        ]);

        if ($totalItems === 0) {
            $t->parse('MAIN');
            return $t->text('MAIN');
        }

        $commentsList = CommentsDtoRepository::getInstance()
            ->getDtoByCondition($queryCondition, $queryParams, 'com_id DESC', $limit, $offset, true);

        $t->assign([
            'ENTRIES_ON_CURRENT_PAGE' => count($commentsList),
        ]);

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

        $t->assign([
            'COMMENTS_ADMIN_URL' => $this->auth['admin'] && $this->linkToAdmin
                ? cot_url('admin', ['m' => 'comments'])
                : '',
            'COMMENTS_AUTH_READ' => $this->auth['read'],
            'COMMENTS_AUTH_WRITE' => $this->auth['write'],
            'COMMENTS_AUTH_ADMIN' => $this->auth['admin'],
        ]);

        /* === Hook - Part1 : Set === */
        $extPlugins = cot_getextplugins('comments.recent.loop');
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

            $editUrl = $deleteUrl = $deleteConfirmUrl = $ipSearch = null;

            if ($this->auth['admin']) {
                $editUrl = cot_url(
                    'comments',
                    ['a' => 'edit', 'id' => $comment->data['com_id'], 'cb' => $currentRouteParamsEncoded]
                );

                $deleteUrl = cot_url(
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

        $t->parse('MAIN');
        return $t->text('MAIN');
    }
}