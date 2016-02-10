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

	protected $strKeywords;

	protected $t = "tl_news";

    protected $filterActive = false;

    protected $filterSearch = false;

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

        // set news categories from filter
        if(\Input::get('newscategories')){
            $this->news_archives = explode(',', \Input::get('newscategories'));
            $this->filterActive = true;
        }
    
        $this->news_archives = $this->sortOutProtected(deserialize($this->news_archives));

        // Return if there are no archives
        if (!is_array($this->news_archives) || empty($this->news_archives))
        {
            return '';
        }

		$GLOBALS['NEWS_FILTER_CATEGORIES'] = $this->news_filterCategories ? true : false;
		$GLOBALS['NEWS_FILTER_DEFAULT']    = deserialize($this->news_filterDefault, true);
		$GLOBALS['NEWS_FILTER_PRESERVE']   = $this->news_filterPreserve;

		if($this->news_filterModule)
		{
			$this->objFilter = \ModuleModel::findByPk($this->news_filterModule);
		}

		$this->news_categories = array();
		// set news categories from filter
		if(\Input::get('categories'))
        {
            $this->news_categories = explode(',', \Input::get('categories'));
            $this->filterActive = true;
        }

        // Show the event reader if an item has been selected
        if (!$this->news_showInModal && $this->news_readerModule > 0  && (isset($_GET['news']) || (\Config::get('useAutoItem') && isset($_GET['auto_item']))))
        {
            return $this->getFrontendModule($this->news_readerModule, $this->strColumn);
        }

        // filter
        if(\Input::get('startDate'))
        {
            $this->startDate = strtotime (\Input::get('startDate') . ' 00:00:00');
            $this->filterActive = true;
        }

        if(\Input::get('endDate'))
        {
            $this->endDate = strtotime(\Input::get('endDate') . ' 23:59:59');
            $this->filterActive = true;
        }

        if(\Input::get('searchKeywords'))
        {
            $this->strKeywords = trim(\Input::get('searchKeywords'));
            $this->filterActive = true;
            $this->filterSearch = true;
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

		if ($intTotal < 1 && !$this->filterSearch) {
			return;
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

		$arrTagIds = array();

		// get items by tag tid
		if(in_array('tags', \ModuleLoader::getActive()))
		{
			$arrTagIds = NewsPlusTagHelper::getNewsIdByTableAndTag(\Input::get("tag"));
		}


        // Get the items normal or by tag
        if(count($arrTagIds) > 0)
		{
            $objArticles = NewsPlusModel::findPublishedByIds($arrTagIds);
        }
		elseif (isset($limit) && !isset($objArticles))
		{
			if($this->filterSearch)
			{
				$objArticles = static::findNewsInSearchIndex(($limit ?: 0), $offset);
			}
			else
			{
				$objArticles = NewsPlusModel::findPublishedByPids($this->news_archives, $this->news_categories, $blnFeatured, ($limit ?: 0), $offset, array(),  $this->startDate, $this->endDate);
			}
        }
		else
		{
            $objArticles = NewsPlusModel::findPublishedByPids($this->news_archives, $this->news_categories, $blnFeatured, 0, $offset, array(), $this->startDate, $this->endDate);
        }

        // store all events ids in session
        $arrUrlParam = array();
        if(\Input::get("newscategories")) $arrUrlParam[] = 'newscategories=' . \Input::get("newscategories");
        if(\Input::get("searchKeywords")) $arrUrlParam[] = 'searchKeywords=' . \Input::get("searchKeywords");
        if(\Input::get("startDate"))      $arrUrlParam[] = 'startDate=' . \Input::get("startDate");
        if(\Input::get("endDate"))        $arrUrlParam[] = 'endDate=' . \Input::get("endDate");

        $arrIds = NewsPlus::getAllPublishedNews($this->news_archives, $this->news_categories);

        // show only news by tag
        if(\Input::get("tag")) $arrUrlParam[] = 'tag=' . \Input::get("tag");

        if(count($arrTagIds)) $arrIds = array_intersect($arrIds, $arrTagIds);

        $session = \Session::getInstance()->getData();
        $session[NEWSPLUS_SESSION_NEWS_IDS] = array();
        $session[NEWSPLUS_SESSION_NEWS_IDS] = $arrIds;
        \Session::getInstance()->setData($session);


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
        if ($objArticles !== null) {
            $this->Template->articles = $this->parseArticles($objArticles);
        }
        $this->Template->archives = $this->news_archives;
    }

    protected function findNewsInSearchIndex($intLimit=0, $intOffset=0)
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
			$strQueryType = !$this->objFilter->news_filterSearchQueryType;
			
            $objSearch = \Search::searchFor($this->strKeywords, $strQueryType, $arrPages, 0, 0, $this->objFilter->news_filterFuzzySearch);
			
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
				$arrColumns[] = "($this->t.pid IN (" . implode(',', $this->news_archives) . "))";

				// filter by date
				if($this->startDate)
					$arrColumns[] = "($this->t.date>=$this->startDate)";
				if($this->endDate)
					$arrColumns[] = "($this->t.date<=$this->endDate)";
				
				if (!BE_USER_LOGGED_IN)
				{
					$time         = time();
					$arrColumns[] = "($this->t.start='' OR $this->t.start<$time) AND ($this->t.stop='' OR $this->t.stop>$time) AND $this->t.published=1";
				}

				$arrOptions['limit']  = $intLimit;
				$arrOptions['offset'] = $intOffset;
				
				return \HeimrichHannot\NewsPlus\NewsPlusModel::findBy($arrColumns, $arrValues, $arrOptions);
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
}

