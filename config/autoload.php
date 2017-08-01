<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
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
	// Modules
	'HeimrichHannot\NewsPlus\ModuleNewsPlus'          => 'system/modules/news_plus/modules/ModuleNewsPlus.php',
	'HeimrichHannot\NewsPlus\ModuleMemberNewsList'    => 'system/modules/news_plus/modules/ModuleMemberNewsList.php',
	'HeimrichHannot\NewsPlus\ModuleNewsMenuPlus'      => 'system/modules/news_plus/modules/ModuleNewsMenuPlus.php',
	'HeimrichHannot\NewsPlus\ModuleNewsReaderPlus'    => 'system/modules/news_plus/modules/ModuleNewsReaderPlus.php',
	'HeimrichHannot\NewsPlus\ModuleNewsArchive'       => 'system/modules/news_plus/modules/ModuleNewsArchive.php',
	'HeimrichHannot\NewsPlus\ModuleNewsFilter'        => 'system/modules/news_plus/modules/ModuleNewsFilter.php',
	'HeimrichHannot\NewsPlus\ModuleNewsListHighlight' => 'system/modules/news_plus/modules/ModuleNewsListHighlight.php',
	'HeimrichHannot\NewsPlus\ModuleNewsListPlus'      => 'system/modules/news_plus/modules/ModuleNewsListPlus.php',

	// Models
	'HeimrichHannot\NewsPlus\NewsPlusModel'           => 'system/modules/news_plus/models/NewsPlusModel.php',

	// Classes
	'HeimrichHannot\NewsPlus\NewsPlusHelper'          => 'system/modules/news_plus/classes/NewsPlusHelper.php',
	'HeimrichHannot\NewsPlus\NewsPlusTagHelper'       => 'system/modules/news_plus/classes/NewsPlusTagHelper.php',
	'HeimrichHannot\NewsPlus\NewsPlus'                => 'system/modules/news_plus/classes/NewsPlus.php',
	'HeimrichHannot\NewsPlus\Hooks'                   => 'system/modules/news_plus/classes/Hooks.php',
	'HeimrichHannot\NewsPlus\Backend\News'            => 'system/modules/news_plus/classes/backend/News.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_newsfilter'                 => 'system/modules/news_plus/templates/modules',
	'mod_newsreader_plus'            => 'system/modules/news_plus/templates/modules',
	'mod_news_modal_ajax'            => 'system/modules/news_plus/templates/modules',
	'mod_newslist_plus'              => 'system/modules/news_plus/templates/modules',
	'mod_membernewslist'             => 'system/modules/news_plus/templates/modules',
	'mod_news_modal'                 => 'system/modules/news_plus/templates/modules',
	'form_newsfilter_cat_option'     => 'system/modules/news_plus/templates/form',
	'form_newsfilter_search'         => 'system/modules/news_plus/templates/form',
	'form_newsfilter_cat_ml_submenu' => 'system/modules/news_plus/templates/form',
	'form_newsfilter_cat_ml_link'    => 'system/modules/news_plus/templates/form',
	'filter_cat_multilevel'          => 'system/modules/news_plus/templates/filter',
	'filter_cat_default'             => 'system/modules/news_plus/templates/filter',
	'infinite_pagination'            => 'system/modules/news_plus/templates/pagination',
	'navigation_arrows'              => 'system/modules/news_plus/templates/navigation',
	'block_modal_news'               => 'system/modules/news_plus/templates/block',
	'news_full_modal_content_plus'   => 'system/modules/news_plus/templates/news',
	'news_short_plus'                => 'system/modules/news_plus/templates/news',
	'news_full_modal_plus'           => 'system/modules/news_plus/templates/news',
));
