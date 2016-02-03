<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @package AVV
 * @author  Oliver Janke <o.janke@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\NewsPlus;


class NewsFilterRegistry
{
	protected $arrData = array();

	protected $arrFields = array();

	protected $arrSubmission = array();

	protected static $strTable = 'tl_news';

	protected static $arrFieldAlias = array
	(
		'pid' => 'news_archives',
		'cat' => 'news_categories',
	);

	protected $arrAllCategories = array();

	protected $arrNewsIds = array();

	protected $arrNewsIdsExclude = array();

	/**
	 * Object instances (Singleton)
	 *
	 * @var array
	 */
	protected static $arrInstances = array();

	/** @var  NewsFilterForm */
	protected $objFilter;

	protected function __construct(array $arrConfig)
	{
		// set defaults, to provide getWhereSql() functionality for news_list without filter
		$this->arrData = \HeimrichHannot\Haste\Util\Arrays::filterByPrefixes($arrConfig, array('news_', 'root'));

		if (($objFilter = \ModuleModel::findByPk($arrConfig['news_filterModule'])) !== null) {
			$this->objFilter        = new NewsFilterForm($objFilter);
			$this->arrFields        = deserialize($objFilter->formHybridEditable, true);
			$this->arrAllCategories = \NewsCategories\NewsModel::getCategoriesCache();
			$this->init();
		}
	}


	public static function getInstance(array $arrConfig)
	{
		$strKey = $arrConfig['news_filterModule'];

		if (!isset(static::$arrInstances[$strKey]))
		{
			static::$arrInstances[$strKey] = new static($arrConfig);
		}

		return static::$arrInstances[$strKey];
	}

	private function __clone()
	{
	}

	protected function init()
	{
		$this->objFilter->generate();
		$this->arrSubmission = $this->objFilter->getSubmission(false, true);
		
		if ($this->arrSubmission === null) {
			return false;
		}

		foreach ($this->arrSubmission as $strName => $varValue) {
			$strKey          = isset(static::$arrFieldAlias[$strName]) ? static::$arrFieldAlias[$strName] : $strName;
			$this->{$strKey} = $varValue;
		}

		if ($this->news_filterCategories) {
			$this->initCategories();
		}
	}

	protected function initCategories()
	{
		if (!is_array($this->arrAllCategories)) {
			return false;
		}

		$this->arrNewsIds        = array_merge($this->arrNewsIds, $this->getNewsFromCategories(deserialize($this->news_filterDefault, true)));
		$this->arrNewsIdsExclude = array_merge($this->arrNewsIdsExclude, $this->getNewsFromCategories(deserialize($this->news_filterDefaultExclude, true)));
	}

	protected function getNewsFromCategories(array $arrCategories = array())
	{
		$arrNewsIds = array();

		foreach ($arrCategories as $category) {
			if (isset($this->arrAllCategories[$category])) {
				$arrNewsIds = array_merge($this->arrAllCategories[$category], $arrNewsIds);
			}
		}

		return $arrNewsIds;
	}

	/**
	 * Set an object property
	 *
	 * @param string $strKey
	 * @param mixed  $varValue
	 */
	public function __set($strKey, $varValue)
	{
		$this->arrData[$strKey] = $varValue;
	}


	/**
	 * Return an object property
	 *
	 * @param string $strKey
	 *
	 * @return mixed
	 */
	public function __get($strKey)
	{
		if (isset($this->arrData[$strKey])) {
			return $this->arrData[$strKey];
		}
	}


	/**
	 * Check whether a property is set
	 *
	 * @param string $strKey
	 *
	 * @return boolean
	 */
	public function __isset($strKey)
	{
		return isset($this->arrData[$strKey]);
	}

	/**
	 * Return the filter data
	 */
	public function getData()
	{
		return $this->arrData;
	}

	public function getWhereSql()
	{
		$t = static::$strTable;

		$arrColumns = array();

		// archives
		if(is_array($this->news_archives) && !empty($this->news_archives))
		{
			$arrColumns['news_archives'] = "$t.pid IN(" . implode(',', array_map('intval', $this->news_archives)) . ")";
		}

		// iterate over all fields
		foreach ($this->arrFields as $strName)
		{
			$strKey = isset(static::$arrFieldAlias[$strName]) ? static::$arrFieldAlias[$strName] : $strName;

			if (($varValue = $this->createFieldSql($strKey)) !== null)
			{
				$arrColumns[$strKey] = $varValue;
			}
		}

		// news ids : first - add news ids that should be added to the result
		if (is_array($this->arrNewsIds) && !empty($this->arrNewsIds))
		{
			$arrColumns['ids'] = "$t.id IN(" . implode(',', array_map('intval', array_unique($this->arrNewsIds))) . ")";
		}

		// news ids : second - remove news ids that should be excluded from the result
		if (is_array($this->arrNewsIdsExclude) && !empty($this->arrNewsIdsExclude)) {
			$arrColumns['ids_exclude'] = "$t.id NOT IN(" . implode(',', array_map('intval', array_unique($this->arrNewsIdsExclude))) . ")";
		}
		
		return $arrColumns;
	}

