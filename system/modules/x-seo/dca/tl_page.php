<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  MEN AT WORK 2011
 * @package    extendedSEO
 * @license    GNU/LGPL
 * @filesource
 */
 
$this->loadLanguageFile('tl_article');

/**
 * Palettes
 */
$arrPalettes = explode(";", $GLOBALS['TL_DCA']['tl_page']['palettes']['root']);

foreach ($arrPalettes as $key => $value)
{
    if (stripos($value, "{meta_legend}") !== false)
    {
        $arrPalettes[$key] = str_replace("pageTitle", "pageTitle,description,keywords", $value);
        break;
    }
}

$GLOBALS['TL_DCA']['tl_page']['palettes']['root'] = implode(";", $arrPalettes);

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['keywords'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_article']['keywords'],
    'exclude' => true,
    'inputType' => 'textarea',
    'eval' => array('style' => 'height:60px;')
);

?>