<?php
/**
 * Comments widget for a specific item
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\plugins\comments\inc;

use Cot;
use cot\modules\page\inc\PageDictionary;
use cot\modules\users\inc\UsersRepository;
use Resources;

class CommentsWidget extends BaseCommentsWidget
{
    /**
     * @var ?string
     */
    public $source = null;

    /**
     * @var ?string
     */
    public $sourceId = null;

    /**
     * @var ?string
     */
    public $extensionCode = null;

    /**
     * @var ?string
     */
    public $categoryCode = null;

    /**
     * @var bool
     */
    public $forceAdmin = false;

    /**
     * URL extension code of the current page where the comment widget is displayed.
     * @var string
     */
    private $currentUrlExtension = '';

    /**
     * URL parameters of the current page where the comment widget is displayed.
     * @var array
     */
    private $currentUrlParams = [];

    /**
     * URL parameters to directly request the HTML of this widget.
     * @var array
     */
    private $ajaxUrlParams = [];

    public function run(): string
    {
        if ($this->auth['read'] && $this->auth['write'] && $this->forceAdmin) {
            $this->auth['admin'] = true;
            $_SESSION['cot_comments_force_admin'][$this->source][$this->sourceId] = true;
        }

        $enabled = empty($this->extensionCode)
            || CommentsService::getInstance()->isEnabled($this->extensionCode, $this->categoryCode);

        if (!$this->auth['read']) {
            return '';
        }

        // Get the URL parameters of the current page where the comment widget is displayed.
        if (COT_AJAX && Cot::$env['ext'] === 'comments' && isset($_GET['ci'])) {
            $ci = cot_import('ci', 'G', 'TXT');
            if (!empty($ci)) {
                $ci = unserialize(base64_decode($ci));
                $this->currentUrlExtension = $ci[0];
                $this->currentUrlParams = $ci[1];
            }
        } elseif (
            Cot::$env['ext'] !== 'comments'
            || !isset($_GET['a'])
            || $_GET['a'] !== 'display'
        ) {
            $this->currentUrlExtension = Cot::$env['ext'];
            $this->currentUrlParams = $_GET;
            unset($this->currentUrlParams['e']);
            if (isset($_GET['rwr'])) {
                unset($this->currentUrlParams['rwr']);
            }
        }

        // Get the URL parameters to directly request the HTML of this widget.
        $this->ajaxUrlParams = [
            'a' => 'display',
            'source' => $this->source,
            'source-id' => $this->sourceId,
        ];

        if (!empty($this->extensionCode)) {
            $this->ajaxUrlParams['ext'] = $this->extensionCode;
        }
        if (!empty($this->categoryCode)) {
            $this->ajaxUrlParams['cat'] = $this->categoryCode;
        }

        $currentPage = cot_import($this->paginationParam, 'G', 'INT');
        if ($currentPage > 1) {
            $this->ajaxUrlParams[$this->paginationParam] = $currentPage;
        }

        if (!COT_AJAX) {
            Resources::linkFileFooter(Cot::$cfg['plugins_dir'] . '/comments/js/comments.js');
            Resources::embedFooter(
                "cot.L.comments_saveError = '" . Cot::$L['comments_saveError'] . "';"
                . "cot.L.comments_tooShort = '" . Cot::$L['comments_tooShort'] . "';"
                . "cot.L.comments_authorTooShort = '" . Cot::$L['comments_authorTooShort'] . "';"
                . "cot.L.captcha_verification_failed = '" . Cot::$L['captcha_verification_failed'] . "';"
            );
        }

        /* == Hook == */
        foreach (cot_getextplugins('comments.main') as $pl) {
            include $pl;
        }
        /* ===== */

        // List
        $this->list();

        $t = $this->getTemplate();

        $t->assign([
            'COMMENTS_SOURCE' => $this->source,
            'COMMENTS_SOURCE_ID' => $this->sourceId,
            'COMMENTS_IS_AJAX' => COT_AJAX,
            'COMMENTS_DISPLAY' => Cot::$cfg['plugin']['comments']['expand_comments'] ? '' : 'none',
        ]);

        if (($this->auth['write'] && $enabled) || $this->auth['admin']) {
            // Commented Item url params
            $currentRouteParams = [$this->currentUrlExtension, $this->currentUrlParams];
            $currentRouteParamsEncoded = base64_encode(serialize($currentRouteParams));
            $containerParams = [
                'data-url-params' => $currentRouteParamsEncoded,
                //'data-params' => base64_encode(json_encode($refreshUrlParams)),
            ];

            $containerParamsPrepared = ' ';
            foreach ($containerParams as $name => $value) {
                $containerParamsPrepared .= $name . '="' . $value . '" ';
            }

            $refreshUrlParams = $this->ajaxUrlParams;
            $refreshUrlParams['ci'] = $currentRouteParamsEncoded;

            $blockParams = [
                'data-refresh' => base64_encode(cot_url('comments', $refreshUrlParams, '', true)),

            ];

            $blockParamsPrepared = ' ';
            foreach ($blockParams as $name => $value) {
                $blockParamsPrepared .= $name . '="' . $value . '" ';
            }

            $t->assign([
                'COMMENTS_BLOCK_PARAMS' => $blockParamsPrepared,
                'COMMENTS_CONTAINER_PARAMS' => $containerParamsPrepared,
            ]);
        }

        // Form
        if ($this->auth['write'] && $enabled) {
            $this->form();
        } else{
            if (!$enabled) {
                $message = Cot::$L['comments_closed'];
            } else {
                $message = Cot::$usr['id'] === 0
                    ? Cot::$L['comments_registeredOnly']
                    : Cot::$L['comments_noRights'];
            }

            $t->assign('COMMENTS_CLOSED', $message);
            $t->parse('MAIN.COMMENTS_CLOSED');
        }

        /* == Hook == */
        foreach (cot_getextplugins('comments.tags') as $pl) {
            include $pl;
        }
        /* ===== */

        $t->parse('MAIN');
        return $t->text('MAIN');
    }

    private function list(): void
    {
        $perPage = (int) Cot::$cfg['plugin']['comments']['maxcommentsperpage'];

        [$currentPage, $offset, $durl] = cot_import_pagenav($this->paginationParam, $perPage);

        $queryCondition = ['source' => 'com_area = :source', 'sourceId' => 'com_code = :sourceId'];
        $queryParams = ['source' => $this->source, 'sourceId' => $this->sourceId];

        $orderWay = Cot::$cfg['plugin']['comments']['order'] === 'Chronological' ? 'ASC' : 'DESC';
        $queryOrder['id'] = "com_id $orderWay";

        $commentsRepository = CommentsRepository::getInstance();

        /* == Hook == */
        foreach (cot_getextplugins('comments.query.before') as $pl) {
            include $pl;
        }
        /* ===== */

        $sqlColumns = !empty($queryColumns) ? ', ' . implode(', ', $queryColumns) : '';

        $sqlJoinTables = !empty($queryJoinTables) ? "\n" . implode("\n", $queryJoinTables) : '';

        $preparedCondition = Cot::$db->prepareCondition($queryCondition);
        $sqlWhere = !empty($preparedCondition) ? "WHERE $preparedCondition" : '';

        $sqlOrder = '';
        if (!empty($queryOrder)) {
            $sqlOrder = ' ORDER BY ' . (is_array($queryOrder) ? implode(', ', $queryOrder) : $queryOrder);
        }

        /* == Hook == */
        foreach (cot_getextplugins('comments.query') as $pl) {
            include $pl;
        }
        /* ===== */

        $table = Cot::$db->quoteTableName(Cot::$db->com);

        // @todo group by, having
        // @todo move query to repository
        $sql = "SELECT COUNT(*) "
            . "FROM $table "
            . $sqlJoinTables . $sqlWhere . $sqlOrder;

        $totalItems = (int) Cot::$db->query($sql, $queryParams)->fetchColumn();

        $currentRouteParams = base64_encode(serialize([$this->currentUrlExtension, $this->currentUrlParams]));

        $pagination = cot_pagenav(
            $this->currentUrlExtension,
            $this->currentUrlParams,
            $offset,
            $totalItems,
            $perPage,
            $this->paginationParam,
            '#comments',
            Cot::$cfg['jquery'] && Cot::$cfg['turnajax'],
            'comments',
            'comments',
            [
                'a' => 'display',
                'source' => $this->source,
                'source-id' => $this->sourceId,
                'ext' => $this->extensionCode,
                'cat' => $this->categoryCode,
                'ci' => $currentRouteParams,
            ]
        );

        if ($currentPage > $pagination['total']) {
            if (COT_AJAX && Cot::$env['ext'] === 'comments') {
                $urlParams = $this->ajaxUrlParams;
                $urlParams[$this->paginationParam] = $pagination['total'];
                cot_redirect(cot_url('comments', $urlParams, '', true));
            } else {
                $urlParams = $this->currentUrlParams;
                $urlParams[$this->paginationParam] = $pagination['total'];
                cot_redirect(cot_url($this->currentUrlExtension, $urlParams, '', true));
            }
        }

        $t = $this->getTemplate();

        $t->assign(cot_generatePaginationTags($pagination));

        if ($totalItems === 0) {
            return;
        }

        // @todo group by, having
        // @todo move query to repository
        $sql = "SELECT {$table}.* $sqlColumns "
            . "FROM $table "
            . $sqlJoinTables . $sqlWhere . $sqlOrder
            . " LIMIT $perPage OFFSET $offset ";

        $commentsList = Cot::$db->query($sql, $queryParams)->fetchAll();

        /* == Hook == */
        foreach (cot_getextplugins('comments.query.done') as $pl) {
            include $pl;
        }
        /* ===== */

        if (empty($commentsList)) {
            return;
        }

        $authorsIds = [];
        foreach ($commentsList as $key => $comment) {
            $comment = $commentsRepository->afterFetch($comment);
            $commentsList[$key] = $comment;

            $authorId = $comment['com_authorid'];
            if ($authorId > 0 && !in_array($authorId, $authorsIds)) {
                $authorsIds[] = $authorId;
            }
        }

        $authors = [];
        if (class_exists(UsersRepository::class)) {
            $authors = UsersRepository::getInstance()->getByIds($authorsIds);
        }

        // Come back url params
        $comBackParams = $this->currentUrlParams;
        if ($currentPage > 1) {
            $comBackParams[$this->paginationParam] = $currentPage;
        }
        $currentRouteParamsEncoded = base64_encode(serialize([$this->currentUrlExtension, $comBackParams]));

        /* === Hook - Part1 : Set === */
        $extp = cot_getextplugins('comments.loop');
        /* ===== */

        $i = $offset;
        $kk = 0;
        foreach ($commentsList as $comment) {
            $i++;
            $kk++;

            if ($comment['com_authorid'] > 0) {
                $author = !empty($authors[$comment['com_authorid']])
                    ? cot_build_user(
                        $authors[$comment['com_authorid']]['user_id'],
                        $authors[$comment['com_authorid']]['user_name']
                    )
                    : Cot::$L['Deleted'];
            } else {
                // Comment from guest
                $author = htmlspecialchars($comment['com_author']);
            }

            $isCommentOwner = (Cot::$usr['id'] > 0 && $comment['com_authorid'] === Cot::$usr['id'])
                || (
                    Cot::$usr['id'] === 0
                    && !empty($_SESSION['cot_comments_edit'][$comment['com_id']])
                    && Cot::$usr['ip'] === $comment['com_authorip']
                );

            $timeLeft = null;
            $timeLeftFormatted = null;
            if (!$this->auth['admin'] && $isCommentOwner) {
                $timeLeft = ($comment['com_date'] + Cot::$cfg['plugin']['comments']['time'] * 60) - Cot::$sys['now'];

                if ($timeLeft > 1) {
                    $timeLeftFormatted = cot_rc(
                        Cot::$L['comments_timeLeft'],
                        ['time' => cot_build_timegap(Cot::$sys['now'], Cot::$sys['now'] + $timeLeft)]
                    );
                }
            }

            $canEdit = $this->auth['admin'] || ($isCommentOwner && $timeLeft > 1);
            $editUrl = null;
            $editComment = null;
            if ($canEdit) {
                $editUrl = cot_url(
                    'comments',
                    ['a' => 'edit', 'id' => $comment['com_id'], 'cb' => $currentRouteParamsEncoded]
                );
                $editComment = cot_rc(
                        'comments_code_edit',
                        [
                            'edit_url' => $editUrl,
                            'allowed_time' => $timeLeftFormatted !== null ? ' - ' . $timeLeftFormatted : null,
                        ]
                    );
            }

            $deleteConfirmUrl = null;
            $ipSearch = null;
            if ($this->auth['admin']) {
                $deleteUrl =  cot_url(
                    'comments',
                    [
                        'a' => 'delete',
                        'id' => $comment['com_id'],
                        'cb' => $currentRouteParamsEncoded,
                        'x' => Cot::$sys['xk'],
                    ],
                );
                $deleteConfirmUrl = cot_confirm_url($deleteUrl, 'comments', 'comments_confirm_delete');
                $ipSearch = cot_build_ipsearch($comment['com_authorip']);
            }

            if (
                Cot::$usr['id'] === 0
                && $isCommentOwner
                && class_exists(PageDictionary::class)
                && $comment['com_area'] === PageDictionary::SOURCE_PAGE
                && Cot::$cfg['cache_page']
            ) {
                Cot::$cfg['cache_page'] = Cot::$cfg['cache_index'] = false;
            }

            $t->assign([
                'COMMENTS_ROW_ID' => $comment['com_id'],
                'COMMENTS_ROW_ORDER' => Cot::$cfg['plugin']['comments']['order'] == 'Recent' ? $totalItems - $i + 1 : $i,
                'COMMENTS_ROW_URL' => cot_url($this->currentUrlExtension, $this->currentUrlParams, '#com' . $comment['com_id']),
                'COMMENTS_ROW_AUTHOR' => $author,
                'COMMENTS_ROW_TEXT' => cot_parse($comment['com_text'], Cot::$cfg['plugin']['comments']['markup']),
                'COMMENTS_ROW_DATE' => cot_date('datetime_medium', $comment['com_date']),
                'COMMENTS_ROW_DATE_STAMP' => $comment['com_date'],
                'COMMENTS_ROW_EDIT_URL' => $editUrl,
                'COMMENTS_ROW_EDIT' => $editComment,
                'COMMENTS_ROW_DELETE_URL' => $deleteConfirmUrl,
                'COMMENTS_ROW_DELETE' => $deleteConfirmUrl !== null
                    ? cot_rc_link($deleteConfirmUrl, Cot::$L['Delete'], 'class="confirmLink"')
                    : '',
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
                            $comment['com_' . $extraField['field_name']]
                        ),
                        'COMMENTS_ROW_' . $tag . '_VALUE' => $comment['com_' . $extraField['field_name']]
                    ]);
                }
            }

            $t->assign(
                cot_generate_usertags(
                    $authors[$comment['com_authorid']] ?? null,
                    'COMMENTS_ROW_AUTHOR_',
                    htmlspecialchars($comment['com_author'])
                )
            );

            /* === Hook - Part2 : Include === */
            foreach ($extp as $pl) {
                include $pl;
            }
            /* ===== */

            $t->parse('MAIN.COMMENTS_ROW');
        }
    }

    private function form(): void
    {
        global $L, $R, $Ls, $cot_captcha;

        $editor = (Cot::$cfg['plugin']['comments']['markup']) ? 'input_textarea_minieditor' : '';

        $hiddenParams = cot_inputbox('hidden', 'source', $this->source)
            . cot_inputbox('hidden', 'source-id', $this->sourceId);

        if (!empty($this->extensionCode)) {
            $hiddenParams .= cot_inputbox('hidden', 'extension', $this->extensionCode);
        }

        if (!empty($this->categoryCode)) {
            $hiddenParams .= cot_inputbox('hidden', 'category', $this->categoryCode);
        }

        $formHint = '';
        if (!$this->auth['admin']) {
            $allowedTime = cot_build_timegap(
                Cot::$sys['now'] - Cot::$cfg['plugin']['comments']['time'] * 60,
                Cot::$sys['now']
            );
            $formHint = cot_rc('comments_editHint', ['time' => $allowedTime]);
        }
        $t = $this->getTemplate();

        $t->assign([
            'COMMENT_FORM_ACTION' => cot_url(
                'plug',
                ['e' => 'comments', 'a' => 'add', '_ajax' => 1]
            ),
            'COMMENT_FORM_AUTHOR' => (Cot::$usr['id'] > 0)
                ? Cot::$usr['name']
                : cot_inputbox('text', 'comment_author'),
            'COMMENT_FORM_AUTHOR_ID' => Cot::$usr['id'],
            'COMMENT_FORM_TEXT' => cot_textarea('comment_text', '', null, null, '', $editor)
                . $hiddenParams,
            'COMMENT_FORM_HINT' => $formHint,
        ]);

        // Extra fields
        if (!empty(Cot::$extrafields[Cot::$db->com])) {
            foreach (Cot::$extrafields[Cot::$db->com] as $extraField) {
                $uname = strtoupper($extraField['field_name']);
                $fieldFormElement = cot_build_extrafields('comment_' . $extraField['field_name'], $extraField, '');
                $fieldTitle = cot_extrafield_title($extraField, 'comments_');

                $t->assign([
                    'COMMENT_FORM_' . $uname => $fieldFormElement,
                    'COMMENT_FORM_' . $uname . '_TITLE' => $fieldTitle,
                    'COMMENT_FORM_EXTRA_FIELD' => $fieldFormElement,
                    'COMMENT_FORM_EXTRA_FIELD_TITLE' => $fieldTitle,
                ]);
                $t->parse('MAIN.NEW_COMMENT.EXTRA_FIELD');
            }
        }

        if (Cot::$usr['id'] === 0 && !empty($cot_captcha)) {
            $captchaTags = cot_generateCaptchaTags(null, 'rverify', 'COMMENT_FORM_');
            $t->assign($captchaTags);
        }

        /* == Hook == */
        foreach (cot_getextplugins('comments.newcomment.tags') as $pl) {
            include $pl;
        }
        /* ===== */

        if (Cot::$usr['id'] === 0) {
            $t->parse('MAIN.NEW_COMMENT.GUEST');

            // Don't cache page with messages and alerts.
            // Note messages can be empty at this stage
            if (cot_check_messages() && Cot::$cache) {
                Cot::$cache->static->disable();
            }
        }

        cot_display_messages($t, 'MAIN.NEW_COMMENT');

        $t->parse('MAIN.NEW_COMMENT');
    }
}