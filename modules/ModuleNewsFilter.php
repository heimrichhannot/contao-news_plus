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

    /**
     * CategroyTemplate
     * @var string
     */
    protected $strCategoryTemplate = 'filter_cat_default';

    protected function compile()
    {
        // Set the flags
        $GLOBALS['NEWS_FILTER_SHOW_SEARCH'] = $this->news_filterShowSearch ? true : false; // filter news by search
        $GLOBALS['NEWS_FILTER_USE_SEARCH_INDEX'] = $this->news_filterUseSearchIndex ? true : false; // filter news by search index or tl_news table
        $GLOBALS['NEWS_FILTER_FUZZY_SEARCH'] = $this->news_filterFuzzySearch ? true : false; // use fuzzy search
        $GLOBALS['NEWS_FILTER_SEARCH_QUERY_TYPE'] = $this->news_filterSearchQueryType ? "and" : "or"; // query type 'and' or 'or'

        $this->strCategoryTemplate = $this->news_filterCategoryTemplate ?: NULL;

        // Show search form in template
        $this->Template->showSearch = $this->news_filterShowSearch ?: NULL;
        $this->Template->showCategories = $this->news_filterShowCategories ?: NULL;

        // Set Fields
        $this->Template->searchKeywords = trim(\Input::get('searchKeywords'));


        /** @var \Contao\Database\Result $rs */
        $rs = \Database::getInstance()->query('SELECT * FROM tl_news_archive ORDER BY title');
        $arrResult = $rs->fetchAllAssoc();

        if(empty($arrResult)) {
            return '';
        }

        global $objPage;
        $objTemplate = new \FrontendTemplate($this->searchTpl ?: $this->strCategoryTemplate);

        if($this->strCategoryTemplate == 'filter_cat_multilevel') {
            $strCategories = trim(\Input::get('newscategories'));
            $objTemplate->filterName = $GLOBALS['TL_LANG']['news_plus']['filterLabel'];
            if ($strCategories != '') {
                $objTemplate->pageLink = $objPage->alias;
                $filterName = ModuleNewsListPlus::findArchiveTitleByPid($strCategories);
                $objTemplate->filterResetName = $GLOBALS['TL_LANG']['news_plus']['resetFilterLabel'];
                $objTemplate->filterName = self::getShortCategoryTitle($filterName);
            }

            $objTemplate->categories = self::groupCategoriesByArchivesTitle($arrResult);
        } else {
            $objTemplate->optionValues = self::getCategoriesFromArchiveTitle($arrResult);
        }

        $this->Template->categories = $objTemplate->parse();
    }

    protected function groupCategoriesByArchivesTitle($archives)
    {
        $strCat = '';
        foreach($archives as $archive) {
            $type = explode(' ', trim($archive['title']));
            $subject = explode(' - ', trim($archive['title']));

            if (strpos($archive['title'], 'Pressemitteilungen') !== false) {
                $archive['title'] = $subject['1'];
                if (count($type) > 2 && count($subject) > 1 && $type['1'] != '-')
                    $arrCategories[$type['0']][$type['1']][] = $archive;
                elseif (count($type) > 3 AND count($subject) > 1)
                    $arrCategories[$type['0']][$subject['1']][] = $archive;
                elseif (count($type) == 3 && count($subject) == 2)
                    $arrCategories[$type['0']][] = $archive;
            } else
                $arrCategories[] = $archive;
        }

        foreach($arrCategories as $key=>$arrArchive) {
            if (is_int($key)) {
                $strCat .= self::getCategoryLink($arrArchive);
            } else {
                $strCat .= self::getCategorySubmenu($arrCategories[$key], $key);
            }
        }

        return $strCat;
    }

    protected function getCategoryLink($archive)
    {
        $objTemplate = new \FrontendTemplate($this->searchTpl ?: 'form_newsfilter_cat_ml_link');
        $objTemplate->value = $archive['title'];
        $objTemplate->pageLink = self::getPageLink();
        $objTemplate->categoryParam = '&newscategories=' . $archive['id'];
        return $objTemplate->parse();
    }

    protected function getCategorySubmenu($arrArchives, $strTitle = '')
    {
        $strCat = '';
        $objTemplate = new \FrontendTemplate($this->searchTpl ?: 'form_newsfilter_cat_ml_submenu');
        $objTemplate->title = $strTitle;
        foreach($arrArchives as $key=>$arrArchive) {
            if (is_int($key))
                $strCat .= self::getCategoryLink($arrArchive);
            else
                $strCat .= self::getCategorySubmenu($arrArchives[$key], $key);

        }
        $objTemplate->values = $strCat;
        return $objTemplate->parse();
    }

    protected function getCategoriesFromArchiveTitle($archives) {
        $strCat = '';
        foreach($archives as $archive)
        {
            $objTemplate = new \FrontendTemplate($this->searchTpl ?: 'form_newsfilter_cat_option');
            $objTemplate->id = $archive['id'];
            $objTemplate->value = $archive['title'];
            $strCat .= $objTemplate->parse();
        }
        return $strCat;
    }

    protected function getPageLink()
    {
        global $objPage;
        $arrPageLinkParam = array();
        if($this->Template->searchKeywords) $arrPageLinkParam[] = 'searchKeywords='.$this->Template->searchKeywords;

        return $objPage->alias.'?'.implode("&", $arrPageLinkParam);
    }

    public static function getShortCategoryTitle($title)
    {
        $subject = explode(' - ', trim($title));
        return array_pop($subject);
    }
}