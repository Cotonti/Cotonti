<?php
/* ====================
 * [BEGIN_COT_EXT]
 * Code=sitemap
 * Name=SiteMap
 * Category=performance-seo
 * Description=Simple XML sitemap for the site
 * Version=1.0
 * Date=2012-05-29
 * Author=Cotonti Team
 * Copyright=Copyright (c) Cotonti Team 2012
 * Notes=BSD License
 * Auth_guests=R
 * Lock_guests=W12345A
 * Auth_members=R
 * Lock_members=W12345A
 * Recommends_modules=page,forums,users
 * [END_COT_EXT]

 * [BEGIN_COT_EXT_CONFIG]
 * cache_ttl=01:string::3600:Cache TTL in seconds
 * freq=02:select:default,always,hourly,daily,weekly,monthly,yearly,never:default:Default change frequency
 * prio=03:select:0.0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1.0:0.5:Default priority
 * perpage=04:string::50000:Max items per sitemap page
 * index_freq=07:select:default,always,hourly,daily,weekly,monthly,yearly,never:default:Index change frequency
 * index_prio=08:select:0.0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1.0:0.5:Index priority
 * page=10:radio::1:Include pages
 * page_freq=12:select:default,always,hourly,daily,weekly,monthly,yearly,never:default:Pages change frequency
 * page_prio=13:select:0.0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1.0:0.5:Pages priority
 * forums=20:radio::1:Include forums
 * forums_freq=22:select:default,always,hourly,daily,weekly,monthly,yearly,never:default:Forums change frequency
 * forums_prio=23:select:0.0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1.0:0.5:Forums priority
 * users=30:radio::0:Include users
 * users_freq=32:select:default,always,hourly,daily,weekly,monthly,yearly,never:default:Users change frequency
 * users_prio=33:select:0.0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1.0:0.5:Users priority
 * [END_COT_EXT_CONFIG]
==================== */

defined('COT_CODE') or die('Wrong URL');

?>