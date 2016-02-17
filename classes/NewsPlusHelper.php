<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package news_plus
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */
namespace HeimrichHannot\NewsPlus;

class NewsPlusHelper extends \Controller
{
	/**
	 * \Contao\InsertTags::replace does not fix the domain, until contao 3.5.7
	 * see: https://github.com/contao/core/issues/8212
	 *
	 * @param $strUrl
	 *
	 * @return string The Url
	 */
	public static function replaceInsertTagsandFixDomain($strUrl)
	{
		// Preserve insert tags
		if (\Config::get('disableInsertTags'))
		{
			return \StringUtil::restoreBasicEntities($strUrl);
		}

		$tags = preg_split('/{{(([^{}]*|(?R))*)}}/', $strUrl, -1, PREG_SPLIT_DELIM_CAPTURE);


		for ($_rit=0, $_cnt=count($tags); $_rit<$_cnt; $_rit+=3)
		{
			$strTag = $tags[$_rit + 1];

			// Skip empty tags
			if ($strTag == '') {
				continue;
			}

			// Run the replacement again if there are more tags
			if (strpos($strTag, '{{') !== false)
			{
				$strTag = static::replaceInsertTagsandFixDomain($strTag);
			}

			$flags = explode('|', $strTag);
			$tag = array_shift($flags);
			$elements = explode('::', $tag);

			// Replace the tag
			switch (strtolower($elements[0]))
			{
				case 'news_url':
					if (($objNews = \NewsModel::findByIdOrAlias($elements[1])) === null)
					{
						break;
					}

					if ($objNews->source == 'external')
					{
						$strUrl = $objNews->url;
					}
					elseif ($objNews->source == 'internal')
					{
						if (($objJumpTo = $objNews->getRelated('jumpTo')) !== null)
						{
							$strUrl = \Controller::generateFrontendUrl($objJumpTo->loadDetails()->row(), null, null, true);
						}
					}
					elseif ($objNews->source == 'article')
					{
						if (($objArticle = \ArticleModel::findByPk($objNews->articleId, array('eager'=>true))) !== null && ($objPid = $objArticle->getRelated('pid')) !== null)
						{
							$strUrl = \Controller::generateFrontendUrl($objPid->loadDetails()->row(), '/articles/' . ((!\Config::get('disableAlias') && $objArticle->alias != '') ? $objArticle->alias : $objArticle->id), null, true);
						}
					}
					else
					{
						if (($objArchive = $objNews->getRelated('pid')) !== null && ($objJumpTo = $objArchive->getRelated('jumpTo')) !== null)
						{
							$strUrl = \Controller::generateFrontendUrl($objJumpTo->loadDetails()->row(), ((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ?  '/' : '/items/') . ((!\Config::get('disableAlias') && $objNews->alias != '') ? $objNews->alias : $objNews->id), null, true);
						}
					}

					break;
			}
		}

		return $strUrl;

	}

	public static function getCSSModalID($id, $type='reader')
	{
		$strID = 'modal_' . $type . '_' . $id;
		return $strID;
	}


	/**
	 * Get the session key for the news id from associated reader module
	 *
	 * @param \ModuleModel $objReaderModule Model of the news reader
	 *
	 * @return string The key for news ids from session
	 */
	public static function getKeyForSessionNewsIds(\ModuleModel $objReaderModule)
	{
		return NEWSPLUS_SESSION_NEWS_IDS . '_' . $objReaderModule->id;
	}
}