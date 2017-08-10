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

use Contao\News;
use NewsCategories\NewsCategoryModel;

class NewsPlus extends News
{
    public static function getAllPublishedNews($archives, $arrCategories)
    {
        $objAllArticles = NewsPlusModel::findPublishedByPidsAndCategories($archives, $arrCategories);
        foreach ($objAllArticles as $article)
        {
            $arrIds[] = $article->id;
        }

        return $arrIds;
    }

    /**
     * Adds support for overriding news archive's jump to in the primary news category assigned to the concrete news
     *
     * @param \NewsModel $objItem
     * @param string     $strUrl
     * @param string     $strBase
     *
     * @return string
     */
    protected function getLink($objItem, $strUrl, $strBase = '')
    {
        switch ($objItem->source)
        {
            // Link to an external page
            case 'external':
                return $objItem->url;
                break;

            // Link to an internal page
            case 'internal':
                if (($objTarget = $objItem->getRelated('jumpTo')) !== null)
                {
                    /** @var \PageModel $objTarget */
                    return $objTarget->getAbsoluteUrl();
                }
                break;

            // Link to an article
            case 'article':
                if (($objArticle = \ArticleModel::findByPk($objItem->articleId, ['eager' => true])) !== null
                    && ($objPid = $objArticle->getRelated('pid')) !== null
                )
                {
                    /** @var \PageModel $objPid */
                    return ampersand(
                        $objPid->getAbsoluteUrl(
                            '/articles/' . ((!\Config::get('disableAlias') && $objArticle->alias != '') ? $objArticle->alias : $objArticle->id)
                        )
                    );
                }
                break;

            default:
                $intJumpTo = 0;

                // news category jump to override?
                if ($objItem->primaryCategory && ($objCategory = NewsCategoryModel::findPublishedByIdOrAlias($objItem->primaryCategory)) !== null)
                {
                    $intJumpTo = $objCategory->jumpToDetails ?: $intJumpTo;
                }

                if (($objPage = \PageModel::findByPk($intJumpTo)) !== null)
                {
                    return $objPage->getAbsoluteUrl(((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ?  '/' : '/items/') . ((!\Config::get('disableAlias') && $objItem->alias != '') ? $objItem->alias : $objItem->id));
                }
                break;
        }

        // Backwards compatibility (see #8329)
        if ($strBase != '' && !preg_match('#^https?://#', $strUrl))
        {
            $strUrl = $strBase . $strUrl;
        }

        // Link to the default page
        return sprintf($strUrl, (($objItem->alias != '' && !\Config::get('disableAlias')) ? $objItem->alias : $objItem->id));
    }
}