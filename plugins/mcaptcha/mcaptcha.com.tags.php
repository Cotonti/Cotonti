<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=comments.newcomment.tags
Tags=comments.tpl:{COMMENTS_FORM_VERIFY_IMG};comments.tpl:{COMMENTS_FORM_VERIFY_INPUT}
[END_COT_EXT]
==================== */

/**
 * mCAPTCHA tags for comments form
 *
 * @package MathCaptcha
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var XTemplate $t
 */

defined('COT_CODE') or die("Wrong URL.");

if ($usr['id'] === 0 && $cfg['captchamain'] === 'mcaptcha') {
    $captchaTags = cot_generateCaptchaTags(null, 'rverify', 'COMMENTS_FORM_');
    $t->assign($captchaTags);

    if (isset($cfg['legacyMode']) && $cfg['legacyMode']) {
        // @deprecated in 0.9.24
        $t->assign([
            'COMMENTS_FORM_VERIFYIMG' => $captchaTags['COMMENTS_FORM_VERIFY_IMG'],
            'COMMENTS_FORM_VERIFYINPUT' => cot_inputbox('text', 'rverify', '', 'size="10" maxlength="20"'),
        ]);
    }
}
