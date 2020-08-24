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
		if( $objAttribute === null || (boolean)$objAttribute->published === false )
		{
			return $varValue;
		}

		// multiple flag
		$blnMultiple = (boolean)$GLOBALS['TL_DCA'][$objDC->table]['fields'][$objDC->field]['eval']['multiple'];
		// @var object
		$objModelHelper = new ContaoModel();
		$objModelHelper->setTable( $objDC->table );
		$objRegistry = Registry::getInstance();

		$varValue = StringUtil::deserialize($varValue);
		if( \is_array($varValue) === false )
		{
			$varValue = \explode(',',$varValue);
		}
		$varValue = \array_filter($varValue);

		// compare to the entries from load_callback
		$arrSession = $this->Session->get('related_items');
		$arrUnset = array();
		if( empty($arrSession[$objDC->table][$objDC->field]) === false )
		{
			$arrUnset = \array_diff($arrSession[$objDC->table][$objDC->field],$varValue);
		}

		if( empty($arrUnset) === false )
		{
			// find selected records
			$objModels = $objModelHelper::findMultipleByIds($arrUnset);
			foreach($objModels as $model)
			{
				// register the model
				$objRegistry->register($model);
				
				$values = StringUtil::deserialize( $model->{$objDC->field} );
				if( $blnMultiple === true )
				{
					$i = \array_search($objDC->id, $values);
					unset($values[$i]);
					$model->__set($objDC->field, \array_unique($values) );
				}
				else
				{
					$model->__set($objDC->field, null);
				}
				// update the record
				$model->save();
			}
		}

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


	/**
	 * Store the current related entries in the session to compare against the new ones saved
	 * @param mixed
	 * @param object
	 * @return mixed
	 */
	public function currentRelatedItems($varValue, $objDC)
	{
		// find the attribute
		$objAttribute = \PCT\CustomElements\Plugins\CustomCatalog\Core\AttributeFactory::findByCustomCatalog($objDC->field,$objDC->table);
		if( $objAttribute === null || (boolean)$objAttribute->published === false )
		{
			return $varValue;
		}

		$varValue = StringUtil::deserialize($varValue);
		if( \is_array($varValue) === false )
		{
			$varValue = \explode(',',$varValue);
		}
		$varValue = \array_filter($varValue);

		// set session
		$arrSession[$objDC->table][$objDC->field] = $varValue;
		$this->Session->set('related_items', $arrSession);
		
		return $varValue;
	}
}