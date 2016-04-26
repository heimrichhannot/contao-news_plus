<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package news_plus
 * @author Mathias Arzberger <develop@pdir.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsPlus;

abstract class NewsPlus extends ModuleNewsPlus
{

    /**
     * get all news items by page pid
     * @param array
     * @param integer
     * @param boolean
     * @return array
     */
    public static function getAllNews($arrPages, $intRoot=0, $blnIsSitemap=false)
    {
        $arrRoot = array();

        if ($intRoot > 0)
        {
            $arrRoot = \Database::getInstance()->getChildRecords($intRoot, 'tl_page');
        }

        $time = time();
        $arrProcessed = array();

        // Get all news archives
        $objArchive = \NewsArchiveModel::findByProtected('');

        // Walk through each archive
        if ($objArchive !== null)
        {
            while ($objArchive->next())
            {
                // Skip news archives without target page
                if (!$objArchive->jumpTo)
                {
                    continue;
                }

                // Skip news archives outside the root nodes
                if (!empty($arrRoot) && !in_array($objArchive->jumpTo, $arrRoot))
                {
                    continue;
                }

                // Get the URL of the jumpTo page
                if (!isset($arrProcessed[$objArchive->jumpTo]))
                {
                    $objParent = \PageModel::findWithDetails($objArchive->jumpTo);

                    // The target page does not exist
                    if ($objParent === null)
                    {
                        continue;
                    }

                    // The target page has not been published (see #5520)
                    if (!$objParent->published || ($objParent->start != '' && $objParent->start > $time) || ($objParent->stop != '' && $objParent->stop < $time))
                    {
                        continue;
                    }

                    // The target page is exempt from the sitemap (see #6418)
                    if ($blnIsSitemap && $objParent->sitemap == 'map_never')
                    {
                        continue;
                    }

                    // Set the domain (see #6421)
                    // $domain = ($objParent->rootUseSSL ? 'https://' : 'http://') . ($objParent->domain ?: \Environment::get('host')) . TL_PATH . '/';

                    // Generate the URL
                    // $arrProcessed[$objArchive->jumpTo] = $domain . $this->generateFrontendUrl($objParent->row(), ((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ?  '/%s' : '/items/%s'), $objParent->language);
                }

                $strUrl = $arrProcessed[$objArchive->jumpTo];

                // Get the items
                $objArticle = \NewsModel::findPublishedDefaultByPid($objArchive->id);

                if ($objArticle !== null)
                {
                    while ($objArticle->next())
                    {
                        $arrPages[] = $objArticle->id;
                    }
                }
            }
        }

        return $arrPages;
    }


    public static function getAllPublishedNews($archives, $arrCategories)
    {
        $objAllArticles = NewsPlusModel::findPublishedByPids($archives, $arrCategories);
        foreach($objAllArticles as $article){
            $arrIds[] = $article->id;
        }
        return $arrIds;
    }


    /**
     * Calculate the span between two timestamps in days
     * @param integer
     * @param integer
     * @return integer
     */
    public static function calculateSpan($intStart, $intEnd)
    {
        return self::unixToJd($intEnd) - self::unixToJd($intStart);
    }



    /**
     * Convert a UNIX timestamp to a Julian day
     * @param integer
     * @return integer
     */
    public static function unixToJd($tstamp)
    {
        list($year, $month, $day) = explode(',', date('Y,m,d', $tstamp));

        // Make year a positive number
        $year += ($year < 0 ? 4801 : 4800);

        // Adjust the start of the year
        if ($month > 2)
        {
            $month -= 3;
        }
        else
        {
            $month += 9;
            --$year;
        }

        $sdn  = floor((floor($year / 100) * 146097) / 4);
        $sdn += floor((($year % 100) * 1461) / 4);
        $sdn += floor(($month * 153 + 2) / 5);
        $sdn += $day - 32045;

        return $sdn;
    }


}
