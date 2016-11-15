<?php
/* ====================
[BEGIN_COT_EXT]
Code=contact
Name=Contact
Category=forms-feedback
Description=Contact form for user feedback delivered by e-mail and recorded in database
Version=2.7.1
Date=2016-11-15
Author=Cotonti Team
Copyright=&copy; Cotonti Team 2008-2016
Notes=
Auth_guests=RW
Lock_guests=12345A
Auth_members=RW
Lock_members=12345A
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
email=01:string:::E-mail
minchars=12:string::5:Min post length, chars
map=12:text:::Map
about=13:text:::About
save=14:select:email,db,both:both:Save Method
template=15:textarea:::Email template
[END_COT_EXT_CONFIG]
==================== */

/**
 * Contact Plugin for Cotonti CMF
 *
 * @package Contact
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
