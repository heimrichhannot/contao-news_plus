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

class ModuleNewsFilter extends ModuleNewsPlus
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


	public function generate()
	{
		if (TL_MODE == 'BE') {
			$objTemplate           = new \BackendTemplate('be_wildcard');
			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD'][$this->type][0]) . ' ###';
			$objTemplate->title    = $this->headline;
			$objTemplate->id       = $this->id;
			$objTemplate->link     = $this->name;
			$objTemplate->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		return parent::generate();
	}

    protected function compile()
    {
        global $objPage;

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

        $this->news_archives = $this->sortOutProtected(deserialize($this->news_archives));

        // Return if there are no archives
        if (!is_array($this->news_archives) || empty($this->news_archives))
        {
            return '';
        }

		// news archive, with newscategories (news_categories module)
		$this->news_filterNewsCategoryArchives = deserialize($this->news_filterNewsCategoryArchives, true);

        $sql = "SELECT * FROM tl_news_archive WHERE tl_news_archive.id IN(" . implode(',', array_map('intval', $this->news_archives)) . ") ORDER BY title";

        /** @var \Contao\Database\Result $rs */
        $rs = \Database::getInstance()->query($sql);
        $arrResult = $rs->fetchAllAssoc();

        if(empty($arrResult)) {
            return '';
        }

        $objTemplate = new \FrontendTemplate($this->searchTpl ?: $this->strCategoryTemplate);
        $objTemplate->filterName = $GLOBALS['TL_LANG']['news_plus']['filterLabel'];
        $objTemplate->rootPageLink = \Controller::generateFrontendUrl($objPage->row());

        if($this->strCategoryTemplate == 'filter_cat_multilevel') {
            $strNewsArchives = trim(\Input::get('newscategories'));
			$strNewsCategories = trim(\Input::get('category'));

            if ($strNewsArchives || $strNewsCategories) {
                $filterName = ModuleNewsListPlus::findArchiveTitleByPid($strNewsArchives);

				// overwrite title with news_category
				if($filterName && $strNewsCategories)
				{
					$objNewsCategory = \NewsCategories\NewsCategoryModel::findPublishedByIdOrAlias($strNewsCategories);

					if($objNewsCategory !== null)
					{
						$filterName = $objNewsCategory->frontendTitle ? $objNewsCategory->frontendTitle : $objNewsCategory->title;
					}
				}
				
                $objTemplate->filterName = self::getShortCategoryTitle($filterName);
                $objTemplate->filterResetName = $GLOBALS['TL_LANG']['news_plus']['resetFilterLabel'];
                $objTemplate->pageLink = $objPage->alias;
                $objTemplate->hiddenField = $strNewsArchives;
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
	
            if (is_int($key))
			{
				if(in_array($arrArchive['id'], $this->news_filterNewsCategoryArchives))
				{
					$strCat .= self::getNewsCategoriesAsSubmenu($arrArchive);
				}
				else
				{
					$strCat .= self::getCategoryLink($arrArchive);
				}
            }
			else
			{
                $strCat .= self::getCategorySubmenu($arrCategories[$key], $key);
            }
        }
	
        return $strCat;
    }

    protected function getCategoryLink($arrArchive)
    {
        $objTemplate = new \FrontendTemplate($this->searchTpl ?: 'form_newsfilter_cat_ml_link');
        $objTemplate->value = $arrArchive['title'];
        $objTemplate->pageLink = self::getPageLink(null, array('newscategories' => $arrArchive['id']));
		$objTemplate->active = in_array($arrArchive['id'], deserialize(\Input::get('newscategories'), true));
        return $objTemplate->parse();
    }

	protected function getNewsCategoriesAsSubmenu($arrArchive)
	{
		$strCat = '';

		$objCategories = \NewsCategories\NewsCategoryModel::findPublishedByParent($arrArchive);

		if($objCategories === null)
		{
			return $strCat;
		}

		$arrCategories = array();

		while($objCategories->next())
		{
			$strCat .= self::getNewsCategoryLink($objCategories->current(), $arrArchive);
			$arrCategories[$objCategories->id] = $objCategories->row();
		}
		
		$objTemplate = new \FrontendTemplate($this->searchTpl ?: 'form_newsfilter_cat_ml_submenu');
		$objTemplate->title = $arrArchive['title'];
		$objTemplate->active = in_array($arrArchive['id'], deserialize(\Input::get('newscategories'), true)) && \Input::get('category') == '';
		$objTemplate->trail = in_array($arrArchive['id'], deserialize(\Input::get('newscategories'), true)) && \Input::get('category') != '';
		$objTemplate->groupPageLink = self::getPageLink(array_keys($arrCategories), array('newscategories' =>$arrArchive['id'] ));
		$objTemplate->values = $strCat;
		return $objTemplate->parse();

		return $strCat;
	}

	protected function getNewsCategoryLink($objCategory, $arrArchive)
	{
		$objTemplate = new \FrontendTemplate($this->searchTpl ?: 'form_newsfilter_cat_ml_link');
		$objTemplate->value = $objCategory->title;
		$objTemplate->pageLink = self::getPageLink(null, array('newscategories' => $arrArchive['id'], 'category' => $objCategory->id));;
		$objTemplate->active = in_array($objCategory->id, deserialize(\Input::get('category'), true));
		return $objTemplate->parse();
	}

    protected function getCategorySubmenu($arrArchives, $strTitle = '')
    {
        $strCat = '';
        $objTemplate = new \FrontendTemplate($this->searchTpl ?: 'form_newsfilter_cat_ml_submenu');
        $objTemplate->title = $strTitle;
		$categories = array();

		$arrSelected = trimsplit(',', \Input::get('newscategories'));

        foreach($arrArchives as $key=>$arrArchive) {
			$categories[] = $arrArchive['id'];

            if (is_int($key)){
				$strCat .= self::getCategoryLink($arrArchive);
			}
            else
			{
				$strCat .= self::getCategorySubmenu($arrArchives[$key], $key);

				$arrChildMenuCategories = array();

				foreach($arrArchives[$key] as $subKey => $subArrArchive)
				{
					$arrChildMenuCategories[] = $subArrArchive['id'];
				}
			}
        }

		$isActive = count(array_intersect($arrSelected, $categories)) > 1;
		$isTrail = count(array_intersect($arrSelected, $categories)) == 1;

		if(!empty($arrChildMenuCategories))
		{
			$isTrail = count(array_intersect($arrSelected, $arrChildMenuCategories)) == 1;
		}

        $objTemplate->groupPageLink = self::getPageLink($categories, array('newscategories' => implode(",", array_filter($categories))));
		$objTemplate->active = $isActive;
		$objTemplate->trail = $isTrail;
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

    protected function getPageLink($categories = array(), $arrParams = array())
    {
        global $objPage;

        $arrPageLinkParam = array();

        if($this->Template->searchKeywords)
		{
			$arrPageLinkParam[] = 'searchKeywords='.$this->Template->searchKeywords;
		}

		if(is_array($arrParams))
		{
			foreach($arrParams as $key => $value)
			{
				$arrPageLinkParam[] = "$key=$value";
			}
		}
	
        return $objPage->alias . '?' . implode("&", $arrPageLinkParam);
    }

    public static function getShortCategoryTitle($title)
    {
        $subject = explode(' - ', trim($title));
        return array_pop($subject);
    }
}