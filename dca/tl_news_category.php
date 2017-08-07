<?php

$arrDca = &$GLOBALS['TL_DCA']['tl_news_category'];

/**
 * Palettes
 */
$arrDca['palettes']['default'] = str_replace('jumpTo', 'jumpTo,jumpToDetailsNote,jumpToDetails', $arrDca['palettes']['default']);

/**
 * Fields
 */
$arrFields = [
    'jumpToDetailsNote' => [
        'inputType' => 'explanation',
        'eval'      => [
            'text'     => &$GLOBALS['TL_LANG']['tl_news_category']['jumpToDetailsNote'],
            'class' => 'tl_info'
        ]
    ],
];

$arrDca['fields'] += $arrFields;

$arrDca['fields']['jumpToDetails'] = $GLOBALS['TL_DCA']['tl_news_category']['fields']['jumpTo'];

$arrDca['fields']['jumpToDetails']['label'] = &$GLOBALS['TL_LANG']['tl_news_category']['jumpToDetails'];