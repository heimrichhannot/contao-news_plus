<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @package news_plus
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsPlus;


class ModuleNewsArchive extends \ModuleNewsArchive
{
	protected function compile()
	{
		// support jump to latest published news day/month/year
		if(strrpos($this->news_jumpToCurrent, 'latest_', -strlen($this->news_jumpToCurrent)) !== FALSE)
		{
			$objNews = NewsPlusModel::findMaxPublishedByPids($this->news_archives);

			if($objNews !== null)
			{
				switch($this->news_jumpToCurrent)
				{
					case 'latest_year':
						\Input::setGet('year', date('Y', $objNews->date));
						break;
					case 'latest_month':
						\Input::setGet('month', date('Ym', $objNews->date));
					break;
					case 'latest_day':
						\Input::setGet('day', date('Ymd', $objNews->date));
					break;
				}
			}
		}

		parent::compile();

	}
}