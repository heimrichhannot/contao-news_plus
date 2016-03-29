<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'HeimrichHannot',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Models
	'HeimrichHannot\NewsPlus\NewsPlusModel'        => 'system/modules/news_plus/models/NewsPlusModel.php',

	// Modules
	'HeimrichHannot\NewsPlus\ModuleNewsPlus'       => 'system/modules/news_plus/modules/ModuleNewsPlus.php',
	'HeimrichHannot\NewsPlus\ModuleNewsMenuPlus'   => 'system/modules/news_plus/modules/ModuleNewsMenuPlus.php',
	'HeimrichHannot\NewsPlus\ModuleNewsReaderPlus' => 'system/modules/news_plus/modules/ModuleNewsReaderPlus.php',
	'HeimrichHannot\NewsPlus\ModuleNewsFilter'     => 'system/modules/news_plus/modules/ModuleNewsFilter.php',
	'HeimrichHannot\NewsPlus\ModuleNewsListPlus'   => 'system/modules/news_plus/modules/ModuleNewsListPlus.php',

	// Classes
	'HeimrichHannot\NewsPlus\NewsPlusTagHelper'    => 'system/modules/news_plus/classes/NewsPlusTagHelper.php',
	'HeimrichHannot\NewsPlus\NewsPagination'       => 'system/modules/news_plus/classes/NewsPagination.php',
	'HeimrichHannot\NewsPlus\Hooks'                => 'system/modules/news_plus/classes/Hooks.php',
	'HeimrichHannot\NewsPlus\NewsPlusHelper'       => 'system/modules/news_plus/classes/NewsPlusHelper.php',
	'HeimrichHannot\NewsPlus\NewsFilterForm'       => 'system/modules/news_plus/classes/NewsFilterForm.php',
	'HeimrichHannot\NewsPlus\NewsPlus'             => 'system/modules/news_plus/classes/NewsPlus.php',
	'HeimrichHannot\NewsPlus\NewsFilterFormHelper' => 'system/modules/news_plus/classes/NewsFilterFormHelper.php',
	'HeimrichHannot\NewsPlus\Backend\News'         => 'system/modules/news_plus/classes/Backend/News.php',
	'HeimrichHannot\NewsPlus\Backend\Module'       => 'system/modules/news_plus/classes/Backend/Module.php',
	'HeimrichHannot\NewsPlus\NewsArticle'          => 'system/modules/news_plus/classes/NewsArticle.php',
	'HeimrichHannot\NewsPlus\NewsFilterRegistry'   => 'system/modules/news_plus/classes/NewsFilterRegistry.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'filter_cat_default'             => 'system/modules/news_plus/templates/filter',
	'filter_cat_multilevel'          => 'system/modules/news_plus/templates/filter',
	'newsnav_default'                => 'system/modules/news_plus/templates/navigation',
	'news_short_plus'                => 'system/modules/news_plus/templates/news',
	'news_full_modal_plus'           => 'system/modules/news_plus/templates/news',
	'news_latest_leisuretip'         => 'system/modules/news_plus/templates/news',
	'news_full_leisuretips'          => 'system/modules/news_plus/templates/news',
	'news_full_modal_content_plus'   => 'system/modules/news_plus/templates/news',
	'news_subnews_default'           => 'system/modules/news_plus/templates/news',
	'mod_news_modal'                 => 'system/modules/news_plus/templates/modules',
	'mod_newsreader_plus'            => 'system/modules/news_plus/templates/modules',
	'mod_newslist_plus'              => 'system/modules/news_plus/templates/modules',
	'mod_newsfilter'                 => 'system/modules/news_plus/templates/modules',
	'mod_news_modal_ajax'            => 'system/modules/news_plus/templates/modules',
	'form_newsfilter_cat_ml_link'    => 'system/modules/news_plus/templates/form',
	'form_newsfilter_search'         => 'system/modules/news_plus/templates/form',
	'form_newsfilter_cat_ml_submenu' => 'system/modules/news_plus/templates/form',
	'formhybrid_newsfilter_default'  => 'system/modules/news_plus/templates/form',
	'form_newsfilter_cat_option'     => 'system/modules/news_plus/templates/form',
	'block_modal_news'               => 'system/modules/news_plus/templates/block',
));
