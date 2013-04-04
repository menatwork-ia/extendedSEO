<?php

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2013 
 * @package    extendedSEO
 * @license    GNU/LGPL
 * @filesource
 */

class ExtendedSeo extends Backend
{
	const KEYWORDS = 1;
	const DESCRIPTION = 2;
	const ROOTTITLE = 3;

	public function generatePage($objPage, $objLayout, $objPageRegular)
	{
		// Page Informations ---------------------------------------------------
		global $objPage;
		$strKeywords = $this->recursivePage($objPage->id, self::KEYWORDS);
		$strDescription = $this->recursivePage($objPage->id, self::DESCRIPTION);
		$strTitle = $this->recursivePage($objPage->id, self::ROOTTITLE);

		// Keywords ------------------------------------------------------------                
		$arrSource = explode(",", $GLOBALS['TL_KEYWORDS']);
		if (!is_array($arrSource))
		{
			$arrSource = array();
		}

		$arrNew = trimsplit(",", $strKeywords);
		if (!is_array($arrNew))
		{
			$arrNew = array();
		}

		$arrSource = array_merge($arrSource, $arrNew);
		$arrSource = array_unique($arrSource);

		foreach ($arrSource as $key => $value)
		{
			if ($value == "")
			{
				unset($arrSource[$key]);
			}
		}

		$GLOBALS['TL_KEYWORDS'] = implode(",", $arrSource);

		// Description ---------------------------------------------------------
		if ($objPage->description == "" || $objPage->description == null)
		{
			$objPage->description = $strDescription;
		}
		
		// Root title ---------------------------------------------------------
		if ($strTitle != "")
		{
			$objPage->rootTitle = $strTitle;
		}
		
	}

	private function recursivePage($pid, $intSearch)
	{
		$arrPage = $this->Database
				->prepare("SELECT * FROM tl_page WHERE id=?")
				->execute($pid)
				->fetchAllAssoc();

		switch ($intSearch)
		{
			case self::KEYWORDS:
				// If we have found the rootpage, return it
				if ($arrPage[0]["pid"] == 0)
				{
					return $arrPage[0]["keywords"];
				}

				// If we have found some informations return it or search on next part
				if (strlen($arrPage[0]["keywords"]) != 0)
				{
					return $arrPage[0]["keywords"];
				}
				else
				{
					return $this->recursivePage($arrPage[0]["pid"], $intSearch);
				}

				break;

			case self::DESCRIPTION:
				// If we have found the rootpage, return it
				if ($arrPage[0]["pid"] == 0)
				{
					return $arrPage[0]["description"];
				}
				// If we have found some informations return it or search on next part
				if (strlen($arrPage[0]["description"]) != 0)
				{
					return $arrPage[0]["description"];
				}
				else
				{
					return $this->recursivePage($arrPage[0]["pid"], $intSearch);
				}

				break;
			
			case self::ROOTTITLE:
				// If we have found the rootpage, return it
				if ($arrPage[0]["pid"] == 0)
				{
					return '';
				}

				// If we have found some informations return it or search on next part
				if (strlen($arrPage[0]["rootTitle"]) != 0)
				{
					return $arrPage[0]["rootTitle"];
				}
				else
				{
					return $this->recursivePage($arrPage[0]["pid"], $intSearch);
				}

				break;

			default:
				// Default return nothing
				return "";
				break;
		}
	}
}