<?php

if (!defined('TL_ROOT'))
    die('You cannot access this file directly!');

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
// Foreach pallet in tl_page
foreach ($GLOBALS['TL_DCA']['tl_page']['palettes'] as $keyPalette => $valuePalette)
{
    // Skip if we have a array or the palttes for subselections
    if (is_array($valuePalette) || $keyPalette == "__selector__")
        continue;

    // Explode entries
    $arrEntries = explode(";", $valuePalette);

    // Search for "{meta_legend}" and insert ne fields
    foreach ($arrEntries as $keyEntry => $valueEntry)
    {
        if (stripos($valueEntry, "{meta_legend}") !== false)
        {
            $arrEntry = trimsplit(",", $valueEntry);

            if (($mixSearch = array_search("description", $arrEntry)) !== FALSE)
            {
                unset($arrEntry[$mixSearch]);
            }

            if (($mixSearch = array_search("description", $arrEntry)) !== FALSE)
            {
                unset($arrEntry[$mixSearch]);
            }

            $arrEntry = array_merge(array_slice($arrEntry, 0, 2), array("description", "keywords"), array_slice($arrEntry, 2, count($arrEntry) - 2));

            $valueEntry = implode(",", $arrEntry);
            $arrEntries[$keyEntry] = $valueEntry;
        }
    }

    // Write new entry back in the palette
    $GLOBALS['TL_DCA']['tl_page']['palettes'][$keyPalette] = implode(";", $arrEntries);
}

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