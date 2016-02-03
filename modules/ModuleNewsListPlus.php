<?php

namespace HeimrichHannot\NewsPlus;

class ModuleNewsListPlus extends ModuleNewsPlus
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_newslist_plus';

	/** @var NewsFilterRegistry  */
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

		$this->news_archives = $this->sortOutProtected(deserialize($this->news_archives));

		// Return if there are no archives
		if (!is_array($this->news_archives) || empty($this->news_archives))
		{
			return '';
		}

		$this->objFilter = NewsFilterRegistry::getInstance($this->arrData);

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
		$intTotal = $this->countItems($blnFeatured);

		if ($intTotal < 1)
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

		$objArticles = $this->fetchItems($blnFeatured, ($limit ?: 0), $offset);

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
	 * Count the total matching items
	 *
	 * @param boolean $blnFeatured
	 *
	 * @return integer
	 */
	protected function countItems($blnFeatured)
	{
		// HOOK: add custom logic
		if (isset($GLOBALS['TL_HOOKS']['newsListPlusCountItems']) && is_array($GLOBALS['TL_HOOKS']['newsListPlusCountItems']))
		{
			foreach ($GLOBALS['TL_HOOKS']['newsListPlusCountItems'] as $callback)
			{
				if (($intResult = \System::importStatic($callback[0])->{$callback[1]}($this->objFilter, $blnFeatured, $this)) === false)
				{
					continue;
				}

				if (is_int($intResult))
				{
					return $intResult;
				}
			}
		}

		return NewsPlusModel::countPublishedByFilter($this->objFilter, $blnFeatured);
	}

	/**
	 * Fetch the matching items
	 *
	 * @param  boolean $blnFeatured
	 * @param  integer $limit
	 * @param  integer $offset
	 *
	 * @return \Model\Collection|\NewsModel|null
	 */
	protected function fetchItems($blnFeatured, $limit, $offset)
	{
		// HOOK: add custom logic
		if (isset($GLOBALS['TL_HOOKS']['newsListPlusFetchItems']) && is_array($GLOBALS['TL_HOOKS']['newsListPlusFetchItems']))
		{
			foreach ($GLOBALS['TL_HOOKS']['newsListPlusFetchItems'] as $callback)
			{
				if (($objCollection = \System::importStatic($callback[0])->{$callback[1]}($this->objFilter, $blnFeatured, $limit, $offset, $this)) === false)
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
			$objAllItems = NewsPlusModel::findPublishedByFilter($this->objFilter, $blnFeatured, 0, 0, array());

			if($objAllItems !== null)
			{
				// store ids for later navigation
				\Session::getInstance()->set(NEWSPLUS_SESSION_NEWS_IDS, $objAllItems->fetchEach('id'));
			}
		}

		return NewsPlusModel::findPublishedByFilter($this->objFilter, $blnFeatured, ($limit ?: 0), $offset, array());
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