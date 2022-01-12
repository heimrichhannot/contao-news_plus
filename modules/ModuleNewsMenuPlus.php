<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @package news_plus
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsPlus;

use Codefog\NewsCategoriesBundle\FrontendModule\NewsMenuModule;
use Haste\Util\Url;

class ModuleNewsMenuPlus extends NewsMenuModule
{

    /**
     * Generate the module
     */
    protected function compile()
    {
        parent::compile();

        $this->jumpToCurrent();
    }

    /**
     * Generate the yearly menu
     */
    protected function compileYearlyMenu()
    {
        $time = time();
        $arrData = array();
        $arrNewsIds = $this->getFilteredNewsIds();
        
        // Configure template for yearly menu
        if (version_compare(VERSION, '4.0', '<'))
        {
            $this->Template = new \FrontendTemplate('mod_newsmenu_year');
        }
        else
        {
            $this->Template->yearly = true;
        }

        // Get the dates
        $objDates = $this->Database->query("SELECT FROM_UNIXTIME(date, '%Y') AS year, COUNT(*) AS count FROM tl_news WHERE pid IN(" . implode(',', array_map('intval', $this->news_archives)) . ")" . ((!BE_USER_LOGGED_IN || TL_MODE == 'BE') ? " AND (start='' OR start<$time) AND (stop='' OR stop>$time) AND published=1" : "") . (!empty($arrNewsIds) ? (" AND id IN (" . implode(',', $arrNewsIds) . ")") : "") . " GROUP BY year ORDER BY year DESC");

        while ($objDates->next())
        {
            $arrData[$objDates->year] = $objDates->count;
        }

        // Sort the data
        ($this->news_order == 'ascending') ? ksort($arrData) : krsort($arrData);

        $arrItems = array();
        $count = 0;
        $limit = count($arrData);
        $strUrl = $this->generateCategoryUrl();

        // Prepare the navigation
        foreach ($arrData as $intYear=>$intCount)
        {
            $intDate = $intYear;
            $quantity = sprintf((($intCount < 2) ? $GLOBALS['TL_LANG']['MSC']['entry'] : $GLOBALS['TL_LANG']['MSC']['entries']), $intCount);

            $arrItems[$intYear]['date'] = $intDate;
            $arrItems[$intYear]['link'] = $intYear;
            $arrItems[$intYear]['href'] = Url::removeQueryString(['year'], \Environment::get('request')) . ($GLOBALS['TL_CONFIG']['disableAlias'] ? '&amp;' : '?') . 'year=' . $intDate;
            $arrItems[$intYear]['title'] = specialchars($intYear . ' (' . $quantity . ')');
            $arrItems[$intYear]['class'] = trim(((++$count == 1) ? 'first ' : '') . (($count == $limit) ? 'last' : ''));
            $arrItems[$intYear]['isActive'] = (\Input::get('year') == $intDate);
            $arrItems[$intYear]['quantity'] = $quantity;
        }

        $this->Template->items = $arrItems;
        $this->Template->showQuantity = ($this->news_showQuantity != '');
    }

    protected function jumpToCurrent()
    {
        $arrItems = $this->Template->items;

        // do not jump to current, if year, month or day have been selected by user
        if (!is_array($arrItems) || empty($arrItems) || isset($_GET['year']) || isset($_GET['month']) || isset($_GET['day']))
        {
            return false;
        }

        $time = $this->news_format_reference ?: time();

        // Get the total number of items
        $intTotal = \NewsModel::countPublishedFromToByPids($this->intBegin, $this->intEnd, $this->news_archives);

        // set time interval from latest news
        if ($intTotal === 0 && $this->news_jumpToCurrent == 'show_current')
        {
            $objLatestNews = NewsPlusModel::findLatestPublishedByPids($this->news_archives);

            if ($objLatestNews !== null)
            {
                $time = $objLatestNews->date;
            }
        }

        foreach ($arrItems as $intPeriod => $arrItem)
        {
            if ($this->news_format == 'news_year' && $intPeriod == date('Y', $time))
            {
                $arrItems[$intPeriod]['isActive'] = true;
                break;
            }
            else
            {
                if ($this->news_format == 'news_month' && $intPeriod == date('Ym', $time))
                {
                    $arrItems[$intPeriod]['isActive'] = true;
                    break;
                }
                else
                {
                    if ($this->news_format == 'news_day' && $intPeriod == date('Ymd' , $time))
                    {
                        $arrItems[$intPeriod]['isActive'] = true;
                        break;
                    }
                }
            }
        }

        $this->Template->items = $arrItems;
    }

}
