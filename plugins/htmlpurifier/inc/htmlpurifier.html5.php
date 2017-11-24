<?php
/**
 * HTML Purifier config to support HTML5
 * 
 * @package HTML Purifier
 * 
 * Based on code by Mateusz Turcza 
 * Copyright (c) 2015 Xemlock 
 * Licensed under the MIT License (MIT)
 * @link https://github.com/xemlock/htmlpurifier-html5
 * 
 * For HTML Purifier customization docs
 * @see http://htmlpurifier.org/docs/enduser-customize.html
 */

$config->set('HTML.Doctype', 'HTML 4.01 Transitional');
$config->set('CSS.AllowTricky', true);

// Set some HTML5 properties
$config->set('HTML.DefinitionID', 'html5-definitions'); // unqiue id
$config->set('HTML.DefinitionRev', 1);
//$config->set('Cache.DefinitionImpl', null); // debug mode for development
$config->set('Attr.ID.HTML5', true);

if ($def = $config->maybeGetRawHTMLDefinition()) {
	// http://developers.whatwg.org/sections.html
	$def->addElement('section', 'Block', 'Flow', 'Common');
	$def->addElement('nav',     'Block', 'Flow', 'Common');
	$def->addElement('aside',   'Block', 'Flow', 'Common');
	// commented as it related to global page (theme defined)   
	// $def->addElement('article', 'Block', 'Flow', 'Common');
	// $def->addElement('header', 'Block', 'Flow', 'Common');
	// $def->addElement('footer', 'Block', 'Flow', 'Common');
	// $def->addElement('main', 'Block', 'Flow', 'Common');
	
	// Content model actually excludes several tags, not modelled here
	$def->addElement('address', 'Block', 'Flow', 'Common');
	$def->addElement('hgroup', 'Block', 'Required: h1 | h2 | h3 | h4 | h5 | h6', 'Common');
	// http://developers.whatwg.org/grouping-content.html
	$def->addElement('figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common');
	$def->addElement('figcaption', 'Inline', 'Flow', 'Common');
	// http://developers.whatwg.org/the-video-element.html#the-video-element
	$def->addElement('video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', array(
		'src' => 'URI',
		'type' => 'Text',
		'width' => 'Length',
		'height' => 'Length',
		'poster' => 'URI',
		'preload' => 'Enum#auto,metadata,none',
		'controls' => 'Bool',
	));
	// http://developers.whatwg.org/the-video-element.html#the-audio-element
	$def->addElement('audio', 'Block', 'Flow', 'Common', array(
		'controls' => 'Bool',
		'preload'  => 'Enum#auto,metadata,none',
		'src'      => 'URI',
	));
	$def->addElement('source', 'Block', 'Flow', 'Common', array(
		'src' => 'URI',
		'type' => 'Text',
	));
	// http://developers.whatwg.org/text-level-semantics.html
	$def->addElement('s',    'Inline', 'Inline', 'Common');
	$def->addElement('var',  'Inline', 'Inline', 'Common');
	$def->addElement('sub',  'Inline', 'Inline', 'Common');
	$def->addElement('sup',  'Inline', 'Inline', 'Common');
	$def->addElement('mark', 'Inline', 'Inline', 'Common');
	$def->addElement('wbr',  'Inline', 'Empty', 'Core');
	// http://developers.whatwg.org/edits.html
	$def->addElement('ins', 'Block', 'Flow', 'Common', array('cite' => 'URI', 'datetime' => 'CDATA'));
	$def->addElement('del', 'Block', 'Flow', 'Common', array('cite' => 'URI', 'datetime' => 'CDATA'));

	// TIME
	$time = $def->addElement('time', 'Inline', 'Inline', 'Common', array('datetime' => 'Text', 'pubdate' => 'Bool'));
	$time->excludes = array('time' => true);
	
	// Others
	$def->addAttribute('iframe', 'allowfullscreen', 'Bool');
	$def->addAttribute('table', 'height', 'Text');
	$def->addAttribute('td', 'border', 'Text');
	$def->addAttribute('th', 'border', 'Text');
	$def->addAttribute('tr', 'width', 'Text');
	$def->addAttribute('tr', 'height', 'Text');
	$def->addAttribute('tr', 'border', 'Text');

	// IMG
	$def->addAttribute('img', 'srcset', 'Text');
	
	// TinyMCE
	// $def->addAttribute('img', 'data-mce-src', 'Text');
	// $def->addAttribute('img', 'data-mce-json', 'Text');
}
