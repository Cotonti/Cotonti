<?php
/**
 * English Language File for the Page Module (page.en.lang.php)
 *
 * @package page
 * @version 0.7.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Admin Page Section
 */

$L['addnewentry'] = 'Add a new entry';
$L['adm_queue_deleted'] = 'Page was deleted in to trash can';
$L['adm_valqueue'] = 'Waiting for validation';
$L['adm_validated'] = 'Already validated';
$L['adm_structure'] = 'Structure of the pages (categories)';
$L['adm_extrafields_desc'] = 'Add/Edit extra fields';
$L['adm_sort'] = 'Sort';
$L['adm_sortingorder'] = 'Set a default sorting order for the categories';
$L['adm_showall'] = 'Show all';
$L['adm_help_page'] = 'The pages that belong to the category &quot;system&quot; are not displayed in the public listings, it\'s to make standalone pages.';
$L['adm_fileyesno'] = 'File (yes/no)';
$L['adm_fileurl'] = 'File URL';
$L['adm_filecount'] = 'File hit count';
$L['adm_filesize'] = 'File size';

/**
 * Page Section
 * Extrafields Subsection
 */

$L['adm_help_pages_extrafield'] = '<b>Base HTML</b> set automaticaly if you leave it blank<br /><br />
<b>New tags in tpl files:</b><br /><br />
page.tpl: {PAGE_XXXXX}, {PAGE_XXXXX_TITLE}<br /><br />
page.add.tpl: {PAGEADD_FORM_XXXXX}, {PAGEADD_FORM_XXXXX_TITLE}<br /><br />
page.edit.tpl: {PAGEEDIT_FORM_XXXXX}, {PAGEEDIT_FORM_XXXXX_TITLE}<br /><br />
list.tpl: {LIST_ROW_XXXXX}, {LIST_TOP_XXXXX}<br />';

/**
 * Config Section
 * Page Subsection
 */

$L['cfg_allowphp_pages'] = array('Allow the PHP page type', 'Execution of PHP code in pages, use with caution!');
$L['cfg_autovalidate'] = array('Autovalidate page', 'Autovalidate page if poster have admin rights for page category');	// New in 0.0.2
$L['cfg_count_admin'] = array('Count Administrators\' hits', '');	// New in 0.0.1
$L['cfg_maxrowsperpage'] = array('Max. lines in lists', ' ');
$L['cfg_maxlistsperpage'] = array('Max. lists per page', ' '); // New in 0.0.6

/**
 * page.add.tpl
 */

$L['pagadd_subtitle'] = 'Submit a new page';
$L['pagadd_title'] = 'New page submission form';

/**
 * page.edit.tpl
 */

$L['paged_subtitle'] = 'Update values for this page';
$L['paged_title'] = 'Page properties';

/**
 * page.tpl
 */

$L['pag_authortooshort'] = 'The author name is too short or missing';
$L['pag_catmissing'] = 'The category code is missing';
$L['pag_desctooshort'] = 'The description is too short or missing';
$L['pag_notavailable'] = 'This page will be published in '; // New in N-0.0.2
$L['pag_titletooshort'] = 'The title is too short or missing';
$L['pag_validation'] = 'Awaiting validation';
$L['pag_validation_desc'] = 'Your pages which have not been validated by administrator yet';

?>