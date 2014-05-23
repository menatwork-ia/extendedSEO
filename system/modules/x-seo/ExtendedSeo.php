<?php

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2013
 * @package    extendedSEO
 * @license    GNU/LGPL
 * @filesource
 */
class ExtendedSeo
{
	const KEYWORDS        = 1;
	const DESCRIPTION     = 2;
	const ROOT_TITLE      = 3;
	const ROOT_PAGE_TITLE = 4;

	/**
	 * Callback from contao.
	 *
	 * @param $objPage
	 * @param $objLayout
	 * @param $objPageRegular
	 */
	public function generatePage($objPage, $objLayout, $objPageRegular)
	{
		global $objPage;

		// Try to get the data.
		$strKeywords    = $this->recursivePage($objPage->id, self::KEYWORDS);
		$strDescription = $this->recursivePage($objPage->id, self::DESCRIPTION);

		// Get the title, first try the roottitle field after this the rootpagetitle.
		$strTitle = $this->recursivePage($objPage->id, self::ROOT_TITLE);
		if (empty($strTitle))
		{
			$strTitle = $this->recursivePage($objPage->id, self::ROOT_PAGE_TITLE);
		}

		// Update the page details.
		$this->updateKeywords($strKeywords);
		$this->changePageDescription($strDescription, $objPage);
		$this->changePageTitle($strTitle, $objPage);
	}

	/**
	 * Add new keywords to the current list.
	 *
	 * @param string $strKeywords The new keywords for adding.
	 */
	protected function updateKeywords($strKeywords)
	{
		// Have we new keywords for adding, if not return.
		if (empty($strKeywords))
		{
			return;
		}

		// Split all keywords.
		$arrSource = trimsplit(',', $GLOBALS['TL_KEYWORDS']);
		if (!is_array($arrSource))
		{
			$arrSource = array();
		}

		// Split all new keywords.
		$arrNew = trimsplit(",", $strKeywords);
		if (!is_array($arrNew))
		{
			$arrNew = array();
		}

		// Merge, unique and cleanup.
		$arrSource = array_merge($arrSource, $arrNew);
		$arrSource = array_unique($arrSource);
		array_filter($arrSource, "strlen");

		// Set the keywords.
		$GLOBALS['TL_KEYWORDS'] = implode(",", $arrSource);
	}

	/**
	 * If the page have no description, add the new one.
	 *
	 * @param string     $strDescription The description.
	 *
	 * @param \PageModel $objPage
	 */
	protected function changePageDescription($strDescription, $objPage)
	{
		// Have we a new description for adding, if not return.
		if (empty($strDescription))
		{
			return;
		}

		// Set the description only the current one is empty.
		if (empty($objPage->description))
		{
			$objPage->description = $strDescription;
		}
	}

	/**
	 * Set the page title.
	 *
	 * @param string     $strTitle The new page title.
	 *                             *
	 * @param \PageModel $objPage
	 */
	protected function changePageTitle($strTitle, $objPage)
	{
		// Have we a new title for adding, if not return.
		if (empty($strTitle))
		{
			return;
		}

		// Set the new titles.
		$objPage->rootTitle     = $strTitle;
		$objPage->rootPageTitle = $strTitle;
	}

	/**
	 * Scann the page tree for information.
	 *
	 * @param int $intId     The id for the start page.
	 *
	 * @param int $intSearch The const. for the filed.
	 *
	 * @return mixed|null The text if something was found or null if no result.
	 */
	private function recursivePage($intId, $intSearch)
	{
		// Get the page by the id.
		$objPage = \Database::getInstance()
			->prepare('SELECT * FROM tl_page WHERE id=?')
			->execute($intId);

		// If we have no page return.
		if ($objPage == null)
		{
			return null;
		}

		// Which field we have to check?
		switch ($intSearch)
		{
			case self::KEYWORDS:
				$strFieldWithData = 'keywords';
				break;

			case self::DESCRIPTION:
				$strFieldWithData = 'description';
				break;

			case self::ROOT_TITLE:
				$strFieldWithData = 'rootTitle';
				break;

			case self::ROOT_PAGE_TITLE:
				$strFieldWithData = 'pageTitle';
				break;

			// Default return nothing
			default:
				return null;
		}

		// If we have found the rootpage, return it
		if ($objPage->pid == 0)
		{
			return $objPage->$strFieldWithData;
		}
		// If we have found some informations return it or search on next part
		elseif (strlen($objPage->$strFieldWithData) != 0)
		{
			return $objPage->$strFieldWithData;
		}
		// If we have a pid try the next page.
		elseif (!empty($objPage->pid))
		{
			return $this->recursivePage($objPage->pid, $intSearch);
		}
		// Else return nothing.
		else
		{
			return null;
		}
	}
}