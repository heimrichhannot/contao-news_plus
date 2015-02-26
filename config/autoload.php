<?php


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
    // Classes
    'HeimrichHannot\NewsPlus\NewsPlus'              => 'system/modules/news_plus/classes/NewsPlus.php',
    // Modules
    'HeimrichHannot\NewsPlus\ModuleNewsPlus'        => 'system/modules/news_plus/modules/ModuleNewsPlus.php',
    'HeimrichHannot\NewsPlus\ModuleNewsListPlus'    => 'system/modules/news_plus/modules/ModuleNewsListPlus.php',
    'HeimrichHannot\NewsPlus\ModuleNewsFilter'    => 'system/modules/news_plus/modules/ModuleNewsFilter.php',
    // Models
    'HeimrichHannot\NewsPlus\NewsPlusModel'         => 'system/modules/news_plus/models/NewsPlusModel.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'form_newsfilter'                       => 'system/modules/news_plus/templates/form',
    'mod_newslist_plus'                     => 'system/modules/news_plus/templates/modules',
    'mod_newsreader_plus'                   => 'system/modules/news_plus/templates/modules',
    'mod_newsfilter'                        => 'system/modules/news_plus/templates/modules',
    'news_short_plus'                       => 'system/modules/news_plus/templates/news',
    'news_full_modal_plus'                  => 'system/modules/news_plus/templates/news',
    'news_full_modal_content_plus'          => 'system/modules/news_plus/templates/news',
));
