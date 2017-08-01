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

use HeimrichHannot\Haste\Database\QueryHelper;


/**
 * Class NewsPlusModel
 *
 * @package HeimrichHannot\NewsPlus
 */
class NewsPlusModel extends \NewsModel
{

    /**
     * Table name
     *
     * @var string
     */
    protected static $strTable = 'tl_news';

    /**
     * Get the categories cache and return it as array
     *
     * @return array
     */
    public static function getCategoriesCache()
    {
        static $arrCache;

        if (!is_array($arrCache))
        {
            $arrCache      = [];
            $objCategories = \Database::getInstance()->execute("SELECT * FROM tl_news_categories");
            $arrCategories = [];

            while ($objCategories->next())
            {
                // Include the parent IDs of each category
                if (!isset($arrCategories[$objCategories->category_id]))
                {
                    $arrCategories[$objCategories->category_id] = \Database::getInstance()->getParentRecords($objCategories->category_id, 'tl_news_category');
                }

                foreach ($arrCategories[$objCategories->category_id] as $intParentCategory)
                {
                    $arrCache[$intParentCategory][] = $objCategories->news_id;
                }
            }
        }

        return $arrCache;
    }

    /**
     * Filter the news by categories
     *
     * @param array
     *
     * @return array
     */
    protected static function filterByCategories($arrColumns)
    {
        $t = static::$strTable;

        // Use the default filter
        if (is_array($GLOBALS['NEWS_FILTER_DEFAULT']) && !empty($GLOBALS['NEWS_FILTER_DEFAULT']))
        {
            $arrCategories = static::getCategoriesCache();

            if (!empty($arrCategories))
            {
                $arrIds = [];

                // Get the news IDs for particular categories
                foreach ($GLOBALS['NEWS_FILTER_DEFAULT'] as $category)
                {
                    if (isset($arrCategories[$category]))
                    {
                        $arrIds = array_merge($arrCategories[$category], $arrIds);
                    }
                }

                $strKey = 'category';

                // Preserve the default category
                if ($GLOBALS['NEWS_FILTER_PRESERVE'])
                {
                    $strKey = 'category_default';
                }

                $arrColumns[$strKey] = "$t.id IN (" . implode(',', (empty($arrIds) ? [0] : array_unique($arrIds))) . ")";
            }
        }

        // Try to find by category
        if ($GLOBALS['NEWS_FILTER_CATEGORIES'] && \Input::get('category'))
        {
            $strClass    = \NewsCategories\NewsCategories::getModelClass();
            $objCategory = $strClass::findPublishedByIdOrAlias(\Input::get('category'));


            if ($objCategory === null)
            {
                return null;
            }

            $arrCategories          = static::getCategoriesCache();
            $arrColumns['category'] = "$t.id IN (" . implode(',', (empty($arrCategories[$objCategory->id]) ? [0] : $arrCategories[$objCategory->id])) . ")";
        }

        return $arrColumns;
    }


