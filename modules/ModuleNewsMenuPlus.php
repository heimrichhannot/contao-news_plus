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


class ModuleNewsMenuPlus extends \ModuleNewsMenu
{

    /**
     * Generate the module
     */
    protected function compile()
    {
        parent::compile();

        $this->jumpToCurrent();
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