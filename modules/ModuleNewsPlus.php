<?php

namespace HeimrichHannot\NewsPlus;

abstract class ModuleNewsPlus extends \ModuleNews
{
    /**
     * News
     * @var string
     */
    protected $news;

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
		$arrData = $this->generateArticle($objNews, $blnAddArchive, $strClass, $intCount);

		$objTemplate = new \FrontendTemplate($this->news_template);
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

		$arrMeta = $this->getMetaFields($objNews);

		// Add the meta information
		$objTemplate->date = $arrMeta['date'];
		$objTemplate->hasMetaFields = !empty($arrMeta);
		$objTemplate->numberOfComments = $arrMeta['ccount'];
		$objTemplate->commentCount = $arrMeta['comments'];
		$objTemplate->timestamp = $objNews->date;
		$objTemplate->author = $arrMeta['author'];
		$objTemplate->datetime = date('Y-m-d\TH:i:sP', $objNews->date);

		// Add enclosures
		if ($objNews->addEnclosure)
		{
			$this->addEnclosuresToTemplate($objTemplate, $objNews->row());
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
				$this->$callback[0]->$callback[1]($objTemplate, $objNews->row(), $this);
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
				$arrArticles = $this->$callback[0]->$callback[1]($arrArticles, $blnAddArchive, $this);
			}
		}

		return $arrArticles;
	}

    protected function generateNavigation($objCurrentArticle, $strUrl, $modalId)
    {
        $objT = new \FrontendTemplate($this->news_navigation_template);

		// get ids from newslist
		$arrIds = \Session::getInstance()->get(NEWSPLUS_SESSION_NEWS_IDS);

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

        $currentIndex = array_search($objCurrentArticle->id, $arrIds);

		$prevID = isset($arrIds[$currentIndex - 1]) ? $arrIds[$currentIndex - 1] : ($this->news_navigation_infinite ? end($arrIds) : null);
	
        // prev only of not first item
        if($prevID !== null)
        {
            $objNews = NewsPlusModel::findByPk($prevID, array());

            if($objNews !== null)
            {
                $objT->prev = $this->generateArticle($objNews);
                $objT->prevLink = $GLOBALS['TL_LANG']['news_plus']['prevLink'];
            }
        }

		$nextID = isset($arrIds[$currentIndex + 1]) ? $arrIds[$currentIndex + 1] : ($this->news_navigation_infinite ? reset($arrIds) : null);

        // next only of not last item
        if($nextID !== null)
        {
            $objNews = NewsPlusModel::findByPk($nextID, array());

            if($objNews !== null)
            {
                $objT->next = $this->generateArticle($objNews);
                $objT->nextLink = $GLOBALS['TL_LANG']['news_plus']['nextLink'];
            }
        }

        return $objT->parse();
    }
}
