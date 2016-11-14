<?php

namespace HeimrichHannot\NewsPlus;

use Contao\FilesModel;
use Contao\NewsArchiveModel;
use Contao\PageModel;
use HeimrichHannot\FieldPalette\FieldPaletteModel;
use NewsCategories\NewsCategoryModel;

class ModuleNewsListMap extends ModuleNewsPlus
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_newslist_map';

	/** @var NewsFilterRegistry  */
	protected $objFilter = null;

    protected $arrSubmission = array();

	protected $strKeywords;

	protected $t = "tl_news";

    protected $filterActive = false;

    protected $filterSearch = false;

	protected $news_categories = array();


    /**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['newslist_map'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

		$this->news_archives = $this->sortOutProtected(deserialize($this->news_archives));

		// Return if there are no archives
		if (!is_array($this->news_archives) || empty($this->news_archives))
		{
			return '';
		}

		$this->objFilter = NewsFilterRegistry::getInstance($this->arrData);

        $this->activeNews = $this->getActiveNews();

        return parent::generate();
    }

    /**
     * Generate the module
     */
    protected function compile()
    {
        $offset = intval($this->skipFirst);
        $limit = null;

        // Maximum number of items
        if ($this->numberOfItems > 0) {
            $limit = $this->numberOfItems;
        }


        // Handle featured news
        if ($this->news_featured == 'featured') {
            $blnFeatured = true;
        } elseif ($this->news_featured == 'unfeatured') {
            $blnFeatured = false;
        } else {
            $blnFeatured = null;
        }

        // show all news items, filter is active - TODO: make configurable in tl_module
        if($this->filterActive)
        {
            $blnFeatured = null;
        }

		$strEmpty = $GLOBALS['TL_LANG']['MSC']['emptyList'];

		if($this->news_empty_overwrite && ($strLabel = $GLOBALS['TL_LANG']['MSC']['news_plus']['emptyNewsList'][$this->news_empty_label]) != '')
		{
			$strEmpty = $strLabel;
		}

        $this->Template->empty = $strEmpty;

		// Get the total number of items
		$intTotal = $this->countItems($blnFeatured);

		if ($intTotal < 1)
		{
			return;
		}
		
        $total = $intTotal - $offset;

        // Adjust the overall limit
        if(isset($limit)) {
            $total = min($limit, $total);
        }
		$this->Template->archives = $this->news_archives;

		$arrArticles = $this->fetchItems($blnFeatured, $limit, $offset);

		$this->Template->map = $this->generateVenueMap($arrArticles);
    }

	public function generateVenueMap($arrArticles)
	{
		$arrVenues = array();

		foreach($arrArticles as $objArticle)
		{
			if(!$objArticle->addVenues) continue;

			$objField = FieldPaletteModel::findByPidAndTableAndField($objArticle->id, 'tl_news', 'venues');

			if(!$objField->venueSingleCoords) continue;

			$objVenue = $objField->current();
			$objVenue->link = $this->getMarkerLink($objArticle);

			$arrVenues[] = $objVenue;
		}

		if(empty($arrVenues))
		{
			return;
		}

		$objMap = new \HeimrichHannot\Haste\Map\GoogleMap();

		$markerIcon = $this->customMarkerIcon ? FilesModel::findByUuid($this->markerIcon)->path : null;
		$markerAction = $this->news_showInModal ? \HeimrichHannot\Haste\Map\GoogleMapOverlay::MARKERACTION_MODAL : \HeimrichHannot\Haste\Map\GoogleMapOverlay::MARKERACTION_LINK;

		foreach($arrVenues as $objVenue)
		{
			$objMap->setCenter($objVenue->venueSingleCoords);
			$objMarker = new \HeimrichHannot\Haste\Map\GoogleMapOverlay();

			if($this->customMarkerIcon)
			{
				$objMarker->setIconSRC($markerIcon);
				$objMarker->setIconSize(array($this->markerWidth, $this->markerHeight));
			}

			$objMarker->setMarkerType(\HeimrichHannot\Haste\Map\GoogleMapOverlay::MARKERTYPE_ICON);
			$objMarker->setMarkerAction($markerAction);
			$objMarker->setUrl($objVenue->link);
			$objMarker->setTarget("#modal_reader_".$this->news_readerModule);
			$objMarker->setPosition($objVenue->venueSingleCoords);
			$objMarker->setTitle($objVenue->venueName);

			$objMap->addOverlay($objMarker);
		}

		return $objMap->generate(
				array(
						'mapSize' => array($this->mapWidth, $this->mapHeight, ''),
						'zoom'    => $this->mapZoom,
				)
		);
	}

	public function getMarkerLink($article)
	{
		$objNewsArchive = NewsArchiveModel::findById($article->pid);
		$strPageAlias = PageModel::findPublishedById($objNewsArchive->jumpTo)->alias;
		return $GLOBALS['TL_LANGUAGE'].'/'.$strPageAlias.'/'. $article->alias;
	}


	/**
	 * Fetch the matching items
	 *
	 * @param  boolean $blnFeatured
	 * @param  integer $limit
	 * @param  integer $offset
	 *
	 * @return \Model\Collection|\NewsModel|null
	 */
	protected function fetchItems($blnFeatured, $limit, $offset)
	{
		// HOOK: add custom logic
		if (isset($GLOBALS['TL_HOOKS']['newsListPlusFetchItems']) && is_array($GLOBALS['TL_HOOKS']['newsListPlusFetchItems']))
		{
			foreach ($GLOBALS['TL_HOOKS']['newsListPlusFetchItems'] as $callback)
			{
				if (($objCollection = \System::importStatic($callback[0])->{$callback[1]}($this->objFilter, $blnFeatured, $limit, $offset, $this)) === false)
				{
					continue;
				}

				if ($objCollection === null || $objCollection instanceof \Model\Collection)
				{
					return $objCollection;
				}
			}
		}

		// store all news item ids in the session if reader module is set
		if($this->objFilter !== null && $this->news_readerModule > 0 && ($objReader = \ModuleModel::findByPk($this->news_readerModule)) !== null)
		{
			$objAllItems = NewsPlusModel::findPublishedByFilter($this->objFilter, $blnFeatured, 0, 0, array());

			if($objAllItems !== null)
			{
				// store ids for later navigation
				\Session::getInstance()->set(NewsPlusHelper::getKeyForSessionNewsIds($objReader), $objAllItems->fetchEach('id'));
			}
		}

		return NewsPlusModel::findPublishedByFilter($this->objFilter, $blnFeatured, ($limit ?: 0), $offset, array());
	}


	/**
	 * Count the total matching items
	 *
	 * @param boolean $blnFeatured
	 *
	 * @return integer
	 */
	protected function countItems($blnFeatured)
	{
		// HOOK: add custom logic
		if (isset($GLOBALS['TL_HOOKS']['newsListPlusCountItems']) && is_array($GLOBALS['TL_HOOKS']['newsListPlusCountItems']))
		{
			foreach ($GLOBALS['TL_HOOKS']['newsListPlusCountItems'] as $callback)
			{
				if (($intResult = \System::importStatic($callback[0])->{$callback[1]}($this->objFilter, $blnFeatured, $this)) === false)
				{
					continue;
				}

				if (is_int($intResult))
				{
					return $intResult;
				}
			}
		}

		return NewsPlusModel::countPublishedByFilter($this->objFilter, $blnFeatured);
	}


	/**
	 * Get the active item if news reader is set
	 * @return The NewsPlusModel or null if none is set
	 */
	protected function getActiveNews()
	{
		// Set the item from the auto_item parameter
		if (!isset($_GET['items']) && \Config::get('useAutoItem') && isset($_GET['auto_item']))
		{
			\Input::setGet('items', \Input::get('auto_item'));
		}

		// Do not index or cache the page if no news item has been specified
		if (!\Input::get('items'))
		{
			return null;
		}

		if($this->news_showInModal || $this->news_readerModule < 1 || ($objModule = \ModuleModel::findByPk($this->news_readerModule)) === null)
		{
			return null;
		}

		$arrArchives = $this->sortOutProtected(deserialize($objModule->news_archives));

		if (!is_array($arrArchives) || empty($arrArchives))
		{
			return null;
		}

		// Get the news item
		$objNews = NewsPlusModel::findPublishedByParentAndIdOrAlias(\Input::get('items'), $arrArchives);

		if ($objNews === null)
		{
			return null;
		}

		return $objNews;
	}

    /**
     * @param $obj
     * @return array
     */
    protected function getArchiveTitle($obj)
    {
        $str = $obj->__get('title');
        $str = explode(' ',trim($str));
        return $str;
    }

    public static function findArchiveTitleByPid($pid)
    {
        $archive = \NewsArchiveModel::findByPk($pid);
        return $archive->title;
    }

    public static function findArchiveByPid($pid)
    {
        return \NewsArchiveModel::findByPk($pid);
    }

    static function getArchiveClassFromTitle($title, $strToLower = false)
    {
        $type = standardize($title);
        $strBase = strstr($type, '-', true);
        $strNewTitle = ($strBase ? $strBase . ' ' : '') . $type;
        if($strToLower) $strNewTitle = strtolower($strNewTitle);
        return $strNewTitle;
    }
}
