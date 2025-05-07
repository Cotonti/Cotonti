<?php
/**
 * Custom Functions for Cotonti
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * Generates a unique cache path based on the URL to fix the conflict between URLeditor and page cache
 * 
 * @param array $parsedUrl Parsed URL components
 * @return string Unique cache path
 */
function cot_staticCacheGetPathByUri($parsedUrl)
{
    $get = [];
    if (!empty($parsedUrl['query'])) {
        $parsedUrl['query'] = str_replace('&amp;', '&', $parsedUrl['query']);
        parse_str($parsedUrl['query'], $get);
    }
    
    // Start with base path
    $path = '';
    
    // Add the host to make cache keys unique per domain/subdomain
    if (!empty($parsedUrl['host'])) {
        $path = preg_replace('#\W#', '_', $parsedUrl['host']) . '_';
    }
    
    // Add module (e parameter)
    if (!empty($get['e'])) {
        $path .= preg_replace('#\W#', '', $get['e']);
    } elseif ($parsedUrl['path'] !== '/') {
        // If no e parameter, use the URL path
        $parsedUrl['path'] = rawurldecode($parsedUrl['path']);
        $path .= trim(preg_replace('#\W#', '_', trim($parsedUrl['path'], '/')), '_');
    }
    
    // Add category (c parameter) if exists
    if (!empty($path)) {
        $c = isset($get['c']) ? trim($get['c']) : null;
        if (!empty($c)) {
            $path .= '/' . $c;
        }
    }
    
    // If nothing found, use 'index'
    if (empty($path)) {
        $path = 'index';
    }
    
    return $path;
} 
