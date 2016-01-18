<?php

namespace HeimrichHannot\NewsPlus;

class ModuleNewsListPlus extends ModuleNewsPlus
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_newslist_plus';

	protected $objFilter = null;

    protected $arrSubmission = array();

	protected $strKeywords;

	protected $t = "tl_news";

    protected $filterActive = false;

    protected $filterSearch = false;

	protected $news_categories = array();

    /**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['newslist_plus'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

		if($this->news_filterModule && ($objFilter = \ModuleModel::findByPk($this->news_filterModule)) !== null)
		{
			$this->initFilter($objFilter);
		}

        $this->news_archives = $this->sortOutProtected(deserialize($this->news_archives));

        // Return if there are no archives
        if (!is_array($this->news_archives) || empty($this->news_archives))
        {
            return '';
        }

		$GLOBALS['NEWS_FILTER_CATEGORIES'] = $this->news_filterCategories ? true : false;
		$GLOBALS['NEWS_FILTER_DEFAULT']    = deserialize($this->news_filterDefault, true);
        $GLOBALS['NEWS_FILTER_DEFAULT_EXCLUDE']    = deserialize($this->news_filterDefaultExclude, true);
		$GLOBALS['NEWS_FILTER_PRESERVE']   = $this->news_filterPreserve;


        // Show the event reader if an item has been selected
        if (!$this->news_showInModal && $this->news_readerModule > 0  && (isset($_GET['news']) || (\Config::get('useAutoItem') && isset($_GET['auto_item']))))
        {
            return $this->getFrontendModule($this->news_readerModule, $this->strColumn);
        }

        return parent::generate();
    }

    /**
     * Generate the module
     */
    protected function compile()
    {
        $offset = intval($this->skipFirst);
        $limit = null;

        // Maximum number of items
        if ($this->numberOfItems > 0) {
            $limit = $this->numberOfItems;
        }

        // Handle featured news
        if ($this->news_featured == 'featured') {
            $blnFeatured = true;
        } elseif ($this->news_featured == 'unfeatured') {
            $blnFeatured = false;
        } else {
            $blnFeatured = null;
        }

        // show all news items, filter is active - TODO: make configurable in tl_module
        if($this->filterActive)
        {
            $blnFeatured = null;
        }

        $this->Template->articles = array();
        $this->Template->empty = $GLOBALS['TL_LANG']['MSC']['emptyList'];

		// Get the total number of items
		$intTotal = NewsPlusModel::countPublishedByPids($this->news_archives, $this->news_categories, $blnFeatured, array(), $this->startDate, $this->endDate);

		if ($intTotal < 1) {
			return;
		}

        $arrFilterIds = array();

		// get items by tag tid
		if(in_array('tags', \ModuleLoader::getActive()))
		{
			$arrFilterIds = NewsPlusTagHelper::getNewsIdByTableAndTag(\Input::get("tag"));
		}

        if($this->filterSearch)
        {
            $objKeywordArticles = static::findNewsInSearchIndex($this->strKeywords, ($this->objFilter->news_filterSearchQueryType != true), ($this->objFilter->news_filterFuzzySearch == true));

			if($objKeywordArticles !== null)
			{
				$arrFilterIds = array_merge($arrFilterIds, $objKeywordArticles->fetchEach('id'));
			}
		}


        if(is_array($arrFilterIds) && !empty($arrFilterIds))
        {
            // Get the total number of items
            $intTotal = NewsPlusModel::countPublishedByPidsAndIds($this->news_archives, $arrFilterIds, $this->news_categories, $blnFeatured, array(), $this->startDate, $this->endDate);

            if ($intTotal < 1) {
                return;
            }
        }

        $total = $intTotal - $offset;

        // Adjust the overall limit
        if(isset($limit)) {
            $total = min($limit, $total);
        }

        // Split the results
        if ($this->perPage > 0 && (!isset($limit) || $this->numberOfItems > $this->perPage)) {
            // Get the current page
            $id   = 'page_n' . $this->id;
            $page = \Input::get($id) ?: 1;

            //Set limit and offset
            $limit = $this->perPage;
            $offset += (max($page, 1) - 1) * $this->perPage;
            $skip = intval($this->skipFirst);

            // Overall limit
            if ($offset + $limit > $total + $skip) {
                $limit = $total + $skip - $offset;
            }
        }

		$objArticles = $this->fetchItems($this->news_archives, $blnFeatured, ($limit ?: 0), $offset, $arrFilterIds, $this->news_categories,  $this->startDate, $this->endDate);

        // store all events ids in session
        $arrUrlParam = array();
        if(\Input::get("newscategories")) $arrUrlParam[] = 'newscategories=' . \Input::get("newscategories");
        if(\Input::get("searchKeywords")) $arrUrlParam[] = 'searchKeywords=' . \Input::get("searchKeywords");
        if(\Input::get("startDate"))      $arrUrlParam[] = 'startDate=' . \Input::get("startDate");
        if(\Input::get("endDate"))        $arrUrlParam[] = 'endDate=' . \Input::get("endDate");

        // show only news by tag
        if(\Input::get("tag")) $arrUrlParam[] = 'tag=' . \Input::get("tag");

		// Split the results
		if($limit > 0 && $limit < $total)
		{
			// Do not index or cache the page if the page number is outside the range
			if ($page < 1 || $page > max(ceil($total / $this->perPage), 1)) {
				global $objPage;
				$objPage->noSearch = 1;
				$objPage->cache = 0;

				// Send a 404 header
				header('HTTP/1.1 404 Not Found');
				return;
			}

			// Add the pagination menu
			$objPagination = new \Pagination($total, $this->perPage, \Config::get('maxPaginationLinks'), $id);
			$this->Template->pagination = $objPagination->generate("\n  ");
		}

        // Add the articles
        if ($objArticles !== null)
		{
            $this->Template->articles = $this->parseArticles($objArticles);
        }
        $this->Template->archives = $this->news_archives;
    }

	/**
	 * Fetch the matching items
	 *
	 * @param  array   $newsArchives
	 * @param  boolean $blnFeatured
	 * @param  integer $limit
	 * @param  integer $offset
	 *
	 * @return \Model\Collection|\NewsModel|null
	 */
	protected function fetchItems($newsArchives, $blnFeatured, $limit, $offset, $arrFilterIds, $newsCategories, $startDate, $endDate)
	{
		// HOOK: add custom logic
		if (isset($GLOBALS['TL_HOOKS']['newsListPlusFetchItems']) && is_array($GLOBALS['TL_HOOKS']['newsListPlusFetchItems']))
		{
			foreach ($GLOBALS['TL_HOOKS']['newsListPlusFetchItems'] as $callback)
			{
				if (($objCollection = \System::importStatic($callback[0])->{$callback[1]}($newsArchives, $blnFeatured, $limit, $offset, $arrFilterIds, $newsCategories, $startDate, $endDate, $this)) === false)
				{
					continue;
				}

				if ($objCollection === null || $objCollection instanceof \Model\Collection)
				{
					return $objCollection;
				}
			}
		}
		
		// store all news item ids in the session
		if($this->objFilter !== null)
		{
			$objAllItems = NewsPlusModel::findPublishedByPidsAndIds($newsArchives, $arrFilterIds, $newsCategories, $blnFeatured, 0, 0, array(), $this->startDate, $this->endDate);

			if($objAllItems !== null)
			{
				// store ids for later navigation
				\Session::getInstance()->set(NEWSPLUS_SESSION_NEWS_IDS, $objAllItems->fetchEach('id'));
			}
		}

		return NewsPlusModel::findPublishedByPidsAndIds($newsArchives, $arrFilterIds, $newsCategories, $blnFeatured, ($limit ?: 0), $offset, array(),  $this->startDate, $this->endDate);
	}

    protected function findNewsInSearchIndex($strKeywords,$blnOrSearch=false, $blnFuzzy=false)
    {
        // Reference page
        if ($this->rootPage > 0)
        {
            $arrPages = $this->Database->getChildRecords($this->rootPage, 'tl_page');
            array_unshift($arrPages, $this->rootPage);
        }
        // Website root
        else
        {
            global $objPage;
            $arrPages = $this->Database->getChildRecords($objPage->rootId, 'tl_page');
        }

		try
		{
            $objSearch = \Search::searchFor($strKeywords, $blnOrSearch, $arrPages, null, null, $blnFuzzy);
			if($objSearch->numRows > 0)
			{
				$arrUrls = $objSearch->fetchEach('url');
				$strKeyWordColumns = "";
				$n = 0;
				foreach($arrUrls as $i => $strAlias)
				{
					$strKeyWordColumns .= ($n > 0 ? " OR " : "") . "$this->t.alias = ?";
					$arrValues[] = basename($strAlias);
					$n++;
				}
				$arrColumns[] = "($strKeyWordColumns)";

				return \HeimrichHannot\NewsPlus\NewsPlusModel::findBy($arrColumns, $arrValues);
			}
			else
				return null;
		}
		catch (\Exception $e)
		{
			$this->log('Website search failed: ' . $e->getMessage(), __METHOD__, TL_ERROR);
			return null;
		}
    }

    /**
     * @param $obj
     * @return array
     */
    protected function getArchiveTitle($obj)
    {
        $str = $obj->__get('title');
        $str = explode(' ',trim($str));
        return $str;
    }

    public static function findArchiveTitleByPid($pid)
    {
        $archive = \NewsArchiveModel::findByPk($pid);
        return $archive->title;
    }

    public static function findArchiveByPid($pid)
    {
        return \NewsArchiveModel::findByPk($pid);
    }

    static function getArchiveClassFromTitle($title, $strToLower = false)
    {
        $type = standardize($title);
        $strBase = strstr($type, '-', true);
        $strNewTitle = ($strBase ? $strBase . ' ' : '') . $type;
        if($strToLower) $strNewTitle = strtolower($strNewTitle);
        return $strNewTitle;
    }

	protected function initFilter(\ModuleModel $objFilter)
	{
		$this->objFilter = new NewsFilterForm($objFilter);
		$this->objFilter->generate();
		$this->arrSubmission = $this->objFilter->getSubmission(false, true);

		if($this->arrSubmission === null)
		{
			return false;
		}

		// set news archives from filter
		if(is_array($this->arrSubmission['pid']) && !empty($this->arrSubmission['pid']))
		{
			$this->news_archives = $this->arrSubmission['pid'];
			$this->filterActive = true;
		}

		// set news categories from filter
		if(is_array($this->arrSubmission['cat']) && !empty($this->arrSubmission['cat']))
		{
			$this->news_categories = $this->arrSubmission['cat'];
			$GLOBALS['NEWS_FILTER_CATEGORIES'] = $this->news_categories;
			$this->filterActive = true;
		}

		// startDate
		if($this->arrSubmission['startDate'])
		{
			$this->startDate = strtotime($this->arrSubmission['startDate'] . ' 00:00:00');
			$this->filterActive = true;
		}

		// endDate
		if($this->arrSubmission['endDate'])
		{
			$this->endDate = strtotime($this->arrSubmission['endDate'] . ' 23:59:59');
			$this->filterActive = true;
		}

		// search query
		if($this->arrSubmission['q'])
		{
			$this->strKeywords = $this->arrSubmission['q'];
			$this->filterActive = true;
			$this->filterSearch = true;
		}
	}
}