    /**
     * Find published news items by their parent ID and ID or alias
     *
     * @param mixed $varId      The numeric ID or alias name
     * @param array $arrPids    An array of parent IDs
     * @param array $arrOptions An optional options array
     *
     * @return \Model|null The NewsModel or null if there are no news
     */
    public static function findPublishedByParentAndIdOrAlias($varId, $arrPids, array $arrOptions = [])
    {
        if (!is_array($arrPids) || empty($arrPids))
        {
            return null;
        }

        $t          = static::$strTable;
        $arrColumns = ["($t.id=? OR $t.alias=?) AND $t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")"];

        if (!BE_USER_LOGGED_IN)
        {
            $time         = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        return static::findBy($arrColumns, [(is_numeric($varId) ? $varId : 0), $varId], $arrOptions);
    }


    /**
     * Find published news items by their parent ID, news categories and ids
     *
     * @param array     $arrPids       An array of news archive IDs
     * @param bool|null $arrCategories An array of news category IDs
     * @param array     $arrIds        An array of news IDs
     * @param boolean   $blnFeatured   If true, return only featured news, if false, return only unfeatured news
     * @param integer   $intLimit      An optional limit
     * @param integer   $intOffset     An optional offset
     * @param array     $arrOptions    An optional options array
     * @param null      $startDate     An optional startDate
     * @param null      $endDate       An optional endDate
     *
     * @return \Model\Collection|null A collection of models or null if there are no news
     */
    public static function findPublishedByPidsAndCategories(
        $arrPids,
        $arrCategories,
        array $arrIds = [],
        $blnFeatured = null,
        $intLimit = 0,
        $intOffset = 0,
        array $arrOptions = [],
        $startDate = null,
        $endDate = null
    ) {
        if (!is_array($arrPids) || empty($arrPids))
        {
            return null;
        }

        $t          = static::$strTable;
        $arrColumns = ["$t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")"];

        if (!empty($arrIds))
        {
            $arrColumns[] = "$t.id IN(" . implode(',', array_map('intval', $arrIds)) . ")";
        }

        if ($blnFeatured === true)
        {
            $arrColumns[] = "$t.featured=1";
        }
        elseif ($blnFeatured === false)
        {
            $arrColumns[] = "$t.featured=''";
        }

        // filter by date
        if ($startDate != null)
        {
            $arrColumns[] = "$t.date>=$startDate";
        }
        if ($endDate != null)
        {
            $arrColumns[] = "$t.date<=$endDate";
        }

        // Never return unpublished elements in the back end, so they don't end up in the RSS feed
        if (!BE_USER_LOGGED_IN || TL_MODE == 'BE')
        {
            $time         = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        // Filter by categories
        $arrColumns = static::filterByCategories($arrColumns);

        if (!isset($arrOptions['order']))
        {
            $arrOptions['order'] = "$t.date DESC";
        }

        $arrOptions['limit']  = $intLimit;
        $arrOptions['offset'] = $intOffset;

        return static::findBy($arrColumns, null, $arrOptions);
    }

    /**
     * Count published news items by their parent ID and categories
     *
     * @param array     $arrPids       An array of news archive IDs
     * @param bool|null $arrCategories An array of news category IDs
     * @param array     $arrIds        An array of news IDs
     * @param boolean   $blnFeatured   If true, return only featured news, if false, return only unfeatured news
     * @param array     $arrOptions    An optional options array
     * @param null      $startDate     An optional startDate
     * @param null      $endDate       An optional endDate
     *
     * @return integer The number of news items
     */
    public static function countPublishedByPidsAndCategories(
        $arrPids,
        $arrCategories,
        array $arrIds = [],
        $blnFeatured = null,
        array $arrOptions = [],
        $startDate = null,
        $endDate = null
    ) {
        if (!is_array($arrPids) || empty($arrPids))
        {
            return 0;
        }

        $t          = static::$strTable;
        $arrColumns = ["$t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")"];

        if (!empty($arrIds))
        {
            $arrColumns[] = "$t.id IN(" . implode(',', array_map('intval', $arrIds)) . ")";
        }

        if ($blnFeatured === true)
        {
            $arrColumns[] = "$t.featured=1";
        }
        elseif ($blnFeatured === false)
        {
            $arrColumns[] = "$t.featured=''";
        }

        // filter by date
        if ($startDate != null)
        {
            $arrColumns[] = "$t.date>=$startDate";
        }
        if ($endDate != null)
        {
            $arrColumns[] = "$t.date<=$endDate";
        }

        if (!BE_USER_LOGGED_IN)
        {
            $time         = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        // Filter by categories
        $arrColumns = static::filterByCategories($arrColumns);

        return static::countBy($arrColumns, null, $arrOptions);
    }


    /**
     * Find published news items with the default redirect target by their parent ID
     *
     * @param integer $intPid     The news archive ID
     * @param array   $arrOptions An optional options array
     *
     * @return \Model\Collection|null A collection of models or null if there are no news
     */
    public static function findPublishedDefaultByPid($intPid, array $arrOptions = [])
    {
        $t          = static::$strTable;
        $arrColumns = ["$t.pid=? AND $t.source='default'"];

        if (!BE_USER_LOGGED_IN)
        {
            $time         = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        if (!isset($arrOptions['order']))
        {
            $arrOptions['order'] = "$t.date DESC";
        }

        return static::findBy($arrColumns, $intPid, $arrOptions);
    }

    /**
     * Find published news by given ids
     *
     * @param array $arrPids An array of news archive IDs
     *
     * @return \Model\Collection|null A collection of models or null if there are no news
     */
    public static function findPublishedByIds($arrPids, array $arrOptions = [])
    {
        if (!is_array($arrPids) || empty($arrPids))
        {
            return null;
        }

        $t          = static::$strTable;
        $arrColumns = ["$t.id IN(" . implode(',', array_map('intval', $arrPids)) . ")"];

        if ($blnFeatured === true)
        {
            $arrColumns[] = "$t.featured=1";
        }
        elseif ($blnFeatured === false)
        {
            $arrColumns[] = "$t.featured=''";
        }

        // Never return unpublished elements in the back end, so they don't end up in the RSS feed
        if (!BE_USER_LOGGED_IN || TL_MODE == 'BE')
        {
            $time         = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        // Filter by search
        $arrColumns = static::findPublishedByHeadlineOrTeaser($arrColumns);

        if (!isset($arrOptions['order']))
        {
            $arrOptions['order'] = "$t.date DESC";
        }

        $arrOptions['limit']  = $intLimit;
        $arrOptions['offset'] = $intOffset;

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
    public static function findPublishedByPid($intId, $intLimit = 0, array $arrOptions = [])
    {
        $time = time();
        $t    = static::$strTable;

        $arrColumns = ["$t.pid=? AND ($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1"];

        if (!isset($arrOptions['order']))
        {
            $arrOptions['order'] = "$t.date DESC";
        }

        if ($intLimit > 0)
        {
            $arrOptions['limit'] = $intLimit;
        }

        return static::findBy($arrColumns, $intId, $arrOptions);
    }


    /**
     * Find all published news items of a certain period of time by their parent ID
     *
     * @param integer $intFrom    The start date as Unix timestamp
     * @param integer $intTo      The end date as Unix timestamp
     * @param array   $arrPids    An array of news archive IDs
     * @param integer $intLimit   An optional limit
     * @param integer $intOffset  An optional offset
     * @param array   $arrOptions An optional options array
     *
     * @return \Model\Collection|null A collection of models or null if there are no news
     */
    public static function findPublishedFromToByPids($intFrom, $intTo, $arrPids, $intLimit = 0, $intOffset = 0, array $arrOptions = [])
    {
        if (!is_array($arrPids) || empty($arrPids))
        {
            return null;
        }

        $t          = static::$strTable;
        $arrColumns = ["$t.date>=? AND $t.date<=? AND $t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")"];

        if (!BE_USER_LOGGED_IN)
        {
            $time         = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        if (!isset($arrOptions['order']))
        {
            $arrOptions['order'] = "$t.date DESC";
        }

        $arrOptions['limit']  = $intLimit;
        $arrOptions['offset'] = $intOffset;

        return static::findBy($arrColumns, [$intFrom, $intTo], $arrOptions);
    }


    /**
     * Count all published news items of a certain period of time by their parent ID
     *
     * @param integer $intFrom    The start date as Unix timestamp
     * @param integer $intTo      The end date as Unix timestamp
     * @param array   $arrPids    An array of news archive IDs
     * @param array   $arrOptions An optional options array
     *
     * @return integer The number of news items
     */
    public static function countPublishedFromToByPids($intFrom, $intTo, $arrPids, array $arrOptions = [])
    {
        if (!is_array($arrPids) || empty($arrPids))
        {
            return null;
        }

        $t          = static::$strTable;
        $arrColumns = ["$t.date>=? AND $t.date<=? AND $t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")"];

        if (!BE_USER_LOGGED_IN)
        {
            $time         = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        return static::countBy($arrColumns, [$intFrom, $intTo], $arrOptions);
    }

    /**
     * Find published news in search index
     *
     * @param array
     *
     * @return array
     */
    public static function findPublishedInSearchIndexByString($keywords, $limit = '', $offset = 0)
    {
        // $objSearch = \Search::searchFor($strKeywords, ($strQueryType == 'or'), $arrPages, 0, 0, $blnFuzzy);


        $t = static::$strTable;
        // $arrColumns = array("($t.id IN(" . implode(',', array_map('intval', $arrIds)) . ")");

        if (!BE_USER_LOGGED_IN)
        {
            $time         = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        $objSearch = \Search::searchFor($keywords, 'or', '', $limit, $offset, true);
        $arrIds    = static::getIdsOfNewsItemsFromSearchObject($objSearch);
        // $arrIds = array('vgt-03-15');
        //return static::findBy('alias', $arrIds);

        $objArticles = static::findPublishedNewsByIds($arrIds);

        return $objArticles;
    }

    protected static function getIdsOfNewsItemsFromSearchObject($objSearch)
    {

        foreach ($objSearch->fetchAllAssoc() as $news)
        {
            $news['archive_title'] = ModuleNewsListPlus::findArchiveTitleByPid($news['pid']);
            $arrNews[]             = $news;
        }

        return $arrNews;
    }

    /**
     * Filter the news by headline or
     *
     * @param array
     *
     * @return array
     */
    protected static function findPublishedByHeadlineOrTeaser($arrColumns)
    {
        $t = static::$strTable;

        // Try to find by given keywords
        if ($GLOBALS['NEWS_FILTER_SHOW_SEARCH'] && \Input::get('searchKeywords'))
        {

            $arrKeywords = explode(" ", trim(\Input::get('searchKeywords')));
            $arrClauses  = [];
            foreach ($arrKeywords as $keyword)
            {
                $arrClauses[] = "($t.headline LIKE '%%" . $keyword . "%%' OR $t.teaser LIKE '%%" . $keyword . "%%')";
            }
            $arrColumns[] = "(" . implode(' OR ', $arrClauses) . ")";
        }

        return $arrColumns;
    }


    /**
     * Find published news by ids
     *D
     *
     * @param mixed $varId      The numeric ID or alias name
     * @param array $arrOptions An optional options array
     *
     * @return \Model|null The model or null if there is no event
     */
    public static function findPublishedNewsByIds($arrIds, array $arrOptions = [])
    {
        $t          = static::$strTable;
        $arrColumns = ["($t.id IN(" . implode(',', array_map('intval', $arrIds)) . ")"];

        if (!BE_USER_LOGGED_IN)
        {
            $time         = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        return static::findBy('alias', $arrIds);
    }

    public function getAllNewsByPids($arrPids, $blnFeatured = null, $intLimit = 0, $intOffset = 0, array $arrOptions = [])
    {
        if (!is_array($arrPids) || empty($arrPids))
        {
            return null;
        }

        $t          = static::$strTable;
        $arrColumns = ["$t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")"];


        if ($blnFeatured === true)
        {
            $arrColumns[] = "$t.featured=1";
        }
        elseif ($blnFeatured === false)
        {
            $arrColumns[] = "$t.featured=''";
        }

        // Never return unpublished elements in the back end, so they don't end up in the RSS feed
        if (!BE_USER_LOGGED_IN || TL_MODE == 'BE')
        {
            $time         = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        // Filter by search
        $arrColumns = static::findPublishedByHeadlineOrTeaser($arrColumns);

        if (!isset($arrOptions['order']))
        {
            $arrOptions['order'] = "$t.date DESC";
        }

        $arrOptions['limit']  = $intLimit;
        $arrOptions['offset'] = $intOffset;

        return static::findBy($arrColumns, null, $arrOptions);
    }

    /**
     * Find the latest published news item by their parent ID
     *
     * @param array $arrPids    An array of news archive IDs
     * @param array $arrOptions An optional options array
     *
     * @return \NewsModel|null A news models or null if there are no news
     */
    public static function findLatestPublishedByPids($arrPids, array $arrOptions = [])
    {
        if (!is_array($arrPids) || empty($arrPids))
        {
            return null;
        }

        $t          = static::$strTable;
        $arrColumns = ["$t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")"];

        if (!BE_USER_LOGGED_IN)
        {
            $time         = time();
            $arrColumns[] = "($t.start='' OR $t.start<$time) AND ($t.stop='' OR $t.stop>$time) AND $t.published=1";
        }

        if (!isset($arrOptions['order']))
        {
            $arrOptions['order'] = "$t.date DESC";
        }

        $arrOptions['limit']  = 1;
        $arrOptions['return'] = 'Model';

        return static::findBy($arrColumns, [], $arrOptions);
    }

    /**
     * Count published news items by their parent ID
     *
     * @param array   $arrPids     An array of news archive IDs
     * @param array   $intMemberAuthor A member ID
     * @param boolean $blnFeatured If true, return only featured news, if false, return only unfeatured news
     * @param array   $arrOptions  An optional options array
     *
     * @return integer The number of news items
     */
    public static function countPublishedByPidsAndMemberAuthor($arrPids, $intMemberAuthor, $blnFeatured=null, array $arrOptions=array())
    {
        if (!is_array($arrPids) || empty($arrPids) || !$intMemberAuthor)
        {
            return 0;
        }

        $t = static::$strTable;
        $arrColumns = array("$t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")");

        if ($blnFeatured === true)
        {
            $arrColumns[] = "$t.featured='1'";
        }
        elseif ($blnFeatured === false)
        {
            $arrColumns[] = "$t.featured=''";
        }

        if (!BE_USER_LOGGED_IN)
        {
            $time = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        $arrColumns[] = QueryHelper::createWhereForSerializedBlob('memberAuthor', [$intMemberAuthor]);

        return static::countBy($arrColumns, null, $arrOptions);
    }

    /**
     * Find published news items by their parent ID
     *
     * @param array   $arrPids     An array of news archive IDs
     * @param array   $intMemberAuthor A member ID
     * @param boolean $blnFeatured If true, return only featured news, if false, return only unfeatured news
     * @param integer $intLimit    An optional limit
     * @param integer $intOffset   An optional offset
     * @param array   $arrOptions  An optional options array
     *
     * @return \Model\Collection|\NewsModel[]|\NewsModel|null A collection of models or null if there are no news
     */
    public static function findPublishedByPidsAndMemberAuthor($arrPids, $intMemberAuthor, $blnFeatured=null, $intLimit=0, $intOffset=0, array $arrOptions=array())
    {
        if (!is_array($arrPids) || empty($arrPids))
        {
            return null;
        }

        $t = static::$strTable;
        $arrColumns = array("$t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")");

        if ($blnFeatured === true)
        {
            $arrColumns[] = "$t.featured='1'";
        }
        elseif ($blnFeatured === false)
        {
            $arrColumns[] = "$t.featured=''";
        }

        // Never return unpublished elements in the back end, so they don't end up in the RSS feed
        if (!BE_USER_LOGGED_IN || TL_MODE == 'BE')
        {
            $time = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        if (!isset($arrOptions['order']))
        {
            $arrOptions['order']  = "$t.date DESC";
        }

        $arrOptions['limit']  = $intLimit;
        $arrOptions['offset'] = $intOffset;

        $arrColumns[] = QueryHelper::createWhereForSerializedBlob('memberAuthor', [$intMemberAuthor]);

        return static::findBy($arrColumns, null, $arrOptions);
    }
}