<?php

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2013 
 * @package    extendedSEO
 * @license    GNU/LGPL
 * @filesource
 */

$this->loadLanguageFile('tl_article');
$this->loadLanguageFile('tl_settings');

/**
 * Palettes
 */
foreach ($GLOBALS['TL_DCA']['tl_page']['palettes'] as $keyPalette => $valuePalette)
{
	// Skip if we have a array or the palettes for subselections
	if (is_array($valuePalette) || $keyPalette == "__selector__")
	{
		continue;
	}

	// Explode entries
	$arrEntries = explode(";", $valuePalette);

	// Search for "{meta_legend}" and insert the fields
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

foreach ($GLOBALS['TL_DCA']['tl_page']['palettes'] as $key => $row)
{
	if ($key == '__selector__' || $key == 'root') continue;
	if (!stristr($row, 'pageTitle')) continue;
	$GLOBALS['TL_DCA']['tl_page']['palettes'][$key] = str_replace('pageTitle', 'pageTitle,rootTitle', $GLOBALS['TL_DCA']['tl_page']['palettes'][$key]);
}

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['keywords'] = array(
	'label'          => &$GLOBALS['TL_LANG']['tl_article']['keywords'],
	'exclude'        => true,
	'inputType'      => 'textarea',
	'eval'           => array('style' => 'height:60px;')
);

$GLOBALS['TL_DCA']['tl_page']['fields']['rootTitle'] = array(
	'label'          => &$GLOBALS['TL_LANG']['tl_settings']['websiteTitle'],
	'exclude'        => true,
	'inputType'      => 'text',
	'eval'           => array('tl_class' => 'long clr')
);