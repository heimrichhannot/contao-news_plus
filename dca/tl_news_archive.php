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


/**
 * Config
 */
$dc['list']['sorting']['fields'] = array('root', 'title');

/**
 * Palettes
 */
$dc['palettes']['default'] = str_replace('jumpTo;', 'jumpTo;{root_legend},root;', $dc['palettes']['default']);

/**
 * Fields
 */
$arrFields = array
(
	'root' => array
	(
		'label'            => &$GLOBALS['TL_LANG']['tl_news_archive']['root'],
		'inputType'        => 'select',
		'options_callback' => array('tl_news_archive_plus', 'getRootPages'),
		'eval'             => array('includeBlankOption' => true),
		'sql'              => "int(10) unsigned NOT NULL default '0'",
	)
);

$dc['fields'] = array_merge($dc['fields'], $arrFields);

class tl_news_archive_plus extends \Backend
{

	public function getRootPages(\DataContainer $dc)
	{
		$arrOptions = array();

		$objPages = \PageModel::findBy('type', 'root');

		if ($objPages === null) return $arrOptions;

		return $objPages->fetchEach('title');
	}
}