<?php

/**
 * Administration
 */

// Status indicators
$R['admin_code_missing'] = '<span class="strong extension missing">'.$L['adm_missing'].'</span>';
$R['admin_code_notinstalled'] = '<span class="strong extension notinstalled">'.$L['adm_notinstalled'].'</span>';
$R['admin_code_partrunning'] = '<span class="strong extention partrunning">'.$L['adm_partrunning'].'</span>';
$R['admin_code_paused'] = '<span class="strong extension paused">'.$L['adm_paused'].'</span>';
$R['admin_code_present'] = '<span class="strong extension present">'.$L['adm_present'].'</span>';
$R['admin_code_running'] = '<span class="strong extension running">'.$L['adm_running'].'</span>';

// Icons

$R['admin_icon_allow'] = '<img class="icon" src="system/admin/img/allow.png" alt="" />'; // need to change for checkbox?
$R['admin_icon_allow_locked'] = '<img class="icon" src="system/admin/img/allow_locked.png" alt="" />'; // need to change for checkbox?
$R['admin_icon_deny'] = '<img class="icon" src="system/admin/img/deny.png" alt="" />'; // need to change for checkbox?
$R['admin_icon_deny_locked'] = '<img class="icon" src="system/admin/img/deny_locked.png" alt="" />'; // need to change for checkbox?

$R['admin_icon_auth_1'] = '<img class="icon" src="system/admin/img/auth_1.png" alt="" />';
$R['admin_icon_auth_2'] = '<img class="icon" src="system/admin/img/auth_2.png" alt="" />';
$R['admin_icon_auth_3'] = '<img class="icon" src="system/admin/img/auth_3.png" alt="" />';
$R['admin_icon_auth_4'] = '<img class="icon" src="system/admin/img/auth_4.png" alt="" />';
$R['admin_icon_auth_5'] = '<img class="icon" src="system/admin/img/auth_5.png" alt="" />';
$R['admin_icon_auth_a'] = '<img class="icon" src="system/admin/img/auth_a.png" alt="" />';
$R['admin_icon_auth_r'] = '<img class="icon" src="system/admin/img/auth_r.png" alt="" />';
$R['admin_icon_auth_w'] = '<img class="icon" src="system/admin/img/auth_w.png" alt="" />';

$R['admin_icon_comments'] = '<img class="icon" src="system/admin/img/comments.png" alt="" />'; // move to trashbin plugin?
$R['admin_icon_forums'] = '<img class="icon" src="system/admin/img/forums.png" alt="" />'; // move to trashbin plugin?
$R['admin_icon_forums_posts'] = '<img class="icon" src="system/admin/img/forums.png" alt="" />'; // move to trashbin plugin?
$R['admin_icon_forums_topics'] = '<img class="icon" src="system/admin/img/forums.png" alt="" />'; // move to trashbin plugin?
$R['admin_icon_page'] = '<img class="icon" src="system/admin/img/page.png" alt="" />'; // move to trashbin plugin?
$R['admin_icon_tools'] = '<img class="icon" src="system/admin/img/tool.png" alt="" />'; // move to trashbin plugin?
$R['admin_icon_user'] = '<img class="icon" src="system/admin/img/user.png" alt="" />'; // move to trashbin plugin? (+ check admin.users.tpl)

$R['admin_icon_delete'] = '<img class="icon" src="system/admin/img/trashbin.png" alt="" />'; // 1 case

$R['admin_icon_discheck0'] = '<img class="icon" src="system/admin/img/discheck0.png" alt="" />';
$R['admin_icon_discheck1'] = '<img class="icon" src="system/admin/img/discheck1.png" alt="" />';

$R['admin_icon_join1'] = '<img class="icon" src="system/admin/img/join1.png" alt="" />';
$R['admin_icon_join2'] = '<img class="icon" src="system/admin/img/join2.png" alt="" />';

$R['admin_icon_blank'] = '<img class="icon" src="system/admin/img/blank.png" alt="" />';

// Usergroups
$R['admin_icon_usergroup0'] = '<img class="icon" src="system/admin/img/users-off.png" title="'.$L['Group0'].'" alt="'.$L['Group0'].'" />';
$R['admin_icon_usergroup1'] = '<img class="icon" src="system/admin/img/users.png" title="'.$L['Group1'].'" alt="'.$L['Group1'].'" />';

//Extrafields
$R['admin_exflds_array'] = '{$tplfile}: {$tags}; ';

// Breadcrumbs
$R['breadcrumbs_container'] = '{$crumbs}';
$R['breadcrumbs_separator'] = ' / ';
$R['breadcrumbs_link'] = '<a href="{$url}" title="{$title}">{$title}</a>';
$R['breadcrumbs_plain'] = '{$title}';
$R['breadcrumbs_crumb'] = '{$crumb}';
$R['breadcrumbs_first'] = '{$crumb}';
$R['breadcrumbs_last'] = '{$crumb}';