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
 * This is the enhancement to the data container array for table tl_catalog_fields 
 * to allow the custom field type for multiTextWizard.
 *
 * PHP version 5
 * @copyright  Christian Schiffler 2009
 * @author     Christian Schiffler  <c.schiffler@cyberspectrum.de> 
 * @package    CatalogAjaxRatingField
 * @license    GPL 
 * @filesource
 */


/**
 * Table tl_catalog_fields 
 */

// Palettes
$GLOBALS['TL_DCA']['tl_catalog_fields']['palettes']['ajaxratingfield'] = 'name,description,colName,type,ajaxratingfield';

// register our fieldtype editor to the catalog Fields
$GLOBALS['TL_DCA']['tl_catalog_fields']['fields']['ajaxratingfield'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_catalog_fields']['ajaxRatingUnitWidth'],
	'inputType'               => 'text',
	'eval'                    => array('multiple'=>false, 'size'=>2, 'rgxp'=>'digit', 'nospace'=>true),
);

// register to catalog module that we provide the ajaxratingfield as field type.
$GLOBALS['TL_DCA']['tl_catalog_fields']['fields']['type']['options'][] = 'ajaxratingfield';

?>