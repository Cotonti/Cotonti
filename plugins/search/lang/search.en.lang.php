<?PHP
/**
 * English Language File for Search Plugin
 *
 * @package Cotonti
 * @version 0.0.6
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL.');

// Plugin settings
$L['cfg_maxwords']= array('Max. words in search query');
$L['cfg_maxsigns']= array('Max. latters in search query');
$L['cfg_maxitems']= array('Max. entries in normal search results');
$L['cfg_maxitems_ext']= array('Max. entries in advanced search results');
$L['cfg_minsigns'] = array('Min. signs in query');
$L['cfg_searchurl'] = array('Type of forum post link to use, Single uses a Single post view, while Normal uses the traditional thread/jump-to link');
$L['cfg_showtext']= array('Display text entries in normal search results');
$L['cfg_showtext_ext']= array('Display text entries in advanced search results');
$L['cfg_addfields']= array('Additional pages fields for search, separated by commas. Example "page_extra1,page_extra2,page_key"');

// Common - title, info, query
$L['plu_title_all'] = 'Site search';
$L['plu_subtitle_all'] = "You can make the search more exact by selecting necessary categories and search options only. Please note that &quot;Search All&quot; does not give you all the options. Choose &quot;Forums&quot; or &quot;Pages&quot; for more options.";
$L['plu_search_req'] = 'Query';
$L['plu_search_key'] = 'Find';
$L['plu_search_example'] = 'E.g. cotonti 6 genoa';

// Title extras
$L['plu_title_frmtab'] = 'Forums';
$L['plu_title_pagtab'] = 'Pages';
$L['plu_title_usetab'] = 'Users';

// Mode selectors and result titles
$L['plu_tabs_all'] = 'All';
$L['plu_tabs_frm'] = 'Forums';
$L['plu_tabs_pag'] = 'Pages';

// Parameters - common
$L['plu_ctrl_list'] = 'Hold CTRL to select multiple sections';
$L['plu_allsections'] = 'All sections';
$L['plu_allcategories'] = 'All categories';
$L['plu_res_sort'] = 'Order results by';
$L['plu_sort_desc'] = 'Descending';
$L['plu_sort_asc'] = 'Ascending';
$L['plu_other_opt'] = 'Optional parameters';
$L['plu_other_date'] = 'Including date';

// Parameters - dates
$L['plu_any_date'] = 'Any date';
$L['plu_last_2_weeks'] = 'Last 2 weeks';
$L['plu_last_1_month'] = 'Last month';
$L['plu_last_3_month'] = 'Last 3 months';
$L['plu_last_1_year'] = 'Last year';
$L['plu_need_datas'] = 'Custom range';
$L['plu_need_dd'] = 'dd';
$L['plu_need_mm'] = 'mm';
$L['plu_need_yy'] = 'yyyy';

// Parameters - forums
$L['plu_frm_set_sec'] = 'Select forum sections';
$L['plu_frm_res_sort1'] = 'Topic updated';
$L['plu_frm_res_sort2'] = 'Topic started';
$L['plu_frm_res_sort3'] = 'Topic title';
$L['plu_frm_res_sort4'] = 'Replies count';
$L['plu_frm_res_sort5'] = 'Views count';
$L['plu_frm_search_names'] = 'Search in topic titles';
$L['plu_frm_search_post'] = 'Search in posts';
$L['plu_frm_search_answ'] = 'Show topics with replies only';

// Parameters - pages
$L['plu_pag_set_sec'] = 'Select page categories';
$L['plu_pag_res_sort1'] = 'Date published';
$L['plu_pag_res_sort2'] = 'Title';
$L['plu_pag_res_sort3'] = 'Popularity';
$L['plu_pag_search_names'] = 'Search in page titles';
$L['plu_pag_search_desc'] = 'Search in page descriptions';
$L['plu_pag_search_text'] = 'Search in page text';
$L['plu_pag_search_file'] = 'Show pages with files only';

// Error messages
$L['plu_querytooshort'] = 'The query string is too short';
$L['plu_toomanywords'] = 'Too many words, limit is set to';
$L['plu_notseltopmes'] = 'You have not selected forum sections in search options';
$L['plu_notseloption'] = 'You have not selected page categories in search options';
$L['plu_noneresult'] = 'Nothing was found. Please try to simplify your query';

// Results
$L['plu_result'] = 'Search results';
$L['plu_found'] = 'Found';
$L['plu_moreres'] = 'more';
$L['plu_match'] = 'matches';
$L['plu_section'] = 'Section';
$L['plu_last_date'] = 'Date updated';
?>