	protected function createFieldSql($strName)
	{
		if ($this->arrData[$strName] == '') {
			return null;
		}

		$t = static::$strTable;

		switch ($strName) {
			case 'news_categories':

				if (!$this->news_filterCategories)
				{
					return null;
				}

				$arrNewsIds = $this->getNewsFromCategories($this->news_categories);

				// preserve ids beside to users selection
				if ($this->news_filterPreserve)
				{
					$this->arrNewsIds = array_merge($this->arrNewsIds, $arrNewsIds);
					return null;
				}

				$this->arrNewsIds = $arrNewsIds;
				return null;
			case 'startDate':
				return "$t.date >= " . strtotime($this->startDate . ' 00:00:00');
			case 'endDate':
				return "$t.date <= " . strtotime($this->endDate . ' 23:59:59');
			case 'trailInfoDistanceMin':
				$strMin = str_replace(',', '.', $this->trailInfoDistanceMin);

				return "($t.addTrailInfoDistance=1 AND $t.trailInfoDistanceMax>=$strMin)";
			case 'trailInfoDistanceMax':
				$strMax = str_replace(',', '.', $this->trailInfoDistanceMax);

				return "($t.addTrailInfoDistance=1 AND ($t.trailInfoDistanceMax<=$strMax OR ($t.trailInfoDistanceMin>0.0 AND $t.trailInfoDistanceMin<=$strMax)))";
			case 'trailInfoDurationMin':
				$strMin = str_replace(',', '.', $this->trailInfoDurationMin);

				return "($t.addTrailInfoDuration=1 AND $t.trailInfoDurationMax>=$strMin)";
			case 'trailInfoDurationMax':
				$strMax = str_replace(',', '.', $this->trailInfoDurationMax);

				return "($t.addTrailInfoDuration=1 AND ($t.trailInfoDurationMax<=$strMax OR ($t.trailInfoDurationMin>0.0 AND $t.trailInfoDurationMax<=$strMax)))";
			case 'trailInfoDifficultyMin':
				$strMin = str_replace(',', '.', $this->trailInfoDifficultyMin);

				return "($t.addTrailInfoDifficulty=1 AND $t.trailInfoDifficultyMax>=$strMin)";
			case 'trailInfoDifficultyMax':
				$strMax = str_replace(',', '.', $this->trailInfoDifficultyMax);

				return "($t.addTrailInfoDifficulty=1 AND ($t.trailInfoDifficultyMax<=$strMax OR ($t.trailInfoDifficultymin>0.0 AND $t.trailInfoDifficultyMin<=$strMax)))";
			case 'trailInfoStart':
				return "$t.trailInfoStart LIKE '%" . $this->trailInfoStart . "%'";
			case 'trailInfoDestination':
				return "$t.trailInfoDestination LIKE '%" . $this->trailInfoDestination . "%'";
			case 'q':
				if(($arrNewsIds = $this->findNewsInSearchIndex($this->q, ($this->objFilter->news_filterSearchQueryType != true), ($this->objFilter->news_filterFuzzySearch == true))) !== null)
				{
					return "($t.id IN(" . implode(',', array_map('intval', array_unique($arrNewsIds))) . "))";
				}
				return null;
		}

		return null;
	}

	/**
	 * @param      $strKeywords
	 * @param bool $blnOrSearch
	 * @param bool $blnFuzzy
	 *
	 * @return array|int|null Return an array of news ids, or 0 if nothing was found, or null if something went wrong
	 */
	protected function findNewsInSearchIndex($strKeywords,$blnOrSearch=false, $blnFuzzy=false)
	{
		global $objPage;

		$t = static::$strTable;

		$intRoot = $this->rootPage > 0 ? $this->rootPage : $objPage->rootId;

		$arrPages = \Database::getInstance()->getChildRecords($intRoot, 'tl_page');

		try
		{
			$objSearch = \Search::searchFor($strKeywords, $blnOrSearch, $arrPages, null, null, $blnFuzzy);

			if($objSearch->numRows < 1)
			{
				return 0;
			}

			$arrUrls = $objSearch->fetchEach('url');
			$strKeyWordColumns = "";
			$n = 0;

			foreach($arrUrls as $i => $strAlias)
			{
				$strKeyWordColumns .= ($n > 0 ? " OR " : "") . "$t.alias = ?";
				$arrValues[] = basename($strAlias);
				$n++;
			}
			
			$arrColumns[] = "($strKeyWordColumns)";

			$objNews = \HeimrichHannot\NewsPlus\NewsPlusModel::findBy($arrColumns, $arrValues);
			
			if($objNews !== null)
			{
				return $objNews->fetchEach('id');
			}

			return 0;
		}
		catch (\Exception $e)
		{
			\System::log('Website search failed: ' . $e->getMessage(), __METHOD__, TL_ERROR);
			return null;
		}

		return null;
	}


}