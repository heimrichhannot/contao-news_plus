<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsPlus\InsertTags;


use NewsCategories\NewsCategoryModel;

class InsertTags extends \Controller
{
    /**
     * Add additional tags
     *
     * @param $strTag
     * @param $blnCache
     * @param $strCache
     * @param $flags
     * @param $tags
     * @param $arrCache
     * @param $index
     * @param $count
     *
     * @return mixed Return false, if the tag was not replaced, otherwise return the value of the replaced tag
     */
    public function replace($strTag, $blnCache, $strCache, $flags, $tags, $arrCache, $index, $count)
    {
        $elements = explode('::', $strTag);

        switch (strtolower($elements[0]))
        {
            case 'news_plus':
            case 'news_plus_open':
            case 'news_plus_url':
                if (($objNews = \NewsModel::findByIdOrAlias($elements[1])) === null)
                {
                    break;
                }

                $strUrl = '';

                if ($objNews->source == 'external')
                {
                    if (substr($objNews->url, 0, 7) == 'mailto:')
                    {
                        $strUrl = \StringUtil::encodeEmail($objNews->url);
                    }
                    else
                    {
                        $strUrl = ampersand($objNews->url);
                    }
                }
                elseif ($objNews->source == 'internal')
                {
                    if (($objJumpTo = $objNews->getRelated('jumpTo')) !== null)
                    {
                        $strUrl = ampersand($this->generateFrontendUrl($objJumpTo->row()));
                    }
                }
                elseif ($objNews->source == 'article')
                {
                    if (($objArticle = \ArticleModel::findByPk($objNews->articleId, array('eager'=>true))) !== null && ($objPid = $objArticle->getRelated('pid')) !== null)
                    {
                        $strUrl = ampersand($this->generateFrontendUrl($objPid->row(), '/articles/' . ((!\Config::get('disableAlias') && $objArticle->alias != '') ? $objArticle->alias : $objArticle->id)));
                    }
                }
                else
                {
                    $intJumpTo = 0;

                    // priority 2 -> archive
                    if (($objArchive = $objNews->getRelated('pid')) !== null && ($objJumpTo = $objArchive->getRelated('jumpTo')) !== null)
                    {
                        $intJumpTo = $objJumpTo->id;
                    }

                    // priority 1 -> news category
                    if ($objNews->primaryCategory && ($objCategory = NewsCategoryModel::findPublishedByIdOrAlias($objNews->primaryCategory)) !== null)
                    {
                        $intJumpTo = $objCategory->jumpToDetails ?: $intJumpTo;
                    }

                    if (($objPage = \PageModel::findByPk($intJumpTo)) === null)
                    {
                        $strUrl = ampersand(\Environment::get('request'), true);
                    }
                    else
                    {
                        $strUrl = ampersand($this->generateFrontendUrl($objPage->row(), ((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ?  '/' : '/items/') . ((!\Config::get('disableAlias') && $objNews->alias != '') ? $objNews->alias : $objNews->id)));
                    }
                }

                // Replace the tag
                switch (strtolower($elements[0]))
                {
                    case 'news_plus':
                        $arrCache[$strTag] = sprintf('<a href="%s" title="%s">%s</a>', $strUrl, specialchars($objNews->headline), $objNews->headline);
                        break;

                    case 'news_plus_open':
                        $arrCache[$strTag] = sprintf('<a href="%s" title="%s">', $strUrl, specialchars($objNews->headline));
                        break;

                    case 'news_plus_url':
                        $arrCache[$strTag] = $strUrl;
                        break;
                }

                return $arrCache[$strTag];
        }

        return false;
    }
}
