<?php

/**
 * Back end modules
 */


/**
 * Frontend modules
 */
array_insert($GLOBALS['FE_MOD']['news'], 2, array
	(
		'newslist_plus'         => 'HeimrichHannot\NewsPlus\ModuleNewsListPlus',
        'newslist_highlight'   => 'HeimrichHannot\NewsPlus\ModuleNewsListHighlight',
        'newsfilter'            => 'HeimrichHannot\NewsPlus\ModuleNewsFilter',
        'newsreader_plus'       => 'HeimrichHannot\NewsPlus\ModuleNewsReaderPlus',
	)
);

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_news']     = 'HeimrichHannot\NewsPlus\NewsPlusModel';



/**
 * Javascript
 */
if (TL_MODE == 'FE') {
    $GLOBALS['TL_JAVASCRIPT']['calendarplus'] = '/system/modules/news_plus/assets/js/jquery.newsplus.js';
//	$GLOBALS['TL_JAVASCRIPT']['newsplus'] = '/system/modules/news_plus/assets/js/isotope.js';
}