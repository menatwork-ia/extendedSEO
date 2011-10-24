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

class ExtendedSeo extends Backend
{
    const KEYWORDS = 1;
    const DESCRIPTION = 2;
    const ROOTTITLE = 3;

    public function generatePage(Database_Result $objPage, Database_Result $objLayout, PageRegular $objPageRegular)
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

?>