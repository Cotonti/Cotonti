<?php
/* ====================
[BEGIN_COT_EXT]
Code=sitemap
Name=SiteMap
Category=seo
Description=Simple XML sitemap for the site
Version=1.0.2
Date=2024-11-20
Author=Cotonti Team
Copyright=Copyright (c) Cotonti Team 2008-2024
Notes=BSD License
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
Recommends_modules=page,forums,users
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
cache_ttl=01:string::3600:Cache TTL in seconds
freq=04:select:default,always,hourly,daily,weekly,monthly,yearly,never:default:Default change frequency
prio=07:select:0.0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1.0:0.5:Default priority
perpage=10:string::50000:Max items per sitemap page
indexSep=20:separator:::Homepage
index_freq=23:select:default,always,hourly,daily,weekly,monthly,yearly,never:default:Index change frequency
index_prio=26:select:0.0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1.0:0.5:Index priority
pageSep=30:separator:::Pages
page=33:radio::1:Include pages
pageCategoryPagination=36:radio::1:Include category pagination
page_freq=39:select:default,always,hourly,daily,weekly,monthly,yearly,never:default:Pages change frequency
page_prio=42:select:0.0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1.0:0.5:Pages priority
forumsSep=50:separator:::Forums
forums=53:radio::1:Include forums
forums_freq=56:select:default,always,hourly,daily,weekly,monthly,yearly,never:default:Forums change frequency
forums_prio=59:select:0.0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1.0:0.5:Forums priority
usersSep=70:separator:::Users
users=73:radio::0:Include users
users_freq=76:select:default,always,hourly,daily,weekly,monthly,yearly,never:default:Users change frequency
users_prio=79:select:0.0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1.0:0.5:Users priority
[END_COT_EXT_CONFIG]
==================== */

defined('COT_CODE') or die('Wrong URL');
