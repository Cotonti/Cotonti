<?php
/**
 * Installer resources
 *
 * @package Install
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */


$R['install_code_available']      = '<span class="fw-semibold text-success">' . $L['Available'] . '</span>';
$R['install_code_not_available']  = '<span class="text-damger">' . $L['na'] . '</span>';

$R['install_code_writable'] = '<span class="fw-semibold text-success">' . $L['install_writable'] . '</span>';

$R['install_code_valid'] = '<span class="fw-semibold text-success">{$text}</span>';
$R['install_code_invalid'] = '<span class="fw-semibold text-danger">{$text}</span>';

$R['install_code_found'] = '<span class="fw-semibold text-success">' . $L['Found'] . '</span>';
$R['install_code_not_found'] = '<span class="fw-semibold text-danger">' . $L['nf'] . '</span>';

$R['install_code_recommends'] = '<div class="alert alert-success small lh-sm py-2 mt-1 mb-0">';
$R['install_code_recommends'] .= '<span class="fw-semibold d-block">' . $L['install_recommends'] . '</span>';
$R['install_code_recommends'] .= '<span class="ms-3 d-block">' . $L['Modules'] . ': {$modules_list}' . '</span>' . '<span class="ms-3 d-block">' . $L['Plugins'] . ': {$plugins_list}</span></div>';

$R['install_code_requires'] = '<div class="alert alert-danger small lh-sm py-2 mt-1 mb-0">';
$R['install_code_requires'] .= '<span class="fw-semibold d-block">' . $L['install_requires'] . '</span>';
$R['install_code_requires'] .= '<span class="ms-3 d-block">' . $L['Modules'] . ': {$modules_list}' . '</span>' . '<span class="ms-3 d-block">' . $L['Plugins'] . ': {$plugins_list}</span></div>';

$R['code_msg_begin'] = '<div class="alert alert-danger small mt-2 mb-0 py-2 opacity-75"><ul class="{$class} list-unstyled">';
$R['code_msg_end'] = '</ul></div>';
$R['code_msg_line'] = '<li><span class="{$class}">{$text}</span></li>';
$R['code_msg_inline'] = '<span class="{$class}">{$text}</span>';

$R['install_checkbox'] = '<input type="hidden" name="{$name}" value="{$value_off}" />';
$R['install_checkbox'] .= '<div class="form-check form-switch">';
$R['install_checkbox'] .= '<input type="checkbox" name="{$name}" value="{$value}"{$checked}{$attrs} /> {$title}';
$R['install_checkbox'] .= '<label class="form-check-label" for="{$name}"></label>';
$R['install_checkbox'] .= '</div>';
