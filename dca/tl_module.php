<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package news_plus
 * @author Mathias Arzberger <develop@pdir.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$dc = &$GLOBALS['TL_DCA']['tl_module'];

/**
 * Palettes
 */

$dc['palettes']['newsfilter'] = '
									{title_legend},name,headline,type;
									{template_legend},news_archives,news_filterTemplate,news_filterCategoryTemplate,news_filterShowSearch,news_filterShowCategories;
									{filter_legend},news_filterUseSearchIndex,news_filterFuzzySearch,news_filterSearchQueryType,news_filterNewsCategoryArchives,news_categoriesRoot,news_customCategories;
									{expert_legend:hide},guests,cssID,space';

$dc['palettes']['newslist_plus']    = '
                                    {title_legend},name,headline,type;
                                    {config_legend},news_archives,news_filterCategories,news_filterDefault,news_filterPreserve,numberOfItems,news_featured,perPage,skipFirst;
                                    {template_legend:hide},news_metaFields,news_template,customTpl,news_showInModal,news_readerModule,news_filterModule;
                                    {image_legend:hide},imgSize;
                                    {protected_legend:hide},protected;
                                    {expert_legend:hide},guests,cssID,space';

$dc['palettes']['newslist_highlight']    = '
                                    {title_legend},name,headline,type;
                                    {config_legend},news_archives,numberOfItems,news_featured,perPage,skipFirst;
                                    {template_legend:hide},news_metaFields,news_template,customTpl,news_showInModal,news_readerModule;
                                    {image_legend:hide},imgSize;
                                    {protected_legend:hide},protected;
                                    {expert_legend:hide},guests,cssID,space';

$dc['palettes']['newsreader_plus']  = '
                                    {title_legend},name,headline,type;
                                    {config_legend},news_archives;
                                    {showtags_legend},tag_filter,tag_ignore,news_showtags;
                                    {template_legend:hide},news_metaFields,news_template,news_template_modal,customTpl,news_pdfJumpTo;
                                    {image_legend:hide},imgSize;
                                    {protected_legend:hide},protected;
                                    {expert_legend:hide},guests,cssID,space';

/**
 * Fields
 */
