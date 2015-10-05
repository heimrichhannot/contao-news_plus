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

$dc = &$GLOBALS['TL_DCA']['tl_news_archive'];

/**
 * Config
 */
$dc['list']['sorting']['fields'] = array('root', 'title');

/**
 * Add a global operation to tl_news_archive
 */
array_insert($GLOBALS['TL_DCA']['tl_news_archive']['list']['global_operations'], 1, array
(
	'config_fe' => array
	(
		'label'               => &$GLOBALS['TL_LANG']['tl_news_archive']['news_config'],
		'href'                => 'table=tl_news_config',
		'icon'                => 'system/modules/news_plus/assets/img/news_config.png',
		'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="c"'
	)
));

/**
 * Palettes
 */
$dc['palettes']['__selector__'][] = 'addDummyImage';
$dc['palettes']['default'] = str_replace('title', 'title,displayTitle', $dc['palettes']['default']);
$dc['palettes']['default'] = str_replace('jumpTo;', 'jumpTo;{root_legend},root;', $dc['palettes']['default']);
$dc['palettes']['default'] = str_replace('jumpTo;', 'jumpTo;{image_legend},addDummyImage;', $dc['palettes']['default']);

/**
 * Subpalettes
 */
$dc['subpalettes']['addDummyImage'] = 'dummyImageSingleSRC';

$arrFields = array
(
	'displayTitle' => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['displayTitle'],
		'exclude'   => true,
		'search'    => true,
		'inputType' => 'text',
		'eval'      => array('maxlength' => 255),
		'sql'       => "varchar(255) NOT NULL default ''"
	),
	'root'         => array
	(
		'label'            => &$GLOBALS['TL_LANG']['tl_news_archive']['root'],
		'inputType'        => 'select',
		'options_callback' => array('tl_news_archive_plus', 'getRootPages'),
		'eval'             => array('includeBlankOption' => true),
		'sql'              => "int(10) unsigned NOT NULL default '0'",
	),
	'addDummyImage'                  => array(
		'label'     => &$GLOBALS['TL_LANG']['tl_dav_ad']['addDummyImage'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'eval'      => array('submitOnChange' => true),
		'sql'       => "char(1) NOT NULL default ''"
	),
	'dummyImageSingleSRC'                 => array(
		'label'     => &$GLOBALS['TL_LANG']['tl_dav_ad']['dummyImageSingleSRC'],
		'exclude'   => true,
		'inputType' => 'fileTree',
		'eval'      => array('filesOnly' => true, 'fieldType' => 'radio', 'mandatory' => true, 'tl_class' => 'clr'),
		'sql'       => "binary(16) NULL"
	)
);

$dc['fields'] = array_merge($dc['fields'], $arrFields);

class tl_news_archive_plus extends \Backend
{

	public function getRootPages(\DataContainer $dc)
	{
		$arrOptions = array();

		$objPages = \PageModel::findBy('type', 'root');

		if ($objPages === null) {
			return $arrOptions;
		}

		return $objPages->fetchEach('title');
	}
}