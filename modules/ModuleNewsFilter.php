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


class ModuleNewsFilter extends \Module
{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_newsfilter';

    protected function compile()
    {
        /** @var \Contao\Database\Result $rs */
        $rs = \Database::getInstance()->query('SELECT * FROM tl_news_archive ORDER BY title');

        $this->Template->archives = $rs->fetchAllAssoc();
    }
}