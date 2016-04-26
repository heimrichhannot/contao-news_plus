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
}