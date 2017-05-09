<?php

namespace HeimrichHannot\NewsPlus;

use HeimrichHannot\FieldPalette\FieldPaletteModel;
use HeimrichHannot\Haste\Map\GoogleMap;
use HeimrichHannot\Haste\Map\GoogleMapOverlay;
use HeimrichHannot\Haste\Visualization\GoogleChartWrapper;

abstract class ModuleNewsPlus extends \ModuleNews
{
	/**
	 * Active news, if list and reader module are on the same page
	 * @var
	 */
	protected $activeNews = null;

	/**
	 * Template for google maps
	 * @var string
	 */
	public $templateMaps = 'dlh_googlemaps_haste';

	/**
	 * Template for marker info in google maps
	 * @var string
	 */
	public $templateMarkerInfo = 'dlh_infowindow';

	/**
	 * Default icon for markers
	 * @var string
	 */
	public $strMarkerIcon = 'system/modules/news_plus/assets/img/maps-marker.png';


	/**
	 * Sort out protected archives
	 * @param array
	 * @return array
	 */
	protected function sortOutProtected($arrArchives)
	{
		if (BE_USER_LOGGED_IN || !is_array($arrArchives) || empty($arrArchives))
		{
			return $arrArchives;
		}

		$this->import('FrontendUser', 'User');
		$objArchive = \NewsArchiveModel::findMultipleByIds($arrArchives);
		$arrArchives = array();

		if ($objArchive !== null)
		{
			while ($objArchive->next())
			{
				if ($objArchive->protected)
				{
					if (!FE_USER_LOGGED_IN)
					{
						continue;
					}

					$groups = deserialize($objArchive->groups);

					if (!is_array($groups) || empty($groups) || !count(array_intersect($groups, $this->User->groups)))
					{
						continue;
					}
				}

				$arrArchives[] = $objArchive->id;
			}
		}

		return $arrArchives;
	}

	/**
	 * Parse an item and return it as string
	 * @param object
	 * @param boolean
	 * @param string
	 * @param integer
	 * @return string
	 */
	protected function parseArticle($objNews, $blnAddArchive=false, $strClass='', $intCount=0)
	{
		// add active class if current news is active news
		if($this->activeNews !== null && $this->activeNews->id == $objNews->id)
		{
			$strClass .= ($strClass ? ' active' : '');
		}

		$arrData = $this->generateArticle($objNews, $blnAddArchive, $strClass, $intCount);

		$strTemplate = ($objNews->news_template) ? : $this->news_template;
		$objTemplate = new \FrontendTemplate($strTemplate);
		$objTemplate->setData($arrData);

		$objTemplate->addImage = false;

		// Add an image
		if ($objNews->addImage && $objNews->singleSRC != '')
		{
			$objModel = \FilesModel::findByUuid($objNews->singleSRC);

			if ($objModel === null)
			{
				if (!\Validator::isUuid($objNews->singleSRC))
				{
					$objTemplate->text = '<p class="error">'.$GLOBALS['TL_LANG']['ERR']['version2format'].'</p>';
				}
			}
			elseif (is_file(TL_ROOT . '/' . $objModel->path))
			{
				// Do not override the field now that we have a model registry (see #6303)
				$arrArticle = $objNews->row();

				// Override the default image size
				if ($this->imgSize != '')
				{
					$size = deserialize($this->imgSize);

					if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]))
					{
						$arrArticle['size'] = $this->imgSize;
					}
				}

