<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @package news_plus
 * @author  Mathias Arzberger <develop@pdir.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$dc = &$GLOBALS['TL_DCA']['tl_module'];

$dc['config']['onload_callback'][] = array('tl_module_news_plus', 'initDCA');

/**
 * Palettes
 */
$dc['palettes']['newsfilter'] = '
									{title_legend},name,headline,type;
									{config_legend},news_archives;
									{template_legend},formHybridPalette,formHybridEditable,formHybridTemplate,formHybridCustomSubTemplates;
									{filter_legend},news_filterUseSearchIndex,news_filterFuzzySearch,news_filterSearchQueryType,news_filterNewsCategoryArchives,news_categoriesRoot,news_customCategories;
									{expert_legend:hide},guests,cssID,space';

$dc['palettes']['newslist_plus'] = '
                                    {title_legend},name,headline,type;
                                    {config_legend},news_archives,news_filterCategories,news_filterDefault,news_filterDefaultExclude,news_filterPreserve,news_archiveTitleAppendCategories,numberOfItems,news_featured,perPage,skipFirst;
                                    {template_legend:hide},news_metaFields,news_template,customTpl,news_showInModal,news_readerModule,news_filterModule,addListGrid,news_pagination_overwrite,news_empty_overwrite,news_useInfiniteScroll;
                                    {image_legend:hide},imgSize;
                                    {youtube_legend},youtube_template;
                                    {media_legend},media_template,media_posterSRC;
                                    {protected_legend:hide},protected;
                                    {expert_legend:hide},guests,cssID,space';

$dc['palettes']['newslist_highlight'] = '
                                    {title_legend},name,headline,type;
                                    {config_legend},news_archives,numberOfItems,news_featured,perPage,skipFirst;
                                    {template_legend:hide},news_metaFields,news_template,customTpl,news_showInModal,news_readerModule;
                                    {image_legend:hide},imgSize;
                                    {youtube_legend},youtube_template;
                                    {media_legend},media_template,media_posterSRC;
                                    {protected_legend:hide},protected;
                                    {expert_legend:hide},guests,cssID,space';

$dc['palettes']['newsreader_plus'] = '
                                    {title_legend},name,headline,type;
                                    {config_legend},news_archives;
                                    {showtags_legend},tag_filter,tag_ignore,news_showtags;
                                    {template_legend:hide},news_metaFields,news_template,news_template_modal,customTpl;
                                    {navigation_legend:hide},news_addNavigation;
                                    {image_legend:hide},imgSize;
                                    {share_legend},addShare;
                                    {youtube_legend},youtube_template,autoplay;
                                    {media_legend},media_template,media_posterSRC;
                                    {protected_legend:hide},protected;
                                    {expert_legend:hide},guests,cssID,space';

$dc['palettes']['newsmenu_plus'] = '
									{title_legend},name,headline,type;
									{config_legend},news_archives,news_showQuantity,news_jumpToCurrent,news_format,news_startDay,news_order;
									{redirect_legend},jumpTo;
									{template_legend:hide},customTpl;
									{protected_legend:hide},protected;
									{expert_legend:hide},guests,cssID,space';

$dc['palettes']['__selector__'][] = 'news_archiveTitleAppendCategories';
$dc['palettes']['__selector__'][] = 'news_addNavigation';
$dc['palettes']['__selector__'][] = 'news_pagination_overwrite';
$dc['palettes']['__selector__'][] = 'news_empty_overwrite';
$dc['palettes']['__selector__'][] = 'news_useInfiniteScroll';
$dc['palettes']['__selector__'][] = 'news_changeTriggerText';


/**
 * SubPalettes
 */

$dc['subpalettes']['news_archiveTitleAppendCategories'] = 'news_archiveTitleCategories';
$dc['subpalettes']['news_addNavigation']                = 'news_navigation_infinite,news_navigation_template';
$dc['subpalettes']['news_pagination_overwrite']         = 'pagination_template,pagination_hash';
$dc['subpalettes']['news_empty_overwrite']              = 'news_empty_label';
$dc['subpalettes']['news_useInfiniteScroll']            = 'news_useAutoTrigger, news_changeTriggerText';
$dc['subpalettes']['news_changeTriggerText']            = 'news_triggerText';

/**
 * Fields
 */
