<?php

namespace HeimrichHannot\NewsPlus;

use NewsCategories\NewsCategoryModel;

abstract class ModuleNewsPlus extends \ModuleNews
{

	/**
	 * URL cache array
	 * @var array
	 */
	private static $arrUrlCache = array();

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
	protected function parseArticle($objArticle, $blnAddArchive=false, $strClass='', $intCount=0)
	{
		global $objPage;

		$arrCategories = deserialize($objArticle->categories, true);

        $objTemplate = new \FrontendTemplate($this->news_template);
		$objTemplate->setData($objArticle->row());
		$objTemplate->class = (($objArticle->cssClass != '') ? ' ' . $objArticle->cssClass : '') . $strClass;
		$objTemplate->newsHeadline = $objArticle->headline;
		$objTemplate->subHeadline = $objArticle->subheadline;
		$objTemplate->hasSubHeadline = $objArticle->subheadline ? true : false;
		$objTemplate->linkHeadline = $this->generateLink($objArticle->headline, $objArticle, $blnAddArchive);
		$objTemplate->more = $this->generateLink($GLOBALS['TL_LANG']['MSC']['more'], $objArticle, $blnAddArchive, true);
        $objTemplate->link = $this->generateNewsUrl($objArticle, $blnAddArchive);
		$objTemplate->linkTarget = ($objArticle->target ? (($objPage->outputFormat == 'xhtml') ? ' onclick="return !window.open(this.href)"' : ' target="_blank"') : '');
		$objTemplate->linkTitle = specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $objArticle->headline), true);
		$objTemplate->count = $intCount; // see #5708
		$objTemplate->text = '';
		$objTemplate->hasText = false;
		$objTemplate->hasTeaser = false;

        // print pdf
        if($this->news_pdfJumpTo) {
            $objTemplate->showPdfButton = true;
            $pdfPage = \PageModel::findByPk($this->news_pdfJumpTo);
            $pdfArticle = \ArticleModel::findPublishedByPidAndColumn($this->news_pdfJumpTo, 'main');

            $options = deserialize($pdfArticle->printable);
            if(in_array('pdf', $options))
                $objTemplate->pdfArticleId = $pdfArticle->id;

            $strUrl = \Controller::generateFrontendUrl($pdfPage->row());
            $objTemplate->pdfJumpTo = $strUrl;
        }

		$objArchive = \NewsArchiveModel::findByPk($objArticle->pid);
		$objTemplate->archive = $objArchive;

        $objTemplate->archive->title = $objTemplate->archive->displayTitle ? $objTemplate->archive->displayTitle : $objTemplate->archive->title;
        $objTemplate->archive->class = ModuleNewsListPlus::getArchiveClassFromTitle($objTemplate->archive->title, true);
		$objTemplate->archiveTitle = $objTemplate->archive->title;

		$arrCategoryTitles = array();

		if($this->news_archiveTitleAppendCategories && !empty($arrCategories))
		{
			$arrTitleCategories = array_intersect($arrCategories, deserialize($this->news_archiveTitleCategories, true));

			if(!empty($arrTitleCategories))
			{
				$objTitleCategories = NewsCategoryModel::findPublishedByIds($arrTitleCategories);

				if($objTitleCategories !== null)
				{
					while($objTitleCategories->next())
					{
						if($objTitleCategories->frontendTitle)
						{
							$arrCategoryTitles[$objTitleCategories->id] = $objTitleCategories->frontendTitle;
							continue;
						}

						$arrCategoryTitles[$objTitleCategories->id] = $objTitleCategories->title;
					}

					$objTemplate->archiveTitle .= ' : ' . implode(' : ', $arrCategoryTitles);
				}
			}
		}

        // add tags
        $objTemplate->showTags = $this->news_showtags;
        if ($this->news_showtags && $this->news_template_modal && $this->Environment->isAjaxRequest)
        {
            $helper = new NewsPlusTagHelper();
            $tagsandlist = $helper->getTagsAndTaglistForIdAndTable($objArticle->id, 'tl_news', $this->tag_jumpTo);
            $tags = $tagsandlist['tags'];
            $taglist = $tagsandlist['taglist'];
            $objTemplate->showTagClass = $this->tag_named_class;
            $objTemplate->tags = $tags;
            $objTemplate->taglist = $taglist;
            $objTemplate->news = 'IN';
        }

        // nav
        $strUrl = '';
        $objArchive = \NewsArchiveModel::findByPk($objArticle->pid);
        if ($objArchive !== null && $objArchive->jumpTo && ($objTarget = $objArchive->getRelated('jumpTo')) !== null)
        {
            $strUrl = $this->generateFrontendUrl($objTarget->row(), ((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ?  '/%s' : '/news/%s'));
        }
        $objTemplate->nav = static::generateArrowNavigation($objArticle, $strUrl, $this->news_readerModule);


		// Clean the RTE output
		if ($objArticle->teaser != '')
		{
			$objTemplate->hasTeaser = true;

			if ($objPage->outputFormat == 'xhtml')
			{
				$objTemplate->teaser = \StringUtil::toXhtml($objArticle->teaser);
			}
			else
			{
				$objTemplate->teaser = \StringUtil::toHtml5($objArticle->teaser);
			}

			$objTemplate->teaser = \StringUtil::encodeEmail($objTemplate->teaser);
		}

		// Display the "read more" button for external/article links
		if ($objArticle->source != 'default')
		{
			$objTemplate->text = true;
			$objTemplate->hasText = true;
		}

		// Compile the news text
		else
		{
			$id = $objArticle->id;

			$objTemplate->text = function () use ($id)
			{
				$strText = '';
				$objElement = \ContentModel::findPublishedByPidAndTable($id, 'tl_news');

				if ($objElement !== null)
				{
					while ($objElement->next())
					{
						$strText .= $this->getContentElement($objElement->current());
					}
				}

				return $strText;
			};

			$objTemplate->hasText = (\ContentModel::findPublishedByPidAndTable($objArticle->id, 'tl_news') !== null);
		}

		$arrMeta = $this->getMetaFields($objArticle);

		// Add the meta information
		$objTemplate->date = $arrMeta['date'];
		$objTemplate->hasMetaFields = !empty($arrMeta);
		$objTemplate->numberOfComments = $arrMeta['ccount'];
		$objTemplate->commentCount = $arrMeta['comments'];
		$objTemplate->timestamp = $objArticle->date;
		$objTemplate->author = $arrMeta['author'];
		$objTemplate->datetime = date('Y-m-d\TH:i:sP', $objArticle->date);

		$objTemplate->addImage = false;

		// Add an image
		if ($objArticle->addImage && $objArticle->singleSRC != '')
		{
			$objModel = \FilesModel::findByUuid($objArticle->singleSRC);

			if ($objModel === null)
			{
				if (!\Validator::isUuid($objArticle->singleSRC))
				{
					$objTemplate->text = '<p class="error">'.$GLOBALS['TL_LANG']['ERR']['version2format'].'</p>';
				}
			}
			elseif (is_file(TL_ROOT . '/' . $objModel->path))
			{
				// Do not override the field now that we have a model registry (see #6303)
				$arrArticle = $objArticle->row();

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

		$objTemplate->enclosure = array();

		// Add enclosures
		if ($objArticle->addEnclosure)
		{
			$this->addEnclosuresToTemplate($objTemplate, $objArticle->row());
		}

		if(in_array('share', \ModuleLoader::getActive()))
		{
			$objArticle->title = $objArticle->headline;
			$objShare = new \HeimrichHannot\Share\Share($this->objModel, $objArticle);
			$objTemplate->share = $objShare->generate();
		}

        // Modal
        if($this->news_showInModal && $objArticle->source == 'default' && $this->news_readerModule)
        {
            $objTemplate->modal = true;
            $objTemplate->modalTarget = '#' . NewsPlusHelper::getCSSModalID($this->news_readerModule);
        }

		// HOOK: add custom logic
		if (isset($GLOBALS['TL_HOOKS']['parseArticles']) && is_array($GLOBALS['TL_HOOKS']['parseArticles']))
		{
			foreach ($GLOBALS['TL_HOOKS']['parseArticles'] as $callback)
			{
				$this->import($callback[0]);
				$this->{$callback[0]}->{$callback[1]}($objTemplate, $objArticle->row(), $this);
			}
		}

		return $objTemplate->parse();
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

/**
	 * Generate a URL and return it as string
	 * @param object
	 * @param boolean
	 * @return string
	 */
	protected function generateNewsUrl($objItem, $blnAddArchive=false)
	{
        $strCacheKey = 'id_' . $objItem->id;

		// Load the URL from cache
		if (isset(self::$arrUrlCache[$strCacheKey]))
		{
			return self::$arrUrlCache[$strCacheKey];
		}

		// Initialize the cache
		self::$arrUrlCache[$strCacheKey] = null;

		switch ($objItem->source)
		{
			// Link to an external page
			case 'external':
				if (substr($objItem->url, 0, 7) == 'mailto:')
				{
					self::$arrUrlCache[$strCacheKey] = \StringUtil::encodeEmail($objItem->url);
				}
				else
				{
					self::$arrUrlCache[$strCacheKey] = ampersand($objItem->url);
				}
				break;

			// Link to an internal page
			case 'internal':
				if (($objTarget = $objItem->getRelated('jumpTo')) !== null)
				{
					self::$arrUrlCache[$strCacheKey] = ampersand($this->generateFrontendUrl($objTarget->row()));
				}
				break;

			// Link to an article
			case 'article':
				if (($objArticle = \ArticleModel::findByPk($objItem->articleId, array('eager'=>true))) !== null && ($objPid = $objArticle->getRelated('pid')) !== null)
				{
					self::$arrUrlCache[$strCacheKey] = ampersand($this->generateFrontendUrl($objPid->row(), '/articles/' . ((!\Config::get('disableAlias') && $objArticle->alias != '') ? $objArticle->alias : $objArticle->id)));
				}
				break;
		}

		// Link to the default page
		if (self::$arrUrlCache[$strCacheKey] === null)
        {
            // priority 3 -> archive
            if (($objArchive = $objItem->getRelated('pid')) !== null && ($objJumpTo = $objArchive->getRelated('jumpTo')) !== null) {
                $intJumpTo = $objJumpTo->id;
            }

            // priority 2 -> news category
            if ($objItem->primaryCategory && ($objCategory = NewsCategoryModel::findPublishedByIdOrAlias($objItem->primaryCategory)) !== null)
            {
                $intJumpTo = $objCategory->jumpToDetails ?: $intJumpTo;
            }

            // priority 1 -> module
            $intJumpTo = $this->jumpToDetails ?: $intJumpTo;

            if(!$GLOBALS['NEWS_LIST_EXCLUDE_RELATED']) $objPage = \PageModel::findByPk($intJumpTo);

			if ($objPage === null)
			{
				self::$arrUrlCache[$strCacheKey] = ampersand(\Environment::get('request'), true);
			}
			else
			{
				self::$arrUrlCache[$strCacheKey] = ampersand($this->generateFrontendUrl($objPage->row(), ((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ?  '/' : '/items/') . ((!\Config::get('disableAlias') && $objItem->alias != '') ? $objItem->alias : $objItem->id)));
			}

			// Add the current archive parameter (news archive)
			if ($blnAddArchive && \Input::get('month') != '')
			{
				self::$arrUrlCache[$strCacheKey] .= (\Config::get('disableAlias') ? '&amp;' : '?') . 'month=' . \Input::get('month');
			}
		}
		return self::$arrUrlCache[$strCacheKey];
	}


    /**
     * Parse the template
     * @return string
     */
    public function generate()
    {
        if ($this->arrData['space'][0] != '')
        {
            $this->arrStyle[] = 'margin-top:'.$this->arrData['space'][0].'px;';
        }

        if ($this->arrData['space'][1] != '')
        {
            $this->arrStyle[] = 'margin-bottom:'.$this->arrData['space'][1].'px;';
        }

        $this->Template = new \FrontendTemplate($this->strTemplate);
        $this->Template->setData($this->arrData);

        $this->compile();

        // print to pdf
        $this->Template->pdfJumpTo = $this->news_pdfJumpTo;

        // Do not change this order (see #6191)
        $this->Template->style = !empty($this->arrStyle) ? implode(' ', $this->arrStyle) : '';
        $this->Template->class = trim('mod_' . $this->type . ' ' . $this->cssID[1]);
        $this->Template->cssID = ($this->cssID[0] != '') ? ' id="' . $this->cssID[0] . '"' : '';

        $this->Template->inColumn = $this->strColumn;

        if ($this->Template->headline == '')
        {
            $this->Template->headline = $this->headline;
        }

        if ($this->Template->hl == '')
        {
            $this->Template->hl = $this->hl;
        }

        if (!empty($this->objModel->classes) && is_array($this->objModel->classes))
        {
            $this->Template->class .= ' ' . implode(' ', $this->objModel->classes);
        }

        return $this->Template->parse();
    }


    protected static function generateArrowNavigation($objCurrentArchive, $strUrl, $modalId)
    {
        $objT = new \FrontendTemplate('navigation_arrows');

        // get ids from NewsPlus::getAllNews
        $session = \Session::getInstance()->getData();
        $arrIds = $session[NEWSPLUS_SESSION_NEWS_IDS];

        if(!$arrIds || !is_array($arrIds) || count($arrIds) < 1) return '';

        $prevID = null;
        $nextID = null;

        $currentIndex = array_search($objCurrentArchive->id, $arrIds);

        // prev only of not first item
        if(isset($arrIds[$currentIndex - 1]))
        {
            $prevID = $arrIds[$currentIndex - 1];

            $objNews = NewsPlusModel::findByPk($prevID, array());
            if($objNews !== null)
            {
                $objT->prev = static::getNewsDetails($objNews, $strUrl, $modalId);
                $objT->prevLink = $GLOBALS['TL_LANG']['news_plus']['prevLink'];
            }
        }

        // next only of not last item
        if(isset($arrIds[$currentIndex + 1]))
        {
            $nextID = $arrIds[$currentIndex + 1];
            $objNews = NewsPlusModel::findByPk($nextID, array());

            if($objNews !== null)
            {
                $objT->next = static::getNewsDetails($objNews, $strUrl, $modalId);
                $objT->nextLink = $GLOBALS['TL_LANG']['news_plus']['nextLink'];
            }
        }

        return $objT->parse();
    }

    protected static function getNewsDetails($objNews, $strUrl, $modalId)
    {
        $arrNews['modal'] = true;
        $arrNews['modalTarget'] = '#' . NewsPlusHelper::getCSSModalID($modalId);
        $arrNews['title'] = specialchars($objNews->headline, true);

        //$session = \Session::getInstance()->getData();
        //if($session[NEWSPLUS_SESSION_URL_PARAM]) $strUrlParam = '&'.$session[NEWSPLUS_SESSION_URL_PARAM];

        $arrNews['href'] = ampersand(sprintf($strUrl, ((!\Config::get('disableAlias') && $objNews->alias != '') ? $objNews->alias : $objNews->id))) . $strUrlParam;
        $arrEvent['link'] = $objNews->title;
        $arrEvent['target'] = '';

        return $arrNews;
    }
}
