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

/**
 * Palettes
 */
$dc['palettes']['newsfilter'] = '
									{title_legend},name,headline,type;
									{template_legend},news_archives,news_filterTemplate,news_filterCategoryTemplate,news_filterShowSearch,news_filterShowCategories;
									{filter_legend},news_filterUseSearchIndex,news_filterFuzzySearch,news_filterSearchQueryType,news_filterNewsCategoryArchives,news_categoriesRoot,news_customCategories;
									{expert_legend:hide},guests,cssID,space';

$dc['palettes']['newslist_plus'] = '
                                    {title_legend},name,headline,type;
                                    {config_legend},news_archives,news_filterCategories,news_filterDefault,news_filterPreserve,news_archiveTitleAppendCategories,numberOfItems,news_featured,perPage,skipFirst;
                                    {template_legend:hide},news_metaFields,news_template,customTpl,news_showInModal,news_readerModule,news_filterModule;
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
                                    {template_legend:hide},news_metaFields,news_template,news_template_modal,customTpl,news_pdfJumpTo;
                                    {image_legend:hide},imgSize;
                                    {youtube_legend},youtube_template;
                                    {media_legend},media_template,media_posterSRC;
                                    {protected_legend:hide},protected;
                                    {expert_legend:hide},guests,cssID,space';

$dc['palettes']['__selector__'][] = 'news_archiveTitleAppendCategories';

/**
 * SubPalettes
 */

$dc['subpalettes']['news_archiveTitleAppendCategories'] = 'news_archiveTitleCategories';

/**
 * Fields
 */
$dc['fields'] = array_merge
(
	array(
		'news_filterTemplate'               => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_module']['news_filterTemplate'],
			'exclude'          => true,
			'inputType'        => 'select',
			'options_callback' => array('tl_module_news_plus', 'getFilterTemplates'),
			'reference'        => &$GLOBALS['TL_LANG']['tl_module'],
			'eval'             => array('includeBlankOption' => true, 'tl_class' => 'w50'),
			'sql'              => "varchar(64) NOT NULL default ''",
		),
		'news_filterCategoryTemplate'       => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_module']['news_filterCategoryTemplate'],
			'exclude'          => true,
			'inputType'        => 'select',
			'options_callback' => array('tl_module_news_plus', 'getFilterCategoriesTemplates'),
			'reference'        => &$GLOBALS['TL_LANG']['tl_module'],
			'eval'             => array('includeBlankOption' => true, 'tl_class' => 'w50'),
			'sql'              => "varchar(64) NOT NULL default ''",
		),
		'news_filterShowSearch'             => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_filterShowSearch'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'eval'      => array('tl_class' => 'w50'),
			'sql'       => "char(1) NOT NULL default ''",
		),
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
		'news_filterShowCategories'         => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_filterShowCategories'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'eval'      => array('tl_class' => 'w50'),
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
		'news_pdfJumpTo'                    => array
		(
			'label'      => &$GLOBALS['TL_LANG']['tl_module']['news_pdfJumpTo'],
			'exclude'    => true,
			'inputType'  => 'pageTree',
			'foreignKey' => 'tl_page.title',
			'eval'       => array('fieldType' => 'radio', 'tl_class' => 'w50 clr'),
			'sql'        => "int(10) unsigned NOT NULL default '0'",
			'relation'   => array('type' => 'belongsTo', 'load' => 'lazy'),
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
			'eval'       => array('mandatory'    => true,
								  'multiple'     => true,
								  'fieldType'    => 'checkbox',
								  'foreignTable' => 'tl_news_category',
								  'titleField'   => 'title',
								  'searchField'  => 'title',
								  'managerHref'  => 'do=news&table=tl_news_category',
			),
			'sql'        => "blob NULL",
		),
		'news_config' => array
		(
			'label'      => &$GLOBALS['TL_LANG']['tl_module']['news_config'],
			'inputType'  => 'select',
			'exclude'    => true,
			'foreignKey' => 'tl_news_config.title',
			'sql'        => "int(10) unsigned NOT NULL",
			'wizard'     => array
			(
				array('tl_module_news_plus', 'editNewsConfig'),
			),
		)
	),
	is_array($dc['fields']) ? $dc['fields'] : array()
);

$dc['fields']['news_archives']['options_callback'] = array('tl_module_news_plus', 'getNewsArchives');
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

	public function editNewsConfig(DataContainer $dc)
	{
		return ($dc->value < 1)
			? ''
			: ' <a href="contao/main.php?do=news&amp;table=tl_news_config_fe&amp;act=edit&amp;id=' . $dc->value . '&amp;popup=1&amp;nb=1&amp;rt=' . REQUEST_TOKEN
			  . '" title="' . sprintf(
				  specialchars($GLOBALS['TL_LANG']['tl_news']['editNewsConfig'][1]),
				  $dc->value
			  ) . '" style="padding-left:3px" onclick="Backend.openModalIframe({\'width\':768,\'title\':\'' . specialchars(
				  str_replace("'", "\\'", sprintf($GLOBALS['TL_LANG']['tl_news']['editNewsConfig'][1], $dc->value))
			  ) . '\',\'url\':this.href});return false">' . Image::getHtml(
				'alias.gif',
				$GLOBALS['TL_LANG']['tl_news']['editNewsConfig'][0],
				'style="vertical-align:top"'
			) . '</a>';
	}
}
