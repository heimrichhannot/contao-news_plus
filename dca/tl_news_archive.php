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

\System::loadLanguageFile('tl_news');

$arrDca = &$GLOBALS['TL_DCA']['tl_news_archive'];

/**
 * Config
 */
$arrDca['list']['sorting']['fields'] = ['root', 'title'];

/**
 * Palettes
 */
$arrDca['palettes']['__selector__'][] = 'addDummyImage';
$arrDca['palettes']['__selector__'][] = 'limitInputCharacterLength';
$arrDca['palettes']['default']        = str_replace('title', 'title,displayTitle', $arrDca['palettes']['default']);
$arrDca['palettes']['default']        = str_replace('jumpTo;', 'jumpTo;{root_legend},root;', $arrDca['palettes']['default']);
$arrDca['palettes']['default']        = str_replace('jumpTo;', 'jumpTo;{image_legend},addDummyImage;', $arrDca['palettes']['default']);
$arrDca['palettes']['default']        = str_replace('jumpTo;', 'jumpTo;{input_legend},limitInputCharacterLength;', $arrDca['palettes']['default']);

/**
 * Subpalettes
 */
$arrDca['subpalettes']['addDummyImage']             = 'dummyImageSingleSRC';
$arrDca['subpalettes']['limitInputCharacterLength'] = 'inputCharacterLengths';

$arrFields = [
    'displayTitle'              => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['displayTitle'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'text',
        'eval'      => ['maxlength' => 255],
        'sql'       => "varchar(255) NOT NULL default ''",
    ],
    'root'                      => [
        'label'            => &$GLOBALS['TL_LANG']['tl_news_archive']['root'],
        'inputType'        => 'select',
        'options_callback' => ['tl_news_archive_plus', 'getRootPages'],
        'eval'             => ['includeBlankOption' => true],
        'sql'              => "int(10) unsigned NOT NULL default '0'",
    ],
    'addDummyImage'             => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['addDummyImage'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'dummyImageSingleSRC'       => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['dummyImageSingleSRC'],
        'exclude'   => true,
        'inputType' => 'fileTree',
        'eval'      => ['filesOnly' => true, 'fieldType' => 'radio', 'mandatory' => true, 'tl_class' => 'clr'],
        'sql'       => "binary(16) NULL",
    ],
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
];

$arrDca['fields'] = array_merge($arrDca['fields'], $arrFields);

class tl_news_archive_plus extends \Backend
{
    public function getRootPages(\DataContainer $arrDca)
    {
        $arrOptions = [];

        $objPages = \PageModel::findBy('type', 'root');

        if ($objPages === null)
        {
            return $arrOptions;
        }

        return $objPages->fetchEach('title');
    }
}