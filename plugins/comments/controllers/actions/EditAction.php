<?php
/**
 * Comments system for Cotonti
 * Edit comment action
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\plugins\comments\controllers\actions;

use Cot;
use cot\controllers\BaseAction;
use cot\dto\ItemDto;
use cot\exceptions\ForbiddenHttpException;
use cot\exceptions\NotFoundHttpException;
use cot\extensions\ExtensionsDictionary;
use cot\plugins\comments\controllers\IndexController;
use cot\plugins\comments\inc\CommentsControlService;
use cot\plugins\comments\inc\CommentsDictionary;
use cot\plugins\comments\inc\CommentsDtoRepository;
use cot\plugins\comments\inc\CommentsNotificationService;
use cot\plugins\comments\inc\CommentsService;
use cot\users\UsersHelper;
use cot\users\UsersRepository;
use XTemplate;

defined('COT_CODE') or die('Wrong URL');

/**
 * @property-read IndexController $controller
 */
class EditAction extends BaseAction
{
    /**
     * @var ?ItemDto
     */
    private $commentDto = null;

    /**
     * @var string
     */
    private $comeback = '';

    /**
     * Extension code comeback url
     * @var string
     */
    private $cbExtensionCode = '';

    /**
     * Comeback URL parameters
     * @var array
     */
    private $cbUrlParams = [];

    /**
     * @var string
     */
    private $comeBackUrl = '';

