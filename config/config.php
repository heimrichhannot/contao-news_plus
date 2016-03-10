<?php

/**
 * Front end modules
 */
array_insert($GLOBALS['FE_MOD']['news'], 1, array(
   'newslist_plus'         => 'HeimrichHannot\NewsPlus\ModuleNewsListPlus',
   )
);

array_insert($GLOBALS['FE_MOD']['news'], 3, array(
   'newsreader_plus'       => 'HeimrichHannot\NewsPlus\ModuleNewsReaderPlus',
   )
);

array_insert($GLOBALS['FE_MOD']['news'], 6, array(
   'newsmenu_plus'       => 'HeimrichHannot\NewsPlus\ModuleNewsMenuPlus',
   )
);

array_insert($GLOBALS['FE_MOD']['news'], count($GLOBALS['FE_MOD']['news']), array(
   'newsfilter'            => 'HeimrichHannot\NewsPlus\ModuleNewsFilter',
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
define('NEWSPLUS_SESSION_NEWS_FILTER', 'NEWSPLUS_SESSION_NEWS_FILTER');


/**
 * Javascript
 */
if (TL_MODE == 'FE') {
    $GLOBALS['TL_JAVASCRIPT']['newsplus'] = '/system/modules/news_plus/assets/js/jquery.newsplus.js';
}

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['parseArticles'][] = array('HeimrichHannot\NewsPlus\Hooks', 'parseArticlesHook');