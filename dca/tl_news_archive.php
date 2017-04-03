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
 * Palettes
 */
$dc['palettes']['__selector__'][] = 'addDummyImage';
$dc['palettes']['__selector__'][] = 'replaceNewsPalette';
$dc['palettes']['__selector__'][] = 'limitSubNews';
$dc['palettes']['__selector__'][] = 'addDescriptionPrefixOnArchived';
$dc['palettes']['__selector__'][] = 'limitInputCharacterLength';

$dc['palettes']['default']        = str_replace('title', 'title,displayTitle', $dc['palettes']['default']);
$dc['palettes']['default']        = str_replace('jumpTo;', 'jumpTo;{root_legend},root;', $dc['palettes']['default']);
$dc['palettes']['default']        = str_replace('jumpTo;', 'jumpTo;{image_legend},addDummyImage;', $dc['palettes']['default']);
$dc['palettes']['default']        = str_replace('jumpTo;', 'jumpTo;{palette_legend},replaceNewsPalette;', $dc['palettes']['default']);
$dc['palettes']['default']        = str_replace('jumpTo;', 'jumpTo;{subnews_legend},limitSubNews;', $dc['palettes']['default']);
$dc['palettes']['default']        = str_replace('jumpTo;', 'jumpTo;{input_legend},limitInputCharacterLength;', $dc['palettes']['default']);
$dc['palettes']['default']        = str_replace('jumpTo', 'jumpTo,addDescriptionPrefixOnArchived', $dc['palettes']['default']);

/**
 * Subpalettes
 */
$dc['subpalettes']['addDummyImage'] = 'dummyImageSingleSRC';
$dc['subpalettes']['replaceNewsPalette'] = 'newsPalette';
$dc['subpalettes']['limitSubNews'] = 'subNewsArchives';
$dc['subpalettes']['addDescriptionPrefixOnArchived'] = 'descriptionPrefixOnArchived,archivedInterval';
$dc['subpalettes']['limitInputCharacterLength'] = 'inputCharacterLengths';

