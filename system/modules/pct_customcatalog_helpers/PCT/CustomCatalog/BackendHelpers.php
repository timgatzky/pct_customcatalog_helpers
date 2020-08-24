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
 * Namespace
 */
namespace PCT\CustomCatalog;

/**
 * Imports
 */
use Contao\Backend;
use Contao\ContaoModel;
use Contao\Model\Registry;
use Contao\StringUtil;

/**
 * Class
 * BackendHelpers
 */
class BackendHelpers extends Backend
{
	/**
	 * Update the related entries of a tags or selectdb field with the ID of the current entry
	 * @param mixed
	 * @param object
	 * @return mixed
	 */
	public function updateRelatedItems($varValue, $objDC)
	{
		// find the attribute
		$objAttribute = \PCT\CustomElements\Plugins\CustomCatalog\Core\AttributeFactory::findByCustomCatalog($objDC->field,$objDC->table);
		if( $objAttribute === null )
		{
			return $varValue;
		}

		$varValue = StringUtil::deserialize($varValue);
		if( \is_array($varValue) === false )
		{
			$varValue = \explode(',',$varValue);
		}
		$varValue = \array_filter($varValue);

		// multiple flag
		$blnMultiple = (boolean)$GLOBALS['TL_DCA'][$objDC->table]['fields'][$objDC->field]['eval']['multiple'];
		
		$objModelHelper = new ContaoModel();
		$objModelHelper->setTable( $objDC->table );
		$objRegistry = Registry::getInstance();

		// find selected records
		$objModels = $objModelHelper::findMultipleByIds($varValue);

		// update the selected records
		if( $objModels !== null )
		{
			foreach($objModels as $model)
			{
				// register the model
				$objRegistry->register($model);
				
				$values = StringUtil::deserialize( $model->{$objDC->field} );
				if( $blnMultiple === true )
				{
					$values[] = $objDC->id;
					$model->__set($objDC->field, \array_unique($values) );
				}
				else
				{
					$model->__set($objDC->field, $objDC->id);
				}
				// update the record
				$model->save();
			}
		}
		
		return $varValue;
	}
}