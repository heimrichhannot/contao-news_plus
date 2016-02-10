<?php
/**
 * Contao Open Source CMS
 * 
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package news_plus
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$dc = &$GLOBALS['TL_DCA']['tl_news'];

$dc['fields']['enclosure']['eval']['orderField']  = 'orderEnclosureSRC';

$arrFields = array
(
	'orderEnclosureSRC' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_news']['orderEnclosureSRC'],
		'sql'                     => "blob NULL"
	),
);

$dc['fields'] = array_merge($dc['fields'], $arrFields);