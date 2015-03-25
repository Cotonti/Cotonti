<?php
/**
 * English Language File for the Page Module (page.en.lang.php)
 *
 * @package Page
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Module Config
 */

$L['cfg_autovalidate'] = 'Autovalidate page';
$L['cfg_autovalidate_hint'] = 'Autovalidate page if poster has admin rights for page category';
$L['cfg_count_admin'] = 'Count Administrators\' hits';
$L['cfg_count_admin_hint'] = '';
$L['cfg_maxlistsperpage'] = 'Max. lists per page';
$L['cfg_maxlistsperpage_hint'] = '';
$L['cfg_order'] = 'Sorting column';
$L['cfg_title_page'] = 'Page title tag format';
$L['cfg_title_page_hint'] = 'Options: {TITLE}, {CATEGORY}';
$L['cfg_way'] = 'Sorting direction';
$L['cfg_truncatetext'] = 'Set truncated page text length in list';
$L['cfg_truncatetext_hint'] = 'Zero to disable this feature';
$L['cfg_allowemptytext'] = 'Allow empty page text';
$L['cfg_keywords'] = 'Keywords';

$L['info_desc'] = 'Enables website content through pages and page categories';

/**
 * Structure Confing
 */

$L['cfg_order_params'] = array(); // Redefined in cot_page_config_order()
$L['cfg_way_params'] = array($L['Ascending'], $L['Descending']);
$L['cfg_metatitle'] = 'Meta title';
$L['cfg_metadesc'] = 'Meta description';

/**
 * Admin Page Section
 */

$L['adm_queue_deleted'] = 'Page was deleted in to the trash can';
$L['adm_valqueue'] = 'Waiting for validation';
$L['adm_validated'] = 'Already validated';
$L['adm_expired'] = 'Expired';
$L['adm_structure'] = 'Structure of the pages (categories)';
$L['adm_sort'] = 'Sort';
$L['adm_sortingorder'] = 'Set a default sorting order for the categories';
$L['adm_showall'] = 'Show all';
$L['adm_help_page'] = 'The pages that belong to the category &quot;system&quot; are not displayed in the public listings, it\'s to make standalone pages.';
$L['adm_fileyesno'] = 'File (yes/no)';
$L['adm_fileurl'] = 'File URL';
$L['adm_filecount'] = 'File hit count';
$L['adm_filesize'] = 'File size';

/**
 * Page add and edit
 */

$L['page_addtitle'] = 'Submit new page';
$L['page_addsubtitle'] = 'Fill out all required fields and submit the form to continue';
$L['page_edittitle'] = 'Page properties';
$L['page_editsubtitle'] = 'Edit all required fields and hit "Submit" to continue';

$L['page_aliascharacters'] = 'Characters \'+\', \'/\', \'?\', \'%\', \'#\', \'&\' are not allowed in aliases';
$L['page_catmissing'] = 'The category code is missing';
$L['page_clone'] = 'Clone page';
$L['page_confirm_delete'] = 'Do you really want to delete this page?';
$L['page_confirm_validate'] = 'Do you want to validate this page?';
$L['page_confirm_unvalidate'] = 'Do you really want to put this page back to the validation queue?';
$L['page_date_now'] = 'Update page date to current';
$L['page_drafts'] = 'Drafts';
$L['page_drafts_desc'] = 'Pages saved in your drafts';
$L['page_notavailable'] = 'This page will be published in ';
$L['page_textmissing'] = 'Page text must not be empty';
$L['page_titletooshort'] = 'The title is too short or missing';
$L['page_validation'] = 'Awaiting validation';
$L['page_validation_desc'] = 'Your pages which have not been validated by administrator yet';

$L['page_file'] = 'File download';
$L['page_filehint'] = '(Set &quot;Yes&quot; to enable the download module at bottom of the page, and fill up the two fields below)';
$L['page_urlhint'] = '(If File download enabled)';
$L['page_filesize'] = 'Filesize, kB';
$L['page_filesizehint'] = '(If File download enabled)';
$L['page_filehitcount'] = 'File hit count';
$L['page_filehitcounthint'] = '(If File download enabled)';
$L['page_metakeywords'] = 'Meta keywords';
$L['page_metatitle'] = 'Meta title';
$L['page_metadesc'] = 'Meta description';

$L['page_formhint'] = 'Once your submission is done, the page will be placed in the validation queue and will be hidden, awaiting confirmation from a site administrator or global moderator before being displayed in the right section. Check all fields carefully. If you need to change something, you will be able to do that later. But submitting changes puts a page into validation queue again.';

$L['page_pageid'] = 'Page ID';
$L['page_deletepage'] = 'Delete this page';

$L['page_savedasdraft'] = 'Page saved as draft.';

/**
 * Page statuses
 */

$L['page_status_draft'] = 'Draft';
$L['page_status_pending'] = 'Pending';
$L['page_status_approved'] = 'Approved';
$L['page_status_published'] = 'Published';
$L['page_status_expired'] = 'Expired';

/**
 * Moved from theme.lang
 */

$L['page_linesperpage'] = 'Lines per page';
$L['page_linesinthissection'] = 'Lines in this section';

$Ls['pages'] = "pages,page";
$Ls['unvalidated_pages'] = "unvalidated pages,unvalidated page";
$Ls['pages_in_drafts'] = "pages in drafts,page in drafts";