$arrFields = array
(
    'news_filterTemplate'   => array
    (
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['news_filterTemplate'],
        'exclude'          => true,
        'inputType'        => 'select',
        'options_callback' => array('tl_module_news_plus', 'getFilterTemplates'),
        'reference'        => &$GLOBALS['TL_LANG']['tl_module'],
        'eval'             => array('includeBlankOption' => true, 'tl_class' => 'w50'),
        'sql'              => "varchar(64) NOT NULL default ''"
    ),
    'news_filterCategoryTemplate'   => array
    (
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['news_filterCategoryTemplate'],
        'exclude'          => true,
        'inputType'        => 'select',
        'options_callback' => array('tl_module_news_plus', 'getFilterCategoriesTemplates'),
        'reference'        => &$GLOBALS['TL_LANG']['tl_module'],
        'eval'             => array('includeBlankOption' => true, 'tl_class' => 'w50'),
        'sql'              => "varchar(64) NOT NULL default ''"
    ),
    'news_filterShowSearch'	=> array
    (
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_filterShowSearch'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'		=> array('tl_class' => 'w50'),
        'sql'       => "char(1) NOT NULL default ''",
    ),
    'news_filterUseSearchIndex'	=> array
    (
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_filterUseSearchIndex'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'		=> array('tl_class' => 'w50'),
        'sql'       => "char(1) NOT NULL default ''",
    ),
    'news_filterFuzzySearch'	=> array
    (
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_filterFuzzySearch'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'		=> array('tl_class' => 'w50'),
        'sql'       => "char(1) NOT NULL default ''",
    ),
    'news_filterSearchQueryType'	=> array
    (
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_filterSearchQueryType'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'		=> array('tl_class' => 'w100 clr'),
        'sql'       => "char(1) NOT NULL default ''",
    ),
    'news_filterShowCategories'	=> array
    (
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_filterShowCategories'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'		=> array('tl_class' => 'w50'),
        'sql'       => "char(1) NOT NULL default ''",
    ),
	'news_filterNewsCategoryArchives' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['news_filterNewsCategoryArchives'],
		'exclude'                 => true,
		'inputType'               => 'checkbox',
		'options_callback'        => array('tl_module_news', 'getNewsArchives'),
		'eval'                    => array('multiple'=>true),
		'sql'                     => "blob NULL"
	),
    'news_readerModule' => array
    (
        'label'                   => &$GLOBALS['TL_LANG']['tl_module']['news_readerModule'],
        'exclude'                 => true,
        'inputType'               => 'select',
        'options_callback'        => array('tl_module_news_plus', 'getReaderModules'),
        'reference'               => &$GLOBALS['TL_LANG']['tl_module'],
        'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
        'sql'                     => "int(10) unsigned NOT NULL default '0'"
    ),
	'news_template_modal' => array
    (
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['news_template_modal'],
        'exclude'          => true,
        'inputType'        => 'select',
        'options_callback' => array('tl_module_news_plus', 'getNewsModalTemplates'),
        'eval'             => array('tl_class' => 'w50', 'includeBlankOption' => true),
        'sql'              => "varchar(64) NOT NULL default ''"
    ),
	'news_showInModal'	=> array
    (
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_showInModal'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'		=> array('tl_class' => 'w50 m12'),
        'sql'       => "char(1) NOT NULL default ''",
    ),
    'news_pdfJumpTo' => array
    (
        'label'                   => &$GLOBALS['TL_LANG']['tl_module']['news_pdfJumpTo'],
        'exclude'                 => true,
        'inputType'               => 'pageTree',
        'foreignKey'              => 'tl_page.title',
        'eval'                    => array('fieldType'=>'radio', 'tl_class'=>'w50 clr'),
        'sql'                     => "int(10) unsigned NOT NULL default '0'",
        'relation'                => array('type'=>'belongsTo', 'load'=>'lazy')
    ),
	'news_filterModule' => array
	(
		'label'            => &$GLOBALS['TL_LANG']['tl_module']['news_filterModule'],
		'exclude'          => true,
		'inputType'        => 'select',
		'options_callback' => array('tl_module_news_plus', 'getFilterModules'),
		'reference'        => &$GLOBALS['TL_LANG']['tl_module'],
		'eval'             => array('includeBlankOption' => true, 'tl_class' => 'w50'),
		'sql'              => "int(10) unsigned NOT NULL default '0'"
	)
);

$dc['fields'] = array_merge($dc['fields'], $arrFields);

/**
 * Class tl_module_news_plus
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @author     Mathias Arzberger <develop@pdir.de>
 * @package    news_plus
 */
class tl_module_news_plus extends Backend
{

	/**
	 * Get all news filter modules and return them as array
	 *
	 * @return array
	 */
	public function getFilterModules()
	{
		$arrModules = array();
		$objModules = $this->Database->execute(
			"SELECT m.id, m.name, t.name AS theme FROM tl_module m LEFT JOIN tl_theme t ON m.pid=t.id WHERE m.type='newsfilter' ORDER BY t.name, m.name"
		);

		while ($objModules->next()) {
			$arrModules[$objModules->theme][$objModules->id] = $objModules->name . ' (ID ' . $objModules->id . ')';
		}

		return $arrModules;
	}

    /**
     * Return all filter modules as array
     * @return array
     */
    public function getFilterTemplates()
    {
        return $this->getTemplateGroup('form_news');
    }

    /**
     * Return all filter modules as array
     * @return array
     */
    public function getFilterCategoriesTemplates()
    {
        return $this->getTemplateGroup('filter_cat');
    }


    /**
     * Return all news modal templates as array
     * @return array
     */
    public function getNewsModalTemplates()
    {
        return $this->getTemplateGroup('news_');
    }

    /**
     * Get all news reader modules and return them as array
     * @return array
     */
    public function getReaderModules()
    {
        $arrModules = array();
        $objModules = $this->Database->execute("SELECT m.id, m.name, t.name AS theme FROM tl_module m LEFT JOIN tl_theme t ON m.pid=t.id WHERE m.type LIKE 'newsreader%' ORDER BY t.name, m.name");

        while ($objModules->next())
        {
            $arrModules[$objModules->theme][$objModules->id] = $objModules->name . ' (ID ' . $objModules->id . ')';
        }

        return $arrModules;
    }
}
