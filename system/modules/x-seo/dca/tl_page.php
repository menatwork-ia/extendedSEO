<?php

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2013
 * @package    extendedSEO
 * @license    GNU/LGPL
 * @filesource
 */

/**
 * PLoad some language files.
 */
try
{
	\Controller::loadLanguageFile('tl_article');
	\Controller::loadLanguageFile('tl_settings');
}
catch (Exception $e)
{
	// Die silently.
}

/**
 * Palettes
 */

// Add the description and keywords.
foreach ($GLOBALS['TL_DCA']['tl_page']['palettes'] as $keyPalette => $valuePalette)
{
	// Skip if we have a array or the palettes for subselections
	if (is_array($valuePalette) || $keyPalette == '__selector__')
	{
		continue;
	}

	// Explode entries
	$arrEntries = explode(";", $valuePalette);

	// Search for "{meta_legend}" and insert the fields
	foreach ($arrEntries as $keyEntry => $valueEntry)
	{
		if (stripos($valueEntry, '{meta_legend}') !== false)
		{
			$arrEntry = trimsplit(',', $valueEntry);

			// First remove the current fields.
			if (($mixSearch = array_search('description', $arrEntry)) !== false)
			{
				unset($arrEntry[ $mixSearch ]);
			}

			if (($mixSearch = array_search('keywords', $arrEntry)) !== false)
			{
				unset($arrEntry[ $mixSearch ]);
			}

			// Than add a fields.
			$arrEntry = array_merge(array_slice($arrEntry, 0, 2), array('description', 'keywords'), array_slice($arrEntry, 2, count($arrEntry) - 2));

			$valueEntry              = implode(",", $arrEntry);
			$arrEntries[ $keyEntry ] = $valueEntry;
		}
	}

	// Write new entry back in the palette
	$GLOBALS['TL_DCA']['tl_page']['palettes'][ $keyPalette ] = implode(";", $arrEntries);
}

// Add the xseo_rootTitle field.
foreach ($GLOBALS['TL_DCA']['tl_page']['palettes'] as $key => $row)
{
	if ($key == '__selector__' || $key == 'root')
	{
		continue;
	}
	elseif (!stristr($row, 'pageTitle'))
	{
		continue;
	}
	else
	{
		$GLOBALS['TL_DCA']['tl_page']['palettes'][ $key ] = str_replace('pageTitle', 'pageTitle,xseo_rootTitle', $GLOBALS['TL_DCA']['tl_page']['palettes'][ $key ]);
	}
}

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['keywords'] = array(
	'label'     => &$GLOBALS['TL_LANG']['tl_article']['keywords'],
	'exclude'   => true,
	'inputType' => 'textarea'
);

$GLOBALS['TL_DCA']['tl_page']['fields']['xseo_rootTitle'] = array(
	'label'     => &$GLOBALS['TL_LANG']['tl_settings']['websiteTitle'],
	'exclude'   => true,
	'inputType' => 'text',
	'eval'      => array('tl_class' => 'long clr')
);
