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

array_insert(
    $GLOBALS['FE_MOD']['news'],
    5,
    array(
        'newsarchive_plus' => 'HeimrichHannot\NewsPlus\ModuleNewsArchive',
    )
);


array_insert(
    $GLOBALS['FE_MOD']['news'],
    1,
    array(
        'newslist_map' => 'HeimrichHannot\NewsPlus\ModuleNewsListMap',
    )
);


array_insert(
    $GLOBALS['FE_MOD']['news'],
    7,
    array(
        'newslist_highlight' => 'HeimrichHannot\NewsPlus\ModuleNewsListHighlight',
    )
);

array_insert(
    $GLOBALS['FE_MOD']['news'],
    8,
    array(
        'newsfilter' => 'HeimrichHannot\NewsPlus\ModuleNewsFilter',
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

/**
 * Javascript
 */
if (TL_MODE == 'FE') {
	$GLOBALS['TL_JAVASCRIPT']['infinitescroll'] = '/system/modules/news_plus/assets/js/jscroll/jquery.jscroll.min.js';
    $GLOBALS['TL_JAVASCRIPT']['newsplus'] = '/system/modules/news_plus/assets/js/jquery.newsplus.js';
}

if (TL_MODE == 'BE')
{
    $GLOBALS['TL_JAVASCRIPT']['be_news_plus'] = '/system/modules/news_plus/assets/js/be_news_plus.min.js|static';
}

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['parseArticles'][] = array('HeimrichHannot\NewsPlus\Hooks', 'parseArticlesHook');
