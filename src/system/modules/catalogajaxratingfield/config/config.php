<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 *
 * The TYPOlight webCMS is an accessible web content management system that 
 * specializes in accessibility and generates W3C-compliant HTML code. It 
 * provides a wide range of functionality to develop professional websites 
 * including a built-in search engine, form generator, file and user manager, 
 * CSS engine, multi-language support and many more. For more information and 
 * additional TYPOlight applications like the TYPOlight MVC Framework please 
 * visit the project website http://www.typolight.org.
 *
 * This is the catalog catalogajaxratingfield extension configuration file.
 *
 * PHP version 5
 * @copyright  Christian Schiffler 2009
 * @author     Christian Schiffler  <c.schiffler@cyberspectrum.de> 
 * @package    CatalogAjaxRatingField
 * @license    GPL 
 * @filesource
 */


/**
 * Back-end module
 */
 
// Register field type editor to catalog module.
$GLOBALS['BE_MOD']['content']['catalog']['fieldTypes']['ajaxratingfield'] = array
(
	'typeimage'    => 'system/modules/catalogajaxratingfield/html/ajaxrating.gif',
	'fieldDef'     => array
	(
		// hopefully never ever someone will add a widget with that name. 
		// I simply needed an invisible one here :)
		'inputType' => 'none',
		'eval'      => array
		(
			'doNotSaveEmpty'=>true,
		),
	),
	'sqlDefColumn' => "double NOT NULL default 0",
	'parseValue' => array(array('CatalogAjaxRatingField', 'parseValue')),
);

$GLOBALS['BE_MOD']['content']['catalog']['typesCatalogFields'][] = 'ajaxratingfield';

?>