$dc['fields'] = array_merge
(
	array(
		'news_filterUseSearchIndex'         => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_filterUseSearchIndex'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'eval'      => array('tl_class' => 'w50'),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'news_filterFuzzySearch'            => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_filterFuzzySearch'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'eval'      => array('tl_class' => 'w50'),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'news_filterSearchQueryType'        => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_filterSearchQueryType'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'eval'      => array('tl_class' => 'w100 clr'),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'news_filterNewsCategoryArchives'   => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_module']['news_filterNewsCategoryArchives'],
			'exclude'          => true,
			'inputType'        => 'checkbox',
			'options_callback' => array('tl_module_news', 'getNewsArchives'),
			'eval'             => array('multiple' => true),
			'sql'              => "blob NULL",
		),
		'news_readerModule'                 => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_module']['news_readerModule'],
			'exclude'          => true,
			'inputType'        => 'select',
			'options_callback' => array('tl_module_news_plus', 'getReaderModules'),
			'reference'        => &$GLOBALS['TL_LANG']['tl_module'],
			'eval'             => array('includeBlankOption' => true, 'tl_class' => 'w50'),
			'sql'              => "int(10) unsigned NOT NULL default '0'",
		),
		'news_template_modal'               => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_module']['news_template_modal'],
			'exclude'          => true,
			'inputType'        => 'select',
			'options_callback' => array('tl_module_news_plus', 'getNewsModalTemplates'),
			'eval'             => array('tl_class' => 'w50', 'includeBlankOption' => true),
			'sql'              => "varchar(64) NOT NULL default ''",
		),
		'news_showInModal'                  => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_showInModal'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'eval'      => array('tl_class' => 'w50 m12'),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'news_filterModule'                 => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_module']['news_filterModule'],
			'exclude'          => true,
			'inputType'        => 'select',
			'options_callback' => array('tl_module_news_plus', 'getFilterModules'),
			'reference'        => &$GLOBALS['TL_LANG']['tl_module'],
			'eval'             => array('includeBlankOption' => true, 'tl_class' => 'w50'),
			'sql'              => "int(10) unsigned NOT NULL default '0'",
		),
		'news_archiveTitleAppendCategories' => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_archiveTitleAppendCategories'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'eval'      => array('tl_class' => 'clr', 'submitOnChange' => true),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'news_archiveTitleCategories'       => array
		(
			'label'      => &$GLOBALS['TL_LANG']['tl_module']['news_archiveTitleCategories'],
			'exclude'    => true,
			'inputType'  => 'treePicker',
			'foreignKey' => 'tl_news_category.title',
			'eval'       => array(
				'mandatory'    => true,
				'multiple'     => true,
				'fieldType'    => 'checkbox',
				'foreignTable' => 'tl_news_category',
				'titleField'   => 'title',
				'searchField'  => 'title',
				'managerHref'  => 'do=news&table=tl_news_category',
			),
			'sql'        => "blob NULL",
		),

		'news_filterDefaultExclude' => array
		(
			'label'      => &$GLOBALS['TL_LANG']['tl_module']['news_filterDefaultExclude'],
			'exclude'    => true,
			'inputType'  => 'treePicker',
			'foreignKey' => 'tl_news_category.title',
			'eval'       => array(
				'multiple'     => true,
				'fieldType'    => 'checkbox',
				'foreignTable' => 'tl_news_category',
				'titleField'   => 'title',
				'searchField'  => 'title',
				'managerHref'  => 'do=news&table=tl_news_category',
				'tl_class'     => 'clr',
			),
			'sql'        => "blob NULL",
		),
		'news_addNavigation'        => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_addNavigation'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'eval'      => array('tl_class' => 'clr', 'submitOnChange' => true),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'news_navigation_template'  => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_module']['news_navigation_template'],
			'exclude'          => true,
			'default'          => 'newsnav_default',
			'inputType'        => 'select',
			'options_callback' => array('tl_module_news_plus', 'getNewsNavigationTemplates'),
			'eval'             => array('tl_class' => 'w50', 'includeBlankOption' => true),
			'sql'              => "varchar(64) NOT NULL default ''",
		),
		'news_navigation_infinite'  => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_navigation_infinite'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'eval'      => array('tl_class' => 'clr'),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'news_pagination_overwrite' => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_pagination_overwrite'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'eval'      => array('tl_class' => 'clr', 'submitOnChange' => true),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'pagination_template'       => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_module']['pagination_template'],
			'exclude'          => true,
			'default'          => 'pagination',
			'inputType'        => 'select',
			'options_callback' => array('tl_module_news_plus', 'getPaginationTemplates'),
			'eval'             => array('tl_class' => 'w50', 'includeBlankOption' => true),
			'sql'              => "varchar(64) NOT NULL default ''",
		),
		'pagination_hash'           => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_module']['pagination_hash'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('tl_class' => 'w50'),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'news_empty_overwrite'      => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_empty_overwrite'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'eval'      => array('tl_class' => 'clr', 'submitOnChange' => true),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'news_empty_label'          => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_module']['news_empty_label'],
			'exclude'          => true,
			'inputType'        => 'select',
			'options_callback' => array('HeimrichHannot\NewsPlus\Backend\Module', 'getEmptyLabel'),
			'eval'             => array('tl_class' => 'w50 clr', 'mandatory' => true),
			'sql'              => "varchar(255) NOT NULL default ''",
		),
		'news_useInfiniteScroll'    => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_useInfiniteScroll'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'eval'      => array('tl_class' => 'clr', 'submitOnChange' => true),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'news_useAutoTrigger'       => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_useAutoTrigger'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'eval'      => array('tl_class' => 'w50'),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'news_changeTriggerText'    => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_changeTriggerText'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'eval'      => array('tl_class' => 'w50', 'submitOnChange' => true),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'news_triggerText'          => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_triggerText'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('tl_class' => 'w50'),
			'sql'       => "varchar(64) NOT NULL default ''",
		),
	),
	is_array($dc['fields']) ? $dc['fields'] : array()
);

