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
        'newslist_highlight'    => 'HeimrichHannot\NewsPlus\ModuleNewsListHighlight',
        'newsfilter'            => 'HeimrichHannot\NewsPlus\ModuleNewsFilter',
        'newsreader_plus'       => 'HeimrichHannot\NewsPlus\ModuleNewsReaderPlus',
	)
);

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_news']     = 'HeimrichHannot\NewsPlus\NewsPlusModel';

/**
 * Constants
 */
define('NEWSPLUS_SESSION_NEWS_IDS', 'NEWS_PLUS_NEWS_IDS');
// define('NEWSPLUS_SESSION_URL_PARAM', 'NEWS_PLUS_URL_PARAM');

/**
 * Javascript
 */
if (TL_MODE == 'FE') {
    $GLOBALS['TL_JAVASCRIPT']['newsplus'] = '/system/modules/news_plus/assets/js/jquery.newsplus.js';
//	$GLOBALS['TL_JAVASCRIPT']['newsplus'] = '/system/modules/news_plus/assets/js/isotope.js';
}

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['parseArticles'][] = array('HeimrichHannot\NewsPlus\Hooks', 'parseArticlesHook');