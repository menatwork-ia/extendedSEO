<?php

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2013 
 * @package    extendedSEO
 * @license    GNU/LGPL
 * @filesource
 */

/*
 * Hooks
 */
$GLOBALS['TL_HOOKS']['generatePage'][] = array('ExtendedSeo', 'generatePage');