$dc['fields']['news_archives']['options_callback']     = array('tl_module_news_plus', 'getNewsArchives');
$dc['fields']['news_readerModule']['options_callback'] = array('tl_module_news_plus', 'getReaderModules');

/**
 * Class tl_module_news_plus
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @author     Mathias Arzberger <develop@pdir.de>
 * @package    news_plus
 */
class tl_module_news_plus extends Backend
{


	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}


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
	 *
	 * @return array
	 */
	public function getFilterTemplates()
	{
		return $this->getTemplateGroup('form_news');
	}

	/**
	 * Return all pagintation templates as array
	 *
	 * @return array
	 */
	public function getPaginationTemplates()
	{
		return array_merge(array('pagination'), $this->getTemplateGroup('pagination_'));
	}

	/**
	 * Return all filter modules as array
	 *
	 * @return array
	 */
	public function getFilterCategoriesTemplates()
	{
		return $this->getTemplateGroup('filter_cat');
	}


	/**
	 * Return all news modal templates as array
	 *
	 * @return array
	 */
	public function getNewsModalTemplates()
	{
		return $this->getTemplateGroup('news_');
	}


	/**
	 * Return all news navigation templates as array
	 *
	 * @return array
	 */
	public function getNewsNavigationTemplates()
	{
		return $this->getTemplateGroup('newsnav_');
	}

	/**
	 * Get all news reader modules and return them as array
	 *
	 * @return array
	 */
	public function getReaderModules()
	{
		$arrModules = array();
		$objModules = $this->Database->execute(
			"SELECT m.id, m.name, t.name AS theme FROM tl_module m LEFT JOIN tl_theme t ON m.pid=t.id WHERE m.type LIKE 'newsreader%' ORDER BY t.name, m.name"
		);

		while ($objModules->next()) {
			$arrModules[$objModules->theme][$objModules->id] = $objModules->name . ' (ID ' . $objModules->id . ')';
		}

		return $arrModules;
	}

	/**
	 * Get all news archives with their root affiliation and return them as array
	 *
	 * @return array
	 */
	public function getNewsArchives()
	{

		if (!$this->User->isAdmin && !is_array($this->User->news)) {
			return array();
		}

		$arrArchives = array();
		$objArchives = $this->Database->execute("SELECT id, title, root FROM tl_news_archive ORDER BY root, title");

		while ($objArchives->next()) {
			if ($this->User->hasAccess($objArchives->id, 'news')) {
				$strTitle = $objArchives->title;

				if (($objRoot = \PageModel::findByPk($objArchives->root)) !== null) {
					$strTitle .= ' <strong> [' . $objRoot->title . '] </strong>';
				}

				$arrArchives[$objArchives->id] = $strTitle;
			}
		}
		
		return $arrArchives;
	}

	public function initDCA(\DataContainer $dc)
	{
		$objModule = \ModuleModel::findByPk($dc->id);

		if ($objModule === null || $objModule->type != 'newsfilter') {
			return;
		}

		$this->setNewsFilterDefaults($objModule);
		$objModule->save();
	}

	private function setNewsFilterDefaults(&$objModule)
	{
		$objModule->formHybridDataContainer = 'tl_newsfilter';

		if ($objModule == '') {
			$objModule->formHybridPalette  = 'default';
			$objModule->formhybridTemplate = 'formhybrid_newsfilter_default';
		}
	}

}
