<?php

namespace HeimrichHannot\NewsPlus;

use HeimrichHannot\Request\Request;
use NewsCategories\NewsCategories;
use NewsCategories\NewsCategoryModel;

class ModuleNewsListPlus extends ModuleNewsPlus
{
    /**
     * Template
     *
     * @var string
     */
    protected $strTemplate = 'mod_newslist_plus';

    /** @var NewsFilterRegistry */
    protected $objFilter = null;

    protected $arrSubmission = [];

    protected $strKeywords;

    protected $t = "tl_news";

    protected $filterActive = false;

    protected $filterSearch = false;

    protected $news_categories = [];

    /**
     * Display a wildcard in the back end
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['newslist_plus'][0]) . ' ###';
            $objTemplate->title    = $this->headline;
            $objTemplate->id       = $this->id;
            $objTemplate->link     = $this->name;
            $objTemplate->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        $this->news_archives = $this->sortOutProtected(deserialize($this->news_archives));

        // Return if there are no archives
        if (!is_array($this->news_archives) || empty($this->news_archives))
        {
            return '';
        }

        // support news category filter
        $strCategory       = \Input::get(NewsCategories::getParameterName());
        $blnCategoryFilter = false;

        if ($strCategory)
        {
            if (($objCategory = NewsCategoryModel::findPublishedByIdOrAlias($strCategory)) !== null)
            {
                $this->arrData['news_categories'] = [$objCategory->id];
                $blnCategoryFilter                = true;
            }
        }

        $this->objFilter = NewsFilterRegistry::getInstance($this->arrData);

        if ($blnCategoryFilter)
        {
            $this->objFilter->addField('cat');
        }

        $this->activeNews = $this->getActiveNews();

        return parent::generate();
    }

    /**
     * Generate the module
     */
    protected function compile()
    {
        $offset = intval($this->skipFirst);
        $limit  = null;

        // Maximum number of items
        if ($this->numberOfItems > 0)
        {
            $limit = $this->numberOfItems;
        }

        // Handle featured news
        if ($this->news_featured == 'featured')
        {
            $blnFeatured = true;
        }
        elseif ($this->news_featured == 'unfeatured')
        {
            $blnFeatured = false;
        }
        else
        {
            $blnFeatured = null;
        }

        // show all news items, filter is active - TODO: make configurable in tl_module
        if ($this->filterActive)
        {
            $blnFeatured = null;
        }

        $this->Template->articles = [];

        $strEmpty = $GLOBALS['TL_LANG']['MSC']['emptyList'];

        if ($this->news_empty_overwrite && ($strLabel = $GLOBALS['TL_LANG']['MSC']['news_plus']['emptyNewsList'][$this->news_empty_label]) != '')
        {
            $strEmpty = $strLabel;
        }

        $this->Template->empty = $strEmpty;

        // Get the total number of items
        $intTotal = $this->countItems($blnFeatured);

        if ($intTotal < 1)
        {
            return;
        }

        $total = $intTotal - $offset;

        // Adjust the overall limit
        if (isset($limit))
        {
            $total = min($limit, $total);
        }

        // Split the results
        if ($this->perPage > 0 && (!isset($limit) || $this->numberOfItems > $this->perPage))
        {
            // Get the current page
            $id   = 'page_n' . $this->id;
            $page = \Input::get($id) ?: 1;

            //Set limit and offset
            $limit = $this->perPage;
            $offset += (max($page, 1) - 1) * $this->perPage;
            $skip = intval($this->skipFirst);

            // Overall limit
            if ($offset + $limit > $total + $skip)
            {
                $limit = $total + $skip - $offset;
            }
        }

        $objArticles = $this->fetchItems($blnFeatured, ($limit ?: 0), $offset);

        // Split the results
        if ($limit > 0 && $limit < $total)
        {
            // Do not index or cache the page if the page number is outside the range
            if ($page < 1 || $page > max(ceil($total / $this->perPage), 1))
            {
                global $objPage;
                $objPage->noSearch = 1;
                $objPage->cache    = 0;

                // Send a 404 header
                header('HTTP/1.1 404 Not Found');

                return;
            }

            $objPaginationTemplate = null;

            if ($this->news_useInfiniteScroll)
            {
                $objPaginationTemplate              = new \FrontendTemplate('infinite_pagination');
                $objPaginationTemplate->triggerText = $this->news_changeTriggerText ? $this->news_triggerText : $GLOBALS['TL_LANG']['news_plus']['loadMore'];
            }

            // custom pagination template
            if ($this->news_pagination_overwrite && $this->pagination_template != '')
            {
                $objPaginationTemplate = new \FrontendTemplate($this->pagination_template);
            }

            // Add the pagination menu
            $objPagination = new NewsPagination($total, $this->perPage, \Config::get('maxPaginationLinks'), $id, $objPaginationTemplate);

            if ($this->pagination_hash != '')
            {
                $objPagination->setLinkHash($this->pagination_hash);
            }

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

        // store all news item ids in the session if reader module is set
        if ($this->objFilter !== null && $this->news_readerModule > 0 && ($objReader = \ModuleModel::findByPk($this->news_readerModule)) !== null)
        {
            $objAllItems = NewsPlusModel::findPublishedByFilter($this->objFilter, $blnFeatured, 0, 0, []);

            if ($objAllItems !== null)
            {
                // store ids for later navigation
                \Session::getInstance()->set(NewsPlusHelper::getKeyForSessionNewsIds($objReader), $objAllItems->fetchEach('id'));
            }
        }

        return NewsPlusModel::findPublishedByFilter($this->objFilter, $blnFeatured, ($limit ?: 0), $offset, []);
    }

    /**
     * Get the active item if news reader is set
     *
     * @return The NewsPlusModel or null if none is set
     */
    protected function getActiveNews()
    {
        // Set the item from the auto_item parameter
        if (!isset($_GET['items']) && \Config::get('useAutoItem') && isset($_GET['auto_item']))
        {
            \Input::setGet('items', \Input::get('auto_item'));
        }

        // Do not index or cache the page if no news item has been specified
        if (!\Input::get('items'))
        {
            return null;
        }

        if ($this->news_showInModal || $this->news_readerModule < 1 || ($objModule = \ModuleModel::findByPk($this->news_readerModule)) === null)
        {
            return null;
        }

        $arrArchives = $this->sortOutProtected(deserialize($objModule->news_archives));

        if (!is_array($arrArchives) || empty($arrArchives))
        {
            return null;
        }

        // Get the news item
        $objNews = NewsPlusModel::findPublishedByParentAndIdOrAlias(\Input::get('items'), $arrArchives);

        if ($objNews === null)
        {
            return null;
        }

        return $objNews;
    }

    /**
     * @param $obj
     *
     * @return array
     */
    protected function getArchiveTitle($obj)
    {
        $str = $obj->__get('title');
        $str = explode(' ', trim($str));

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
        $type        = standardize($title);
        $strBase     = strstr($type, '-', true);
        $strNewTitle = ($strBase ? $strBase . ' ' : '') . $type;
        if ($strToLower)
        {
            $strNewTitle = strtolower($strNewTitle);
        }

        return $strNewTitle;
    }
}