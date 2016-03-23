<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @package news_plus
 * @author  Mathias Arzberger <develop@pdir.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsPlus;


/**
 * Class NewsPlusModel
 *
 * @package HeimrichHannot\NewsPlus
 */
class NewsPlusModel extends \NewsModel
{

	/**
	 * Find published news items by filter
	 *
	 * @param NewsFilterRegistry $objFilter   The Filter Registry Object
	 * @param boolean            $blnFeatured If true, return only featured news, if false, return only unfeatured news
	 * @param integer            $intLimit    An optional limit
	 * @param integer            $intOffset   An optional offset
	 * @param array              $arrOptions  An optional options array
	 *
	 * @return integer The number of news items
	 */
	public static function findPublishedByFilter(NewsFilterRegistry $objFilter, $blnFeatured = null, $intLimit = 0, $intOffset = 0, array $arrOptions = array())
	{
		$t = static::$strTable;

		$arrColumns = $objFilter->getWhereSql();

		if ($blnFeatured === true) {
			$arrColumns[] = "$t.featured=1";
		} elseif ($blnFeatured === false) {
			$arrColumns[] = "$t.featured=''";
		}

		// Never return unpublished elements in the back end, so they don't end up in the RSS feed
		if (!BE_USER_LOGGED_IN || TL_MODE == 'BE') {
			$time         = time();
			$arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
		}

		if (!isset($arrOptions['order'])) {
			$arrOptions['order'] = "$t.date DESC";
		}

		$arrOptions['limit']  = $intLimit;
		$arrOptions['offset'] = $intOffset;

		return static::findBy($arrColumns, null, $arrOptions);
	}


	/**
	 * Count published news items by filter
	 *
	 * @param NewsFilterRegistry $objFilter   The Filter Registry Object
	 * @param boolean            $blnFeatured If true, return only featured news, if false, return only unfeatured news
	 * @param array              $arrOptions  An optional options array
	 *
	 * @return integer The number of news items
	 */
	public static function countPublishedByFilter(NewsFilterRegistry $objFilter, $blnFeatured = null, array $arrOptions = array())
	{
		$t = static::$strTable;

		$arrColumns = $objFilter->getWhereSql();

		if ($blnFeatured === true) {
			$arrColumns[] = "$t.featured=1";
		} elseif ($blnFeatured === false) {
			$arrColumns[] = "$t.featured=''";
		}

		if (!BE_USER_LOGGED_IN) {
			$time         = time();
			$arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
		}

		return static::countBy($arrColumns, null, $arrOptions);
	}


	/**
	 * Find news items by their parent ID
	 *
	 * @param array   $arrPids    An array of news archive IDs
	 * @param integer $intLimit   An optional limit
	 * @param array   $arrOptions An optional options array
	 *
	 * @return \Model\Collection|null A collection of models or null if there are no news
	 */
	public static function findByPids(array $arrPids=array(), $intLimit = 0, array $arrOptions = array())
	{
		$t    = static::$strTable;

		$arrColumns = array("$t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")");

		if (!isset($arrOptions['order']))
		{
			$arrOptions['order'] = "$t.date DESC";
		}

		if ($intLimit > 0) {
			$arrOptions['limit'] = $intLimit;
		}

		return static::findBy($arrColumns, null, $arrOptions);
	}

	/**
	 * Find published news items by their IDs
	 *
	 * @param array   $arrIds     An array of news ids
	 * @param integer $intLimit   An optional limit
	 * @param array   $arrOptions An optional options array
	 *
	 * @return \Model\Collection|null A collection of models or null if there are no news
	 */
	public static function findPublishedByIds(array $arrIds = array(), $intLimit = 0, array $arrOptions = array())
	{
		$time = time();
		$t    = static::$strTable;

		$arrColumns = array("$t.id IN(" . implode(',', array_map('intval', $arrIds)) . ")");

		$arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";

		if (!isset($arrOptions['order'])) {
			$arrOptions['order'] = "$t.date DESC";
		}

		if ($intLimit > 0) {
			$arrOptions['limit'] = $intLimit;
		}

		return static::findBy($arrColumns, null, $arrOptions);
	}

	/**
	 * Find published news items by their parent ID
	 *
	 * @param integer $intId      The news archive ID
	 * @param integer $intLimit   An optional limit
	 * @param array   $arrOptions An optional options array
	 *
	 * @return \Model\Collection|null A collection of models or null if there are no news
	 */
	public static function findPublishedByPid($intId, $intLimit = 0, array $arrOptions = array())
	{
		$time = time();
		$t    = static::$strTable;

		$arrColumns = array("$t.pid=? AND ($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1");

		if (!isset($arrOptions['order'])) {
			$arrOptions['order'] = "$t.date DESC";
		}

		if ($intLimit > 0) {
			$arrOptions['limit'] = $intLimit;
		}

		return static::findBy($arrColumns, $intId, $arrOptions);
	}

	public static function findNewPublishedByIds($currentID, $currentDate, $arrIds, $blnInfinite=false, $strType='default', array $arrOptions = array())
	{
		$time = time();
		$t    = static::$strTable;

		$arrValues = array();
		$arrColumns[] = "$t.id IN(" . implode(',', array_map('intval', $arrIds)) . ")";
		
		$arrColumns['id'] = "$t.id != ?";
		$arrValues['id'] = $currentID;

		$arrColumns['date'] = "$t.date >= ?";
		$arrValues['date'] = $currentDate;

		$arrColumns['source'] = "$t.source = ?";
		$arrValues['source'] = $strType;

		if (!BE_USER_LOGGED_IN) {
			$time         = time();
			$arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
		}

		$arrOptions['order'] = 'date';

		$arrOptions['limit'] = 1;

		$objModel = static::findBy($arrColumns, $arrValues, $arrOptions);

		if($objModel === null && $blnInfinite)
		{
			$objModel = static::findLastPublishedByIds($arrIds, $strType, $arrOptions);
		}

		return $objModel;
	}


	public static function findOldPublishedByIds($currentID, $currentDate, $arrIds, $blnInfinite=false, $strType='default', array $arrOptions = array())
	{
		$time = time();
		$t    = static::$strTable;

		$arrValues = array();
		$arrColumns[] = "$t.id IN(" . implode(',', array_map('intval', $arrIds)) . ")";

		$arrColumns['id'] = "$t.id != ?";
		$arrValues['id'] = $currentID;

		$arrColumns['date'] = "$t.date <= ?";
		$arrValues['date'] = $currentDate;

		$arrColumns['source'] = "$t.source = ?";
		$arrValues['source'] = $strType;

		if (!BE_USER_LOGGED_IN) {
			$time         = time();
			$arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
		}

		$arrOptions['limit'] = 1;

		$arrOptions['order'] = 'date DESC';

		$objModel = static::findBy($arrColumns, $arrValues, $arrOptions);

		if($objModel === null && $blnInfinite)
		{
			$objModel = static::findFirstPublishedByIds($arrIds, $strType, $arrOptions);
		}

		return $objModel;
	}

	public static function findFirstPublishedByIds($arrIds, $strType='default', array $arrOptions = array())
	{
		$time = time();
		$t    = static::$strTable;

		$arrValues = array();
		$arrColumns[] = "$t.id IN(" . implode(',', array_map('intval', $arrIds)) . ")";

		$arrColumns['source'] = "$t.source = ?";
		$arrValues['source'] = $strType;

		if (!BE_USER_LOGGED_IN) {
			$time         = time();
			$arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
		}

		$arrOptions['limit'] = 1;

		$arrOptions['order'] = 'date DESC';

		return static::findBy($arrColumns, $arrValues, $arrOptions);
	}

	public static function findLastPublishedByIds($arrIds, $strType='default', array $arrOptions = array())
	{
		$time = time();
		$t    = static::$strTable;

		$arrValues = array();
		$arrColumns[] = "$t.id IN(" . implode(',', array_map('intval', $arrIds)) . ")";

		$arrColumns['source'] = "$t.source = ?";
		$arrValues['source'] = $strType;

		if (!BE_USER_LOGGED_IN) {
			$time         = time();
			$arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
		}

		$arrOptions['limit'] = 1;

		$arrOptions['order'] = 'date';

		return static::findBy($arrColumns, $arrValues, $arrOptions);
	}
}