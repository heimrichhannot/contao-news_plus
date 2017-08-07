<?php

$arrDca = &$GLOBALS['TL_DCA']['tl_news'];

/**
 * Callbacks
 */
$arrDca['config']['onload_callback'][] = ['HeimrichHannot\NewsPlus\Backend\News', 'modifyDC'];

/**
 * Palettes
 */
$arrDca['palettes']['default'] = str_replace('categories', 'categories,primaryCategory', $arrDca['palettes']['default']);

/**
 * Fields
 */
$arrFields = [
    'primaryCategory' => [
        'label'                   => &$GLOBALS['TL_LANG']['tl_news']['primaryCategory'],
        'exclude'                 => true,
        'filter'                  => true,
        'inputType'               => 'treePicker',
        'foreignKey'              => 'tl_news_category.title',
        'eval'                    => array('fieldType'=>'radio', 'foreignTable'=>'tl_news_category', 'titleField'=>'title', 'searchField'=>'title', 'managerHref'=>'do=news&table=tl_news_category'),
        'sql'                     => "blob NULL"
    ],
    'orderEnclosureSRC' => [
        'label' => &$GLOBALS['TL_LANG']['tl_news']['orderEnclosureSRC'],
        'sql'   => "blob NULL"
    ],

];

$arrDca['fields'] = array_merge($arrDca['fields'], $arrFields);

$arrDca['fields']['enclosure']['eval']['orderField'] = 'orderEnclosureSRC';