<?php
/* ====================
[BEGIN_COT_EXT]
Code=contact
Name=Contact
Category=forms-feedback
Description=Contact form for user feedback delivered by e-mail and recorded in database
Version=2.7.0
Date=2011-05-24
Author=Cotonti Team
Copyright=&copy; 2008-2012 Cotonti Team
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
 * @package contact
 * @version 2.1
 * @author Cotonti Team
 * @copyright (c) 2008-2012 Cotonti Team
 * @license BSD
 */

?>