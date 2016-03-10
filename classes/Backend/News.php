<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsPlus\Backend;


class News extends \Backend
{


	public function getMoreLinkText()
	{
		$arrOptions = array();

		$arrTitles = $GLOBALS['TL_LANG']['MSC']['news']['morelinktext'];

		if(!is_array($arrTitles))
		{
			return $arrOptions;
		}

		foreach($arrTitles as $strKey => $strTitle)
		{
			if(is_array($strTitle))
			{
				$strTitle = $strTitle[0];
			}

			$arrOptions[$strKey] = $strTitle;
		}

		return $arrOptions;
	}
}