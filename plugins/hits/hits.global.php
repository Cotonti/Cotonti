<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
Order=12
[END_COT_EXT]
==================== */

/**
 * Hits counter
 *
 * @package Hits
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if (!defined('COT_ADMIN')) {
    require_once cot_incfile('hits', 'plug');

    // Total number of site visits
    if (Cot::$cfg['plugin']['hits']['adminhits'] || Cot::$usr['maingrp'] != COT_GROUP_SUPERADMINS) {
        if (Cot::$cache && Cot::$cache->mem) {
            $hits = Cot::$cache->mem->get('hits', 'system');
            if (empty($hits) || !is_array($hits)) {
                $hits = ['date' => Cot::$sys['day'], 'count' => 0,];
            }

            if (empty(Cot::$cfg['plugin']['hits']['hit_precision'])) {
                Cot::$cfg['plugin']['hits']['hit_precision'] = 100;
            }

            if ($hits['date'] < Cot::$sys['day']) {
                cot_stat_inc('totalpages', $hits['count']);
                cot_stat_update($hits['date'], $hits['count']);
                Cot::$cache->mem->remove('hits', 'system');
                $hits = ['date' => Cot::$sys['day'], 'count' => 0,];
            }

            $hits['count']++;

            if ($hits['count'] >= Cot::$cfg['plugin']['hits']['hit_precision']) {
                cot_stat_inc('totalpages', $hits['count']);
                cot_stat_update($hits['date'], $hits['count']);
                Cot::$cache->mem->remove('hits', 'system');
                $hits = [
                    'date' => Cot::$sys['day'],
                    'count' => 0, // Use 0 because we are incrementing value in DB for this number
                ];
            }

            Cot::$cache->mem->store('hits', $hits, 'system');
        } else {
            cot_stat_inc('totalpages');
            cot_stat_update(Cot::$sys['day']);
        }
    }

    // Maximum number of users online
    if (cot_plugin_active('whosonline')) {
        if (Cot::$cache && Cot::$cache->mem && Cot::$cache->mem->exists('maxusers', 'system')) {
            $maxusers = Cot::$cache->mem->get('maxusers', 'system');
        } else {
            $maxusers = (int) Cot::$db->query(
                'SELECT stat_value FROM ' . Cot::$db->stats . " WHERE stat_name='maxusers' LIMIT 1"
            )->fetchColumn();
            if (Cot::$cache && Cot::$cache->mem) {
                Cot::$cache->mem->store('maxusers', $maxusers, 'system', 0);
            }
        }

        if (
            !empty(Cot::$sys['whosonline_all_count'])
            && $maxusers < Cot::$sys['whosonline_all_count']
        ) {
            cot_stat_set('maxusers', Cot::$sys['whosonline_all_count']);
            if (Cot::$cache && Cot::$cache->mem) {
                Cot::$cache->mem->store('maxusers', Cot::$sys['whosonline_all_count'], 'system', 0);
            }
        }
    }
}