    public function run(): string
    {
        [$auth['read'], $auth['write'], $auth['admin']] = cot_auth('plug', 'comments');

        if (!$auth['write']) {
            throw new ForbiddenHttpException();
        }

        $id = cot_import('id', 'G', 'INT');
        if (empty($id)) {
            throw new NotFoundHttpException();
        }

        $this->commentDto = CommentsDtoRepository::getInstance()->getById($id, true);
        if ($this->commentDto === null) {
            throw new NotFoundHttpException();
        }

        $comment = $this->commentDto->data;

        // Try to fetch $force_admin from session
        if (
            isset($_SESSION['cot_comments_force_admin'][$comment['com_area']][$comment['com_code']])
            && $_SESSION['cot_comments_force_admin'][$comment['com_area']][$comment['com_code']]
            && $auth['read']
            && $auth['write']
        ) {
            $auth['admin'] = true;
        }

        $isOwner = (Cot::$usr['id'] > 0 && $comment['com_authorid'] == Cot::$usr['id'])
            || (Cot::$usr['id'] === 0 && isset($_SESSION['cot_comments_edit'][$id]));

        $editTimeExpired = Cot::$sys['now'] > ($comment['com_date'] + Cot::$cfg['plugin']['comments']['time'] * 60);

        if (!$auth['admin'] && !($isOwner && $auth['write'])) {
            throw new NotFoundHttpException();
        }

       $this->prepareComeBack();

        if (!$auth['admin'] && $editTimeExpired) {
            if (!empty($this->cbExtensionCode)) {
                cot_error(Cot::$L['comments_editTimeExpired']);
                cot_redirect(cot_url($this->cbExtensionCode, $this->cbUrlParams, '#comments', true));
            } else {
                throw new ForbiddenHttpException();
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update();
        }

        return $this->renderEditForm();
    }

    private function prepareComeBack(): void
    {
        $this->comeback = cot_import('cb', 'G', 'TXT');
        if (empty($this->comeback)) {
            $this->comeback = cot_import('cb', 'P', 'TXT');
            unset($_POST['cb']);
        }

        // Comeback url params
        if (!empty($this->comeback)) {
            $cbDecoded = unserialize(base64_decode($this->comeback));
            $this->cbExtensionCode = $cbDecoded[0];
            $this->cbUrlParams = $cbDecoded[1];
            $this->comeBackUrl = cot_url($this->cbExtensionCode, $this->cbUrlParams, '', true);
        }

        if (!empty($this->comeBackUrl)) {
            return;
        }

        $comeBackUrl = cot_import('cbu', 'P', 'TXT');
        unset($_POST['cbu']);
        if (!empty($comeBackUrl)) {
            $this->comeBackUrl = $comeBackUrl;
            return;
        }

        // It is currently difficult to extract URL parameters with the Url-editor enabled.
        // So, will use the URL "as is".
        $refUrl = str_replace(COT_ABSOLUTE_URL, '', $_SERVER['HTTP_REFERER']);

        $useRefUrl = false;
        if (
            mb_stripos($refUrl, '/comments') === false
            && mb_stripos($refUrl, 'e=comments') === false
        ) {
            $useRefUrl = true;
        }

        // we come from comments extension
        if (
            !$useRefUrl
            && mb_stripos($refUrl, 'a=edit') === false
            && mb_stripos($refUrl, 'a=delete') === false
        ) {
            $useRefUrl = true;
        }

        if ($useRefUrl) {
            $parsedUrl = parse_url($refUrl);
            $this->comeBackUrl = ltrim($parsedUrl['path'], '/');
            if (!empty($parsedUrl['query'])) {
                $this->comeBackUrl .= '?' . $parsedUrl['query'];
            }
        }
    }

    private function renderEditForm(): string
    {
        global $L, $R, $Ls;

        $comment = $this->commentDto->data;

        $templateFile = cot_tplfile(['comments', 'edit', $comment['com_area']], ExtensionsDictionary::TYPE_PLUGIN);

        $itemTitle = $this->commentDto->getTitleHtml();
        if (empty($itemTitle)) {
            $itemTitle = $this->commentDto->title;
        }

        $author = null;
        if ($comment['com_authorid'] > 0) {
            $author = UsersRepository::getInstance()->getById($comment['com_authorid']);
        }

        $authorName = $comment['com_authorid'] > 0
            ? htmlspecialchars(UsersHelper::getInstance()->getFullName($author))
            : htmlspecialchars($comment['com_author']);

        $t = new XTemplate($templateFile);

        $editor = (Cot::$cfg['plugin']['comments']['markup']) ? 'input_textarea_minieditor' : '';

        $formParams = '';
        if (!empty($this->comeback)) {
            $formParams .= cot_inputbox('hidden', 'cb', $this->comeback);
        } elseif (!empty($this->comeBackUrl)) {
            $formParams .= cot_inputbox('hidden', 'cbu', $this->comeBackUrl);
        }

        $t->assign([
            'TITLE' => cot_rc(Cot::$L['comments_editTitle'], ['title' => mb_lcfirst($itemTitle)]),

            'BACK_URL' => !empty($this->comeBackUrl)
                ? $this->comeBackUrl . '#comments'
                : $this->commentDto->url,
            'COMMENT_FORM_ACTION' => cot_url(
                'comments',
                ['a' => 'edit', 'id' => $comment['com_id']]
            ),
            'COMMENT_FORM_PARAMS' => $formParams,
            'COMMENT_FORM_TEXT' => cot_textarea(
                'comment_text',
                $comment['com_text'],
                12,
                64,
                '',
                $editor
            ),

            // @todo editable poster name for guests
            'AUTHOR' => $authorName,
            'COMMENT_IP' => $comment['com_authorip'],
            'COMMENT_DATE' => cot_date('datetime_medium', $comment['com_date']),
            'COMMENT_DATE_STAMP' => $comment['com_date'],
        ]);

        // Extra fields
        if (!empty(Cot::$extrafields[Cot::$db->com])) {
            foreach (Cot::$extrafields[Cot::$db->com] as $extraField) {
                $uname = strtoupper($extraField['field_name']);
                $fieldFormElement = cot_build_extrafields(
                    'comment_' . $extraField['field_name'],
                    $extraField,
                    $comment['com_' . $extraField['field_name']]
                );
                $fieldTitle = cot_extrafield_title($extraField, 'comments_');

                $t->assign([
                    'COMMENT_FORM_' . $uname => $fieldFormElement,
                    'COMMENT_FORM_' . $uname . '_TITLE' => $fieldTitle,
                    'COMMENT_FORM_EXTRA_FILED' => $fieldFormElement,
                    'COMMENT_FORM_EXTRA_FILED_TITLE' => $fieldTitle
                ]);
                $t->parse('MAIN.EXTRA_FILED');
            }
        }

        $t->assign(cot_generate_usertags($author, 'AUTHOR_'));

        /* == Hook == */
        foreach (cot_getextplugins('comments.edit.tags') as $pl) {
            include $pl;
        }
        /* ===== */

        cot_display_messages($t, 'MAIN');

        $t->parse('MAIN');

        return $t->text('MAIN');
    }

    private function update(): void
    {
        $data = [];
        $comment = &$this->commentDto->data;

        /* == Hook == */
        foreach (cot_getextplugins('comments.edit.update.first') as $pl) {
            include $pl;
        }
        /* ===== */

        $data['com_text'] = cot_import('comment_text', 'P', 'HTM');

        if (!empty(Cot::$extrafields[Cot::$db->com])) {
            foreach (Cot::$extrafields[Cot::$db->com] as $exfld) {
                $data['com_' . $exfld['field_name']] = cot_import_extrafields('comment_' . $exfld['field_name'],
                    $exfld, 'P', '', 'comments_');
            }
        }

        foreach ($data as $key => $value) {
            $comment[$key] = $value;
        }

        $service = CommentsService::getInstance();
        $service->validateWithMessages($comment);

        /* == Hook == */
        foreach (cot_getextplugins('comments.edit.update.validate') as $pl) {
            include $pl;
        }
        /* ===== */

        unset($_POST['cb']);

        if (cot_error_found()) {
            return;
        }

        /**
         * We can also try getting it from the session. For now, we assume that the URL of the commented object
         * matches the return URL.
         * The priority should be as follows: 'ci' (commented item url) from the request,
         * if not available, then from the session, and 'cb' (comeback url) as the last option.
         */
        $ciExtensionCode = $ciUrlParams = null;

        if (
            !defined('COT_ADMIN')
            && !in_array($this->cbExtensionCode, ['index', 'comments'])
        ) {
            $ciExtensionCode = $this->cbExtensionCode;
            $ciUrlParams = $this->cbUrlParams;
        }

        $saved = CommentsControlService::getInstance()->save(
            $comment['com_id'],
            $comment,
            $ciExtensionCode,
            $ciUrlParams,
            $this->comeBackUrl
        );

        /* == Hook == */
        foreach (cot_getextplugins('comments.edit.update.done') as $pl) {
            include $pl;
        }
        /* ===== */

        if ($saved) {
            cot_message(Cot::$L['comments_saved']);

            if (Cot::$cfg['plugin']['comments']['mail']) {
                CommentsNotificationService::getInstance()->notifyAdmins(
                    $comment,
                    CommentsDictionary::EVENT_UPDATE,
                    $ciExtensionCode,
                    $ciUrlParams
                );
            }
        }

        if (!empty($this->comeBackUrl)) {
            cot_redirect($this->comeBackUrl . '#com' . $comment['com_id']);
        } else {
            cot_redirect(str_replace('&amp;', '&', $this->commentDto->url));
        }
    }
}