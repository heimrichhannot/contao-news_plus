<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsPlus;


class NewsPagination extends \Pagination
{
	protected $strHash = '';

	public function setLinkHash($strHash)
	{
		$this->strHash = $strHash;
	}

	protected function linkToPage($intPage)
	{
		$strLink = parent::linkToPage($intPage);

		if($this->strHash != '')
		{
			$strLink .= '#' . $this->strHash;
		}

		return $strLink;
	}
}