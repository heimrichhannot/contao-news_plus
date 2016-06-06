<?php

/**
 * Front end modules
 */
array_insert(
	$GLOBALS['FE_MOD']['news'],
	1,
	array(
		'newslist_plus' => 'HeimrichHannot\NewsPlus\ModuleNewsListPlus',
	)
);

array_insert(
	$GLOBALS['FE_MOD']['news'],
	3,
	array(
		'newsreader_plus' => 'HeimrichHannot\NewsPlus\ModuleNewsReaderPlus',
	)
);

array_insert(
	$GLOBALS['FE_MOD']['news'],
	6,
	array(
		'newsmenu_plus' => 'HeimrichHannot\NewsPlus\ModuleNewsMenuPlus',
	)
);

$GLOBALS['FE_MOD']['news']['newsfilter'] = 'HeimrichHannot\NewsPlus\ModuleNewsFilter';


/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_news'] = 'HeimrichHannot\NewsPlus\NewsPlusModel';

/**
 * Constants
 */
define('NEWSPLUS_SESSION_NEWS_IDS', 'NEWS_PLUS_NEWS_IDS');
define('NEWSPLUS_SESSION_NEWS_FILTER', 'NEWSPLUS_SESSION_NEWS_FILTER');
define('NEWSPLUS_FILTER_SLIDER_DEFAULT_MIN', 0);
define('NEWSPLUS_FILTER_SLIDER_DEFAULT_MAX', 100);


/**
 * Javascript
 */
if (TL_MODE == 'FE') {
	$GLOBALS['TL_JAVASCRIPT']['infinitescroll'] = '/system/modules/news_plus/assets/js/jscroll/jquery.jscroll.min.js';
	$GLOBALS['TL_JAVASCRIPT']['newsplus']       = '/system/modules/news_plus/assets/js/jquery.newsplus.js';
}

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['parseArticles'][] = array('HeimrichHannot\NewsPlus\Hooks', 'parseArticlesHook');