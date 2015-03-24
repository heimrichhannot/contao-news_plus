<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package news_plus
 * @author Mathias Arzberger <develop@pdir.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

/**
 * Class NewsPlusTagHelper
 *
 * Helper class for tags
 * @copyright  Helmut Schottmüller 2008-2010
 * @author     Helmut Schottmüller <contao@aurealis.de>
 * @package    Controller
 */

namespace HeimrichHannot\NewsPlus;

use Contao\TagHelper;

class NewsPlusTagHelper extends TagHelper
{

    public function getTagsAndTaglistForIdAndTable($id, $table, $jumpto)
    {
        $pageArr = array();
        if (strlen($jumpto))
        {
            $objFoundPage = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?")
                ->limit(1)
                ->execute($jumpto);
            $pageArr = ($objFoundPage->numRows) ? $objFoundPage->fetchAssoc() : array();
        }
        if (count($pageArr) == 0)
        {
            global $objPage;
            $pageArr = $objPage->row();
        }
        $tags = $this->getTags($id, $table);
        $taglist = array();
        foreach ($tags as $id => $tag)
        {
            $strUrl = ampersand($this->generateFrontendUrl($pageArr, $items . '?tag=' . \System::urlencode($tag)));
            $tags[$id] = '<a href="' . $strUrl . '">' . specialchars($tag) . '</a>';
            $taglist[$id] = array(
                'url' => $tags[$id],
                'tag' => $tag,
                'class' => \TagList::_getTagNameClass($tag)
            );
        }
        return array(
            'tags' => $tags,
            'taglist' => $taglist
        );
    }

    static function getNewsIdByTableAndTag($tag)
    {
        $sql = "SELECT tid FROM tl_tag WHERE from_table = 'tl_news' AND tag = '$tag'";
        $rs = \Database::getInstance()->query($sql);
        $arrResult = $rs->fetchEach('tid');
        return $arrResult;
    }
}