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

    public function generateAjax()
    {
        return $this->id;
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

		$arrIds = array();

		// get items by tag tid
		if(in_array('tags', \ModuleLoader::getActive()))
		{
			$arrIds = NewsPlusTagHelper::getNewsIdByTableAndTag(\Input::get("tag"));
		}

		if($this->filterSearch)
		{
			$arrIds = array_merge($arrIds, $this->findNewsInSearchIndex(($limit ?: 0), $offset));
		}

		// Get the total number of items
		$intTotal = NewsPlusModel::countPublishedByPidsAndCategories($this->news_archives, $this->news_categories, $arrIds, $blnFeatured, array(), $this->startDate, $this->endDate);

		if ($intTotal < 1 && !$this->filterSearch)
		{
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

		$objArticles = NewsPlusModel::findPublishedByPidsAndCategories($this->news_archives, $this->news_categories, $arrIds, $blnFeatured, ($limit ?: 0), $offset, array(),  $this->startDate, $this->endDate);

        // store all events ids in session
        $arrUrlParam = array();
        if(\Input::get("newscategories")) $arrUrlParam[] = 'newscategories=' . \Input::get("newscategories");
        if(\Input::get("searchKeywords")) $arrUrlParam[] = 'searchKeywords=' . \Input::get("searchKeywords");
        if(\Input::get("startDate"))      $arrUrlParam[] = 'startDate=' . \Input::get("startDate");
        if(\Input::get("endDate"))        $arrUrlParam[] = 'endDate=' . \Input::get("endDate");

        $arrIds = NewsPlus::getAllPublishedNews($this->news_archives, $this->news_categories);

//        // show only news by tag
//        if(\Input::get("tag")) $arrUrlParam[] = 'tag=' . \Input::get("tag");
//
//        if(count($arrTagIds)) $arrIds = array_intersect($arrIds, $arrTagIds);

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

            // load specific pagination template if infiniteScroll is used
            // otherwise keep standard pagination
            $objT = $this->news_useInfiniteScroll ? new \FrontendTemplate('infinite_pagination') : null;

            if(!is_null($objT))$objT->triggerText = $this->news_changeTriggerText ? $this->news_triggerText : $GLOBALS['TL_LANG']['news_plus']['loadMore'];

			// Add the pagination menu
            $objPagination = new \Pagination($total, $this->perPage, \Config::get('maxPaginationLinks'), $id, $objT);

            $this->Template->pagination = $objPagination->generate("\n  ");
		}

        // Add the articles
        if ($objArticles !== null) {
            $this->Template->articles = $this->parseArticles($objArticles);
        }
        $this->Template->archives = $this->news_archives;
        // add triggerText for infiniteScroll

    }

    protected function findNewsInSearchIndex()
    {
		$arrIds = array();

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
            $objSearch = \Search::searchFor($this->strKeywords, $this->objFilter->news_filterSearchQueryType === false, $arrPages, 0, 0, $this->objFilter->news_filterFuzzySearch);
			
			if($objSearch->numRows > 0)
			{
				$arrUrls = array();

				while($objSearch->next())
				{
					$arrUrls[] = $objSearch->url;
				}

				$strKeyWordColumns = "";
				$n = 0;

				foreach($arrUrls as $i => $strAlias)
				{
					$strKeyWordColumns .= ($n > 0 ? " OR " : "") . "$this->t.alias = ?";
					$arrValues[] = basename($strAlias);
					$n++;
				}

				$arrColumns[] = "($strKeyWordColumns)";

				$objArticles = \HeimrichHannot\NewsPlus\NewsPlusModel::findBy($arrColumns, $arrValues);
				
				if($objArticles !== null)
				{
					$arrIds =  $objArticles->fetchEach('id');
				}
				
				return $arrIds;
			}
			else
				return $arrIds;
		}
		catch (\Exception $e)
		{
			$this->log('Website search failed: ' . $e->getMessage(), __METHOD__, TL_ERROR);
			return $arrIds;
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