$arrFields = array
(
	'displayTitle'        => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['displayTitle'],
		'exclude'   => true,
		'search'    => true,
		'inputType' => 'text',
		'eval'      => array('maxlength' => 255),
		'sql'       => "varchar(255) NOT NULL default ''",
	),
	'root'                => array
	(
		'label'            => &$GLOBALS['TL_LANG']['tl_news_archive']['root'],
		'inputType'        => 'select',
		'options_callback' => array('tl_news_archive_plus', 'getRootPages'),
		'eval'             => array('includeBlankOption' => true),
		'sql'              => "int(10) unsigned NOT NULL default '0'",
	),
	'addDummyImage'       => array(
		'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['addDummyImage'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'eval'      => array('submitOnChange' => true),
		'sql'       => "char(1) NOT NULL default ''",
	),
	'dummyImageSingleSRC' => array(
		'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['dummyImageSingleSRC'],
		'exclude'   => true,
		'inputType' => 'fileTree',
		'eval'      => array('filesOnly' => true, 'fieldType' => 'radio', 'mandatory' => true, 'tl_class' => 'clr'),
		'sql'       => "binary(16) NULL",
	),
	'replaceNewsPalette'  => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['replaceNewsPalette'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'eval'      => array('submitOnChange' => true),
		'sql'       => "char(1) NOT NULL default ''",
	),
	'newsPalette'         => array
	(
		'label'            => &$GLOBALS['TL_LANG']['tl_news_archive']['newsPalette'],
		'exclude'          => true,
		'inputType'        => 'select',
		'eval'             => array('mandatory' => true, 'includeBlankOption' => true),
		'options_callback' => array('tl_news_archive_plus', 'getNewsPalettes'),
		'sql'              => "varchar(255) NOT NULL default ''",
	),
	'limitSubNews'        => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['limitSubNews'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'eval'      => array('submitOnChange' => true),
		'sql'       => "char(1) NOT NULL default ''",
	),
	'subNewsArchives'     => array
	(
		'label'            => &$GLOBALS['TL_LANG']['tl_news_archive']['subNewsArchives'],
		'exclude'          => true,
		'inputType'        => 'checkboxWizard',
		'options_callback' => array('tl_news_archive_plus', 'getSubNewsArchives'),
		'eval'             => array('multiple' => true, 'mandatory' => true),
		'sql'              => "blob NULL",
	),
	'addDescriptionPrefixOnArchived'        => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['addDescriptionPrefixOnArchived'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'eval'      => array('submitOnChange' => true),
		'sql'       => "char(1) NOT NULL default ''",
	),
	'descriptionPrefixOnArchived' => array(
		'label'                   => &$GLOBALS['TL_LANG']['tl_news_archive']['descriptionPrefixOnArchived'],
		'exclude'                 => true,
		'search'                  => true,
		'inputType'               => 'text',
		'eval'                    => array('maxlength' => 255, 'tl_class' => 'w50'),
		'sql'                     => "varchar(255) NOT NULL default ''"
	),
	'archivedInterval' => array(
		'label'                   => &$GLOBALS['TL_LANG']['tl_news_archive']['archivedInterval'],
		'exclude'                 => true,
		'inputType'               => 'text',
		'default'                 => 365,
		'eval'                    => array('rgxp' => 'digit', 'tl_class' => 'w50'),
		'sql'                     => "int(10) unsigned NOT NULL default '0'"
	),
	'limitInputCharacterLength' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['limitInputCharacterLength'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'inputCharacterLengths'     => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['inputCharacterLengths'],
        'exclude'   => true,
        'inputType' => 'multiColumnEditor',
        'eval'      => [
            'tl_class'          => 'clr',
            'multiColumnEditor' => [
                'minRowCount' => 0,
                'fields'      => [
                    'field'  => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['inputCharacterLengths']['field'],
                        'options'   => ['headline', 'subheadline', 'teaser'],
                        'reference' => &$GLOBALS['TL_LANG']['tl_news'],
                        'inputType' => 'select',
                        'eval'      => ['chosen' => true, 'style' => 'width: 250px'],
                    ],
                    'length' => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['inputCharacterLengths']['length'],
                        'inputType' => 'text',
                        'eval'      => ['style' => 'width: 250px', 'rgxp' => 'digit'],
                    ],
                ],
            ],
        ],
        'sql'       => "blob NULL",
    ],
);

$dc['fields'] = array_merge($dc['fields'], $arrFields);

class tl_news_archive_plus extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	/**
	 * Get all news archives and return them as array
	 *
	 * @return array
	 */
	public function getSubNewsArchives(DataContainer $dc)
	{
		$arrArchives = array();

		if (!$this->User->isAdmin && !is_array($this->User->news))
		{
			return $arrArchives;
		}

		$objArchives = \NewsArchiveModel::findAll(array('order' => 'title'));

		while ($objArchives->next())
		{
			if ($this->User->hasAccess($objArchives->id, 'news'))
			{
				$arrArchives[$objArchives->id] = $objArchives->title;
			}
		}

		return $arrArchives;
	}

	public function getNewsPalettes(DataContainer $dc)
	{
		$arrOptions = array();

		\Controller::loadDataContainer('tl_news');

		$arrPalettes = $GLOBALS['TL_DCA']['tl_news']['palettes'];

		if (!is_array($arrPalettes)) {
			return $arrOptions;
		}

		foreach ($arrPalettes as $strName => $strPalette) {
			if (in_array($strName, array('__selector__', 'internal', 'external', 'default'))) {
				continue;
			}
			
			$arrOptions[$strName] = $strName;
		}

		return $arrOptions;
	}

	public function getRootPages(DataContainer $dc)
	{
		$arrOptions = array();

		$objPages = \PageModel::findBy('type', 'root');

		if ($objPages === null) {
			return $arrOptions;
		}

		return $objPages->fetchEach('title');
	}
}
