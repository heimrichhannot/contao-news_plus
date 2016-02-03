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
