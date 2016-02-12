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