<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @package news_plus
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsPlus;


class NewsArticle extends \Controller
{
	protected $objConfig;

	protected $objNews;

	protected $blnAddArchive = false;

	protected $strClass = '';

	protected $intCount = 0;

	protected $arrData = array();

	protected $arrCategories = array();

	private static $arrUrlCache = array();

	public function __construct($objNews, $objConfig, $blnAddArchive=false, $strClass='', $intCount=0)
	{
		if ($objNews instanceof \Model)
		{
			$this->objNews = $objNews;
		}
		elseif ($objNews instanceof \Model\Collection)
		{
			$this->objNews = $objNews->current();
		}

		$this->objConfig = $objConfig;
		$this->blnAddArchive = $blnAddArchive;
		$this->strClass = $strClass;
		$this->intCount = $intCount;
		$this->arrData = $objNews->row();
		$this->arrCategories = deserialize($objNews->categories, true);
		
		parent::__construct($objConfig);
	}

	public function getData()
	{
		global $objPage;

		$this->class = (($this->cssClass != '') ? ' ' . $this->cssClass : '') . $this->strClass;
		// $objTemplate->archiveTitle = $this->getArchiveTitle($objArticle->title);
		$this->newsHeadline = $this->headline;
		$this->subHeadline = $this->subheadline;
		$this->hasSubHeadline = $this->subheadline ? true : false;
		$this->linkHeadline = $this->generateLink($this->headline);
		$this->more = $this->generateLink($GLOBALS['TL_LANG']['MSC']['more'], true);
		$this->link = $this->generateNewsUrl();
		$this->linkTarget = ($this->target ? (($objPage->outputFormat == 'xhtml') ? ' onclick="return !window.open(this.href)"' : ' target="_blank"') : '');
		$this->linkTitle = specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $this->headline), true);
		$this->count = $this->intCount; // see #5708
		$this->text = '';
		$this->hasText = false;
		$this->hasTeaser = false;

		$objArchive = \NewsArchiveModel::findByPk($this->pid);

		$objArchive->title = $objArchive->displayTitle ?: $objArchive->title;
		$objArchive->class = ModuleNewsListPlus::getArchiveClassFromTitle($objArchive->title, true);
		$objArchive->archiveTitle = $objArchive->title;

		$this->archive = $objArchive;

		$arrCategoryTitles = array();

		if($this->objConfig->news_archiveTitleAppendCategories && !empty($arrCategories))
		{
			$arrTitleCategories = array_intersect($arrCategories, deserialize($this->objConfig->news_archiveTitleCategories, true));

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

					$this->archiveTitle .= ' : ' . implode(' : ', $arrCategoryTitles);
				}
			}
		}

		$this->trailInfoDistanceMin = \System::getFormattedNumber($this->trailInfoDistanceMin);
		$this->trailInfoDistanceMax = \System::getFormattedNumber($this->trailInfoDistanceMax);

		$this->trailInfoDurationMin = \System::getFormattedNumber($this->trailInfoDurationMin);
		$this->trailInfoDurationMax = \System::getFormattedNumber($this->trailInfoDurationMax);


		// add tags
		$this->showTags = $this->objConfig->news_showtags;

		if ($this->objConfig->news_showtags && $this->objConfig->news_template_modal && \Environment::get('isAjaxRequest'))
		{
			$helper = new NewsPlusTagHelper();
			$tagsandlist = $helper->getTagsAndTaglistForIdAndTable($this->id, 'tl_news', $this->objConfig->tag_jumpTo);
			$tags = $tagsandlist['tags'];
			$taglist = $tagsandlist['taglist'];
			$this->showTagClass = $this->objConfig->tag_named_class;
			$this->tags = $tags;
			$this->taglist = $taglist;
			$this->news = 'IN';
		}

		// Clean the RTE output
		if ($this->teaser != '')
		{
			$this->hasTeaser = true;

			if ($objPage->outputFormat == 'xhtml')
			{
				$this->teaser = \String::toXhtml($this->teaser);
			}
			else
			{
				$this->teaser = \String::toHtml5($this->teaser);
			}

			$this->teaser = \String::encodeEmail($this->teaser);
		}

		// Display the "read more" button for external/article links
		if ($this->source != 'default')
		{
			$this->text = true;
			$this->hasText = true;
		}

		// Compile the news text
		else
		{
			$id = $this->id;

			$this->text = function () use ($id)
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

			$this->hasText = (\ContentModel::findPublishedByPidAndTable($this->id, 'tl_news') !== null);
		}

		$arrMeta = $this->getMetaFields();

		// Add the meta information
		$this->date = $arrMeta['date'];
		$this->hasMetaFields = !empty($arrMeta);
		$this->numberOfComments = $arrMeta['ccount'];
		$this->commentCount = $arrMeta['comments'];
		$this->timestamp = $this->objNews->date;
		$this->author = $arrMeta['author'];
		$this->datetime = date('Y-m-d\TH:i:sP', $this->objNews->date);

		// Modal
		if($this->objConfig->news_showInModal && $this->source == 'default' && $this->objConfig->news_readerModule)
		{
			$this->modal = true;
			$this->modalTarget = '#' . NewsPlusHelper::getCSSModalID($this->objConfig->news_readerModule);
		}

		return $this->arrData;
	}

	/**
	 * Return the meta fields of a news article as array
	 *
	 * @return array
	 */
	protected function getMetaFields()
	{
		$meta = deserialize($this->objConfig->news_metaFields);

		if (!is_array($meta))
		{
			return array();
		}

		/** @var \PageModel $objPage */
		global $objPage;

		$return = array();

		foreach ($meta as $field)
		{
			switch ($field)
			{
				case 'date':
					$return['date'] = \Date::parse($objPage->datimFormat, $this->objNews->date);
					break;

				case 'author':
					/** @var \UserModel $objAuthor */
					if (($objAuthor = $this->objNews->getRelated('author')) !== null)
					{
						$return['author'] = $GLOBALS['TL_LANG']['MSC']['by'] . ' ' . $objAuthor->name;
					}
					break;

				case 'comments':
					if ($this->objNews->noComments || !in_array('comments', \ModuleLoader::getActive()) || $this->objNews->source != 'default')
					{
						break;
					}
					$intTotal = \CommentsModel::countPublishedBySourceAndParent('tl_news', $this->objNews->id);
					$return['ccount'] = $intTotal;
					$return['comments'] = sprintf($GLOBALS['TL_LANG']['MSC']['commentCount'], $intTotal);
					break;
			}
		}

		return $return;
	}

	/**
	 * Generate a URL and return it as string
	 * @param object
	 * @param boolean
	 * @return string
	 */
	protected function generateNewsUrl()
	{
		$strCacheKey = 'id_' . $this->objNews->id;

		// Load the URL from cache
		if (isset(self::$arrUrlCache[$strCacheKey]))
		{
			return self::$arrUrlCache[$strCacheKey];
		}

		// Initialize the cache
		self::$arrUrlCache[$strCacheKey] = null;

		switch ($this->objNews->source)
		{
			// Link to an external page
			case 'external':
				if (substr($this->objNews->url, 0, 7) == 'mailto:')
				{
					self::$arrUrlCache[$strCacheKey] = \String::encodeEmail($this->objNews->url);
				}
				else
				{
					self::$arrUrlCache[$strCacheKey] = ampersand(NewsPlusHelper::replaceInsertTagsandFixDomain($this->objNews->url));
				}
				break;

			// Link to an internal page
			case 'internal':
				if (($objTarget = $this->objNews->getRelated('jumpTo')) !== null)
				{
					self::$arrUrlCache[$strCacheKey] = ampersand(\Controller::generateFrontendUrl($objTarget->loadDetails()->row()), null, null, true);
				}
				break;

			// Link to an article
			case 'article':
				if (($objArticle = \ArticleModel::findByPk($this->objNews->articleId, array('eager'=>true))) !== null && ($objPid = $objArticle->getRelated('pid')) !== null)
				{
					self::$arrUrlCache[$strCacheKey] = ampersand(\Controller::generateFrontendUrl($objPid->loadDetails()->row(), '/articles/' . ((!\Config::get('disableAlias') && $this->objNews->alias != '') ? $this->objNews->alias : $this->objNews->id)), null, true);
				}
				break;
		}

		// Link to the default page
		if (self::$arrUrlCache[$strCacheKey] === null)
		{
			if(!$GLOBALS['NEWS_LIST_EXCLUDE_RELATED']) $objPage = \PageModel::findByPk($this->objNews->getRelated('pid')->jumpTo);

			if ($objPage === null)
			{
				self::$arrUrlCache[$strCacheKey] = ampersand(\Environment::get('request'), true);
			}
			else
			{
				self::$arrUrlCache[$strCacheKey] = ampersand(\Controller::generateFrontendUrl($objPage->loadDetails()->row(), ((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ?  '/' : '/items/') . ((!\Config::get('disableAlias') && $this->objNews->alias != '') ? $this->objNews->alias : $this->objNews->id)), null, true);
			}

			// Add the current archive parameter (news archive)
			if ($this->blnAddArchive && \Input::get('month') != '')
			{
				self::$arrUrlCache[$strCacheKey] .= (\Config::get('disableAlias') ? '&amp;' : '?') . 'month=' . \Input::get('month');
			}
		}
		return self::$arrUrlCache[$strCacheKey];
	}

	/**
	 * Generate a link and return it as string
	 *
	 * @param string     $strLink
	 * @param boolean    $blnIsReadMore
	 *
	 * @return string
	 */
	protected function generateLink($strLink, $blnIsReadMore=false)
	{
		// Internal link
		if ($this->objNews->source != 'external')
		{
			return sprintf('<a href="%s" title="%s">%s%s</a>',
						   $this->generateNewsUrl(),
						   specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $this->objNews->headline), true),
						   $strLink,
				($blnIsReadMore ? '<span class="invisible"> '.$this->objNews->headline.'</span>' : ''));
		}

		// Encode e-mail addresses
		if (substr($this->objNews->url, 0, 7) == 'mailto:')
		{
			$strArticleUrl = \StringUtil::encodeEmail($this->objNews->url);
		}

		// Ampersand URIs
		else
		{
			$strArticleUrl = ampersand($this->objNews->url);
		}

		/** @var \PageModel $objPage */
		global $objPage;

		// External link
		return sprintf('<a href="%s" title="%s"%s>%s</a>',
					   $strArticleUrl,
					   specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['open'], $strArticleUrl)),
			($this->objNews->target ? (($objPage->outputFormat == 'xhtml') ? ' onclick="return !window.open(this.href)"' : ' target="_blank"') : ''),
					   $strLink);
	}


	/**
	 * Set an object property
	 *
	 * @param string $strKey
	 * @param mixed  $varValue
	 */
	public function __set($strKey, $varValue)
	{
		$this->arrData[$strKey] = $varValue;
	}


	/**
	 * Return an object property
	 *
	 * @param string $strKey
	 *
	 * @return mixed
	 */
	public function __get($strKey)
	{
		if (isset($this->arrData[$strKey]))
		{
			return $this->arrData[$strKey];
		}

		return parent::__get($strKey);
	}


	/**
	 * Check whether a property is set
	 *
	 * @param string $strKey
	 *
	 * @return boolean
	 */
	public function __isset($strKey)
	{
		return isset($this->arrData[$strKey]);
	}
}
