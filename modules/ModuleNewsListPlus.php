<?php

namespace HeimrichHannot\NewsPlus;

use HeimrichHannot\CalendarPlus\EventsPlusHelper;

class ModuleNewsListPlus extends ModuleNewsPlus
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_newslist_plus';

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
        if(isset($_GET['newscategories']) && $_GET['newscategories'] != '') $this->news_archives = explode(',', \Input::get('newscategories'));
        $this->news_archives = $this->sortOutProtected(deserialize($this->news_archives));

        // Return if there are no archives
        if (!is_array($this->news_archives) || empty($this->news_archives))
        {
            return '';
        }

        // Show the event reader if an item has been selected
        if (!$this->news_showInModal && $this->news_readerModule > 0  && (isset($_GET['news']) || (\Config::get('useAutoItem') && isset($_GET['auto_item']))))
        {
            return $this->getFrontendModule($this->news_readerModule, $this->strColumn);
        }

        $blnFuzzy = $GLOBALS['NEWS_FILTER_FUZZY_SEARCH'];
        $strQueryType = $GLOBALS['NEWS_FILTER_SEARCH_QUERY_TYPE'];
        $strKeywords = trim(\Input::get('searchKeywords'));

        // Get news from search index if there are keywords
        if ($GLOBALS['NEWS_FILTER_SHOW_SEARCH'] && $GLOBALS['NEWS_FILTER_USE_SEARCH_INDEX'] && \Input::get('searchKeywords') != '' && \Input::get('searchKeywords') != '*' && !$this->jumpTo) {
            return self::findNewsInSearchIndex($strKeywords, $strQueryType, $blnFuzzy);
        } else {
            return parent::generate();
        }
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


        $this->Template->articles = array();
        $this->Template->empty = $GLOBALS['TL_LANG']['MSC']['emptyList'];

        // Get the total number of items
        $intTotal = NewsPlusModel::countPublishedByPids($this->news_archives, $blnFeatured);

        if ($intTotal < 1) {
            return;
        }

        $total = $intTotal - $offset;

        // Split the results
        if ($this->perPage > 0 && (!isset($limit) || $this->numberOfItems > $this->perPage)) {
            // Adjust the overall limit
            if (isset($limit)) {
                $total = min($limit, $total);
            }

            // Get the current page
            $id = 'page_n' . $this->id;
            $page = \Input::get($id) ?: 1;

            // Do not index or cache the page if the page number is outside the range
            if ($page < 1 || $page > max(ceil($total / $this->perPage), 1)) {
                global $objPage;
                $objPage->noSearch = 1;
                $objPage->cache = 0;

                // Send a 404 header
                header('HTTP/1.1 404 Not Found');
                return;
            }

            // Set limit and offset
            $limit = $this->perPage;
            $offset += (max($page, 1) - 1) * $this->perPage;
            $skip = intval($this->skipFirst);

            // Overall limit
            if ($offset + $limit > $total + $skip) {
                $limit = $total + $skip - $offset;
            }

            // Add the pagination menu
            $objPagination = new \Pagination($total, $this->perPage, \Config::get('maxPaginationLinks'), $id);
            $this->Template->pagination = $objPagination->generate("\n  ");
        }

        // Get the items
        if (isset($limit)) {
            $objArticles = NewsPlusModel::findPublishedByPids($this->news_archives, $blnFeatured, $limit, $offset);
        } else {
            $objArticles = NewsPlusModel::findPublishedByPids($this->news_archives, $blnFeatured, 0, $offset);
        }

        // Add the articles
        if ($objArticles !== null) {
            $this->Template->articles = $this->parseArticles($objArticles);
        }
        $this->Template->archives = $this->news_archives;
    }

    protected function findNewsInSearchIndex($strKeywords,$strQueryType, $blnFuzzy)
    {
        // Reference page
        if ($this->rootPage > 0)
        {
            $intRootId = $this->rootPage;
            $arrPages = $this->Database->getChildRecords($this->rootPage, 'tl_page');
            array_unshift($arrPages, $this->rootPage);
        }
        // Website root
        else
        {
            global $objPage;
            $intRootId = $objPage->rootId;
            $arrPages = $this->Database->getChildRecords($objPage->rootId, 'tl_page');
        }

        $arrResult = null;
        $strChecksum = md5($strKeywords . $strQueryType . $intRootId . $blnFuzzy);
        $query_starttime = microtime(true);
        $strCacheFile = 'system/cache/search/' . $strChecksum . '.json';

        // Load the cached result
        if (file_exists(TL_ROOT . '/' . $strCacheFile))
        {
            $objFile = new \File($strCacheFile, true);

            if ($objFile->mtime > time() - 1800)
            {
                $arrResult = json_decode($objFile->getContent(), true);
            }
            else
            {
                $objFile->delete();
            }
        }

        // Cache the result
        if ($arrResult === null)
        {
            try
            {
                $objSearch = \Search::searchFor($strKeywords, $strQueryType, $arrPages, $limit, $offset, $blnFuzzy);
                $arrResult = $objSearch->fetchAllAssoc();
            }
            catch (\Exception $e)
            {
                $this->log('Website search failed: ' . $e->getMessage(), __METHOD__, TL_ERROR);
                $arrResult = array();
            }

            \File::putContent($strCacheFile, json_encode($arrResult));
        }

        $query_endtime = microtime(true);

        // Sort out protected pages
        if (\Config::get('indexProtected') && !BE_USER_LOGGED_IN)
        {
            $this->import('FrontendUser', 'User');

            foreach ($arrResult as $k=>$v)
            {
                if ($v['protected'])
                {
                    if (!FE_USER_LOGGED_IN)
                    {
                        unset($arrResult[$k]);
                    }
                    else
                    {
                        $groups = deserialize($v['groups']);

                        if (!is_array($groups) || empty($groups) || !count(array_intersect($groups, $this->User->groups)))
                        {
                            unset($arrResult[$k]);
                        }
                    }
                }
            }

            $arrResult = array_values($arrResult);
        }

        $count = count($arrResult);

        // No results
        if ($count < 1)
        {
            $this->Template->header = sprintf($GLOBALS['TL_LANG']['MSC']['sEmpty'], $strKeywords);
            $this->Template->duration = substr($query_endtime-$query_starttime, 0, 6) . ' ' . $GLOBALS['TL_LANG']['MSC']['seconds'];
            return;
        }

        $from = 1;
        $to = $count;

        // Pagination
        if ($this->perPage > 0)
        {
            $id = 'page_s' . $this->id;
            $page = \Input::get($id) ?: 1;
            $per_page = \Input::get('per_page') ?: $this->perPage;

            // Do not index or cache the page if the page number is outside the range
            if ($page < 1 || $page > max(ceil($count/$per_page), 1))
            {
                global $objPage;
                $objPage->noSearch = 1;
                $objPage->cache = 0;

                // Send a 404 header
                header('HTTP/1.1 404 Not Found');
                return;
            }

            $from = (($page - 1) * $per_page) + 1;
            $to = (($from + $per_page) > $count) ? $count : ($from + $per_page - 1);

            // Pagination menu
            if ($to < $count || $from > 1)
            {
                $objPagination = new \Pagination($count, $per_page, \Config::get('maxPaginationLinks'), $id);
                $this->Template->pagination = $objPagination->generate("\n  ");
            }
        }

        // Get the results
        for ($i=($from-1); $i<$to && $i<$count; $i++)
        {
            $objTemplate = new \FrontendTemplate($this->searchTpl ?: 'news_short_plus');
            $objTemplate->headline = $arrResult[$i]['title'];
            $objTemplate->teaser = $arrResult[$i]['text'];
            $objTemplate->link = $arrResult[$i]['url'];
            $objTemplate->hasMetaFields = true;
            $objTemplate->date = date('d.m.Y', $arrResult[$i]['tstamp']);
            $objTemplate->timestamp = $arrResult[$i]['tstamp'];
            $objTemplate->datetime = date('Y-m-d\TH:i:sP', $arrResult[$i]['tstamp']);;

            // archive data
            $objArchive = self::findArchiveByPid($arrResult[$i]['pid']);
            $objArchive->title = self::getArchiveClassFromTitle($objArchive->title);
            $objArchive->class = self::getArchiveClassFromTitle($objArchive->title, true);
            $objTemplate->archive = $objArchive;

            // Modal
            if($this->news_showInModal && $this->news_readerModule)
            {
                $objTemplate->modal = true;
                $objTemplate->modalTarget = '#' . EventsPlusHelper::getCSSModalID($this->news_readerModule);
            }

            $arrNews[] = $objTemplate->parse();
        }

        $objTemplate = new \FrontendTemplate('mod_newslist_plus');
        $objTemplate->class = "mod_newslist_plus row";
        $objTemplate->articles = $arrNews;
        return $objTemplate->parse();
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

    protected function getArchiveClassFromTitle($title, $strToLower = false)
    {
        $type = explode(' ',trim($title));
        $subject = explode(' - ',trim($title));
        $strNewTitle = $type[0] . ' ' . $subject[1];
        if($strToLower) $strNewTitle = strtolower($strNewTitle);
        return $strNewTitle;
    }
}