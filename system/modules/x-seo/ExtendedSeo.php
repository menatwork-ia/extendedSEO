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
	/**
	 * Const. for the fields.
	 */
	const KEYWORDS        = 1;
	const DESCRIPTION     = 2;
	const ROOT_TITLE      = 3;

	/**
	 * The cache for the pages.
	 *
	 * @var array
	 */
	protected $arrPageCache = array();

	/**
	 * Callback from contao.
	 *
	 * @param $objPage
	 * @param $objLayout
	 * @param $objPageRegular
	 */
	public function generatePage($objPage, $objLayout, $objPageRegular)
	{
		// Get the current page.
		global $objPage;

		// Add the current page to the cache.
		$this->arrPageCache[ $objPage->id ] = $objPage;

		// Try to get the data.
		$strKeywords    = $this->recursivePage($objPage->id, self::KEYWORDS);
		$strDescription = $this->recursivePage($objPage->id, self::DESCRIPTION);

		// Get the title for the page. Try for each normal page the xseo_rootTitle.
		// If we reached the rootPage use the pageTitle if not the follow the contao way.
		$strTitle = $this->recursivePage($objPage->id, self::ROOT_TITLE);

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
		$arrNew = trimsplit(',', $strKeywords);
		if (!is_array($arrNew))
		{
			$arrNew = array();
		}

		// Merge, unique and cleanup.
		$arrSource = array_merge($arrSource, $arrNew);
		$arrSource = array_unique($arrSource);
		array_filter($arrSource, 'strlen');

		// Set the keywords.
		$GLOBALS['TL_KEYWORDS'] = implode(',', $arrSource);
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
	 * Resolve page details and add it to the cache.
	 *
	 * @param $intId
	 *
	 * @return mixed
	 */
	protected function getPageDetails($intId)
	{
		// Check if we have the data in the cache.
		if (!isset($this->arrPageCache[ $intId ]))
		{
			// Get the page by the id.
			$objPage = \Contao\PageModel::findWithDetails($intId);

			// Add to the cache.
			$this->arrPageCache[ $intId ] = $objPage;
		}

		return $this->arrPageCache[ $intId ];
	}

	/**
	 * Scan the page tree for information.
	 *
	 * @param int $intId     The id for the start page.
	 *
	 * @param int $intSearch The const. for the filed.
	 *
	 * @return mixed|null The text if something was found or null if no result.
	 */
	private function recursivePage($intId, $intSearch)
	{
		// Get the page details.
		$objPage = $this->getPageDetails($intId);

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
				$strFieldWithData = 'xseo_rootTitle';
				break;

			// Default return nothing
			default:
				return null;
		}

		// If we reached the rootPage ...
		if ($objPage->pid == 0)
		{
			// and the title is requested, return the normal one ...
			if ($intSearch == self::ROOT_TITLE)
			{
				return $objPage->pageTitle;
			}
			// else all other data ...
			elseif (strlen($objPage->$strFieldWithData) != 0)
			{
				return $objPage->$strFieldWithData;
			}
			// or null if we have not data.
			else
			{
				return null;
			}
		}
		// Get the data if we have some one ...
		elseif (strlen($objPage->$strFieldWithData) != 0)
		{
			return $objPage->$strFieldWithData;
		}
		// or ask the parent for more information.
		elseif (!empty($objPage->pid))
		{
			return $this->recursivePage($objPage->pid, $intSearch);
		}
		// At this point give up.
		else
		{
			return null;
		}
	}
}
