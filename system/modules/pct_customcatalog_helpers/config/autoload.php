<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2020 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2020
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		pct_customcatalog_helpers
 * @link		http://contao.org
 */

/**
 * Register the classes
 */
\Contao\ClassLoader::addClasses(array
(
	'PCT\CustomCatalog\Helpers' 				=> 'system/modules/pct_customcatalog_helpers/PCT/CustomCatalog/Helpers.php',
	'PCT\CustomCatalog\BackendHelpers' 			=> 'system/modules/pct_customcatalog_helpers/PCT/CustomCatalog/BackendHelpers.php',
));