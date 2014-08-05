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
 * Language2file runonce
 */
class ExtendedSeoRunOnce
{
    public function run()
    {
        try
        {
            // Check if we have the table.
            if (!\Database::getInstance()->tableExists('tl_page'))
            {
                throw new \RuntimeException('Missing table tl_page.');
            }

            // Check if we have we to update.
            if (\Database::getInstance()->fieldExists('xseo_rootTitle', 'tl_page'))
            {
                return;
            }

            // Add field.
            $strSQL = 'ALTER TABLE tl_page ADD xseo_rootTitle varchar(255) NOT NULL default \'\'';
            \Database::getInstance()->query($strSQL);

            // Copy data.
            $strSQL    = 'SELECT id, rootTitle FROM tl_page WHERE rootTitle != \'\'';
            $arrResult = \Database::getInstance()
                ->prepare($strSQL)
                ->execute()
                ->fetchAllAssoc();

            foreach ($arrResult as $arrRow)
            {
                $strSQL = 'UPDATE tl_page SET xseo_rootTitle = ? WHERE id = ?';
                \Database::getInstance()
                    ->prepare($strSQL)
                    ->execute($arrRow['rootTitle'], $arrRow['id']);
            }

            \System::log('Update ExtendedSeo.', __CLASS__ . ' || ' . __FUNCTION__, TL_ERROR);
        }
        catch (Exception $e)
        {
            \System::log('Error on updating the ExtendedSeo extension with msg: ' . $e->getMessage(), __CLASS__ . ' || ' . __FUNCTION__, TL_ERROR);
        }
    }
}

$objL2fRunOnce = new ExtendedSeoRunOnce();
$objL2fRunOnce->run();