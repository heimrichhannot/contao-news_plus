<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsPlus;


class ModuleNewsArchive extends \ModuleNewsArchive
{
    /**
     * Generate the module
     */
    protected function compile()
    {
        /** @var \PageModel $objPage */
        global $objPage;

        $limit = null;
        $offset = 0;
        $intBegin = 0;
        $intEnd = 0;

        $intYear = \Input::get('year');
        $intMonth = \Input::get('month');
        $intDay = \Input::get('day');

        $time = $this->news_format_reference ?: time();

        // Jump to the current period
        if (!isset($_GET['year']) && !isset($_GET['month']) && !isset($_GET['day']) && $this->news_jumpToCurrent != 'all_items')
        {
            switch ($this->news_format)
            {
                case 'news_year':
                    $intYear = date('Y', $time);
                    break;

                default:
                case 'news_month':
                    $intMonth = date('Ym', $time);
                    break;

                case 'news_day':
                    $intDay = date('Ymd', $time);
                    break;
            }
        }

        // Create the date object
        try
        {
            if ($intYear)
            {
                $strDate = $intYear;
                $objDate = new \Date($strDate, 'Y');
                $intBegin = $objDate->yearBegin;
                $intEnd = $objDate->yearEnd;
                $this->headline .= ' ' . date('Y', $objDate->tstamp);
            }
            elseif ($intMonth)
            {
                $strDate = $intMonth;
                $objDate = new \Date($strDate, 'Ym');
                $intBegin = $objDate->monthBegin;
                $intEnd = $objDate->monthEnd;
                $this->headline .= ' ' . \Date::parse('F Y', $objDate->tstamp);
            }
            elseif ($intDay)
            {
                $strDate = $intDay;
                $objDate = new \Date($strDate, 'Ymd');
                $intBegin = $objDate->dayBegin;
                $intEnd = $objDate->dayEnd;
                $this->headline .= ' ' . \Date::parse($objPage->dateFormat, $objDate->tstamp);
            }
            elseif ($this->news_jumpToCurrent == 'all_items')
            {
                $intBegin = 0;
                $intEnd = time();
            }
        }
        catch (\OutOfBoundsException $e)
        {
            /** @var \PageError404 $objHandler */
            $objHandler = new $GLOBALS['TL_PTY']['error_404']();
            $objHandler->generate($objPage->id);
        }

        $this->Template->articles = array();

        // Split the result
        if ($this->perPage > 0)
        {
            // Get the total number of items
            $intTotal = \NewsModel::countPublishedFromToByPids($intBegin, $intEnd, $this->news_archives);

            if ($intTotal > 0)
            {
                $total = $intTotal;

                // Get the current page
                $id = 'page_a' . $this->id;
                $page = (\Input::get($id) !== null) ? \Input::get($id) : 1;

                // Do not index or cache the page if the page number is outside the range
                if ($page < 1 || $page > max(ceil($total/$this->perPage), 1))
                {
                    /** @var \PageError404 $objHandler */
                    $objHandler = new $GLOBALS['TL_PTY']['error_404']();
                    $objHandler->generate($objPage->id);
                }

                // Set limit and offset
                $limit = $this->perPage;
                $offset = (max($page, 1) - 1) * $this->perPage;

                // Add the pagination menu
                $objPagination = new \Pagination($total, $this->perPage, \Config::get('maxPaginationLinks'), $id);
                $this->Template->pagination = $objPagination->generate("\n  ");
            }
        }

        // Get the news items
        if (isset($limit))
        {
            $objArticles = \NewsModel::findPublishedFromToByPids($intBegin, $intEnd, $this->news_archives, $limit, $offset);
        }
        else
        {
            $objArticles = \NewsModel::findPublishedFromToByPids($intBegin, $intEnd, $this->news_archives);
        }

        // Add the articles
        if ($objArticles !== null)
        {
            $this->Template->articles = $this->parseArticles($objArticles);
        }

        $this->Template->headline = trim($this->headline);
        $this->Template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];
        $this->Template->empty = $GLOBALS['TL_LANG']['MSC']['empty'];
    }
}