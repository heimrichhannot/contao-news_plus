<?php
/**
 * Contao Open Source CMS
 * 
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package news_plus
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$dc = &$GLOBALS['TL_DCA']['tl_news_archive'];

$dc['palettes']['default'] = str_replace('title', 'title,displayTitle', $dc['palettes']['default']);

$arrFields = array
(
	'displayTitle' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_news_archive']['displayTitle'],
		'exclude'                 => true,
		'search'                  => true,
		'inputType'               => 'text',
		'eval'                    => array('maxlength'=>255),
		'sql'                     => "varchar(255) NOT NULL default ''"
	),
);

$dc['fields'] = array_merge($dc['fields'], $arrFields);