				$arrArticle['singleSRC'] = $objModel->path;
				$this->addImageToTemplate($objTemplate, $arrArticle);
			}
		}

		// Add enclosures
		if ($objNews->addEnclosure)
		{
			$this->addEnclosuresToTemplate($objTemplate, $objNews->row());
		}

		// Add subnews
		if ($objNews->addSubNews)
		{
			$this->addSubNewsToTemplate($objTemplate, $objNews->row());
		}

		// Add Google Maps with KML data
		if ($objNews->addTrailInfo && $objNews->addTrailInfoKmlData)
		{
			$objMap = new GoogleMap();
			$objMap->setInfoWindowUnique(true);

			$objKml = new GoogleMapOverlay();
			$objKml->setType(GoogleMapOverlay::TYPE_KML_GEOXML);
			$objKml->setKmlUrl($objNews->trailInfoKmlData);

			if ($objNews->addVenues && $objNews->venues !== null)
			{
				$objVenues = FieldPaletteModel::findMultipleByIds(deserialize($objNews->venues, true));

				if ($objVenues !== null)
				{
					foreach ($objVenues as $objVenue)
					{
						$objMarker = new GoogleMapOverlay();
						$objMarker->setPosition($objVenue->venueSingleCoords);
						$objMarker->setMarkerType(GoogleMapOverlay::MARKERTYPE_ICON);
						$objMarker->setIconSRC(($objNews->strMarkerIcon) ? : $this->strMarkerIcon);
						$objMarker->setIconAnchor(array(0, 20, 'px'));
						$objMarker->setIconSize(array(17, 30, 'px'));
						$objMarker->setMarkerAction(GoogleMapOverlay::MARKERACTION_INFO);
						$objMarkerInfoTemplate = new \FrontendTemplate(($objNews->templateMarkerInfo) ? : $this->templateMarkerInfo);
						$objMarkerInfoTemplate->setData($objVenue->row());
						$objMarker->setInfoWindow($objMarkerInfoTemplate->parse());
						$objMarker->setInfoWindowAnchor(array(1, 22, 'px'));
						$objMarker->setInfoWindowSize(array('auto', 'auto', 'px'));
						$objMap->addOverlay($objMarker);
					}
				}
			}

			$objMap->addOverlay($objKml);

			$objMap->initId();
			$objTemplate->objMap = $objMap->generate(
				array(
					'mapSize' => array('100%', '480px', ''),
					'zoom'    => 15,
					'dlh_googlemap_template' => ($objNews->templateMaps) ? : $this->templateMaps,
				)
			);

			if ($objNews->trailInfoShowElevationProfile)
			{
				$objChart = new GoogleChartWrapper();

				$objChart->setMap($objMap->getId());
				$objTemplate->objChart = $objChart->generate(
					array(
						'chartSize'    => array('100%', '', ''),
						'google_chart_template' => 'google_chart_column',
					)
				);
			}
		}

		// Add share
		if(in_array('share', \ModuleLoader::getActive()))
		{
			$objNews->title = $objNews->headline;
			$objShare = new \HeimrichHannot\Share\Share($this->objModel, $objNews);
			$objTemplate->share = $objShare->generate();
		}

		if($this->news_addNavigation)
		{
			$objTemplate->nav = $this->generateNavigation($objNews, $arrData['link'], $this->news_readerModule);
		}
		
		// HOOK: add custom logic
		if (isset($GLOBALS['TL_HOOKS']['parseArticles']) && is_array($GLOBALS['TL_HOOKS']['parseArticles']))
		{
			foreach ($GLOBALS['TL_HOOKS']['parseArticles'] as $callback)
			{
				$this->import($callback[0]);
				$this->{$callback[0]}->{$callback[1]}($objTemplate, $objNews->row(), $this);
			}
		}

		return $objTemplate->parse();
	}


	/**
	 * Generate the article data and return it as array
	 * @param object
	 * @param boolean
	 * @param string
	 * @param integer
	 * @return array
	 */
	protected function generateArticle($objNews, $blnAddArchive=false, $strClass='', $intCount=0)
	{
		$objArticle = new NewsArticle($objNews, $this, $blnAddArchive, $strClass, $intCount);

		return $objArticle->getData();
	}


	/**
	 * Parse one or more items and return them as array
	 * @param object
	 * @param boolean
	 * @return array
	 */
	protected function parseArticles($objArticles, $blnAddArchive=false)
	{
		$arrArticles = parent::parseArticles($objArticles, $blnAddArchive);

		// HOOK: add custom logic
		if (isset($GLOBALS['TL_HOOKS']['parseAllArticles']) && is_array($GLOBALS['TL_HOOKS']['parseAllArticles']))
		{
			foreach ($GLOBALS['TL_HOOKS']['parseAllArticles'] as $callback)
			{
				$this->import($callback[0]);
				$arrArticles = $this->{$callback[0]}->{$callback[1]}($arrArticles, $blnAddArchive, $this);
			}
		}

		return $arrArticles;
	}

    protected function generateNavigation($objCurrentArticle, $strUrl, $modalId)
    {
        $objT = new \FrontendTemplate($this->news_navigation_template);

		// get ids from newslist
		$arrIds = \Session::getInstance()->get(NewsPlusHelper::getKeyForSessionNewsIds($this->objModel));
	
		// if no list context, aquire news ids from news_archive
        if(count($arrIds) < 1)
		{
			$objNews = NewsPlusModel::findPublishedByPid($objCurrentArticle->pid);

			if($objNews == null)
			{
				return '';
			}

			$arrIds = $objNews->fetchEach('id');
		}

		$objPrevNews = NewsPlusModel::findNewPublishedByIds($objCurrentArticle->id, $objCurrentArticle->date, $arrIds, $this->news_navigation_infinite);

        // prev only if not first item
        if($objPrevNews !== null)
        {
			$objT->prev = $this->generateArticle($objPrevNews);
			$objT->prevLink = $GLOBALS['TL_LANG']['news_plus']['prevLink'];
        }

		$objNextNews = NewsPlusModel::findOldPublishedByIds($objCurrentArticle->id, $objCurrentArticle->date, $arrIds, $this->news_navigation_infinite);

        // next only of not last item
        if($objNextNews !== null)
        {
			$objT->next = $this->generateArticle($objNextNews);
			$objT->nextLink = $GLOBALS['TL_LANG']['news_plus']['nextLink'];
        }

        return $objT->parse();
    }

	/**
	 * Add subnews to a template
	 *
	 * @param object $objTemplate The template object to add the subnews to
	 * @param array  $arrItem     The element or module as array
	 * @param string $strKey      The name of the field in $arrItem
	 */
	public function addSubNewsToTemplate($objTemplate, $arrItem, $strKey='subNews')
	{
		$arrSubnews = array();

		$objFieldpalette = \HeimrichHannot\FieldPalette\FieldPaletteModel::findPublishedByIds(deserialize($arrItem[$strKey], true));

		if($objFieldpalette === null)
		{
			return $arrSubnews;
		}


		while($objFieldpalette->next())
		{
			$objNews = NewsPlusModel::findPublishedByIds(array($objFieldpalette->nid));

			if($objNews === null)
			{
				continue;
			}

			$objNews->news_template = $objFieldpalette->news_template;
			$arrSubnews[] = $this->parseArticle($objNews);
		}

		$objTemplate->parsedSubNews = $arrSubnews;
	}
}
