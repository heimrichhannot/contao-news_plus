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

$arrDca = &$GLOBALS['TL_DCA']['tl_module'];

/**
 * Palettes
 */
$arrDca['palettes']['newsfilter'] = '
									{title_legend},name,headline,type;
									{template_legend},news_archives,news_filterTemplate,news_filterCategoryTemplate,news_filterShowSearch,news_filterShowCategories;
									{filter_legend},news_filterUseSearchIndex,news_filterFuzzySearch,news_filterSearchQueryType,news_filterNewsCategoryArchives,news_categoriesRoot,news_customCategories;
									{expert_legend:hide},guests,cssID,space';

$arrDca['palettes']['newslist_plus'] = '
                                    {title_legend},name,headline,type;
                                    {config_legend},news_archives,jumpToDetails,news_filterCategories,news_filterDefault,news_filterPreserve,news_archiveTitleAppendCategories,numberOfItems,news_featured,perPage,skipFirst;
                                    {showtags_legend},news_showtags;
                                    {template_legend:hide},news_metaFields,news_template,customTpl,news_showInModal,news_readerModule,news_filterModule,addListGrid, news_useInfiniteScroll;
                                    {image_legend:hide},imgSize;
                                    {youtube_legend},youtube_template;
                                    {media_legend},media_template,media_posterSRC;
                                    {protected_legend:hide},protected;
                                    {expert_legend:hide},guests,cssID,space';

$arrDca['palettes']['newslist_highlight'] = '
                                    {title_legend},name,headline,type;
                                    {config_legend},news_archives,numberOfItems,news_featured,perPage,skipFirst;
                                    {template_legend:hide},news_metaFields,news_template,customTpl,news_showInModal,news_readerModule;
                                    {image_legend:hide},imgSize;
                                    {youtube_legend},youtube_template;
                                    {media_legend},media_template,media_posterSRC;
                                    {protected_legend:hide},protected;
                                    {expert_legend:hide},guests,cssID,space';

$arrDca['palettes']['newsreader_plus'] = '
                                    {title_legend},name,headline,type;
                                    {config_legend},news_archives;
                                    {showtags_legend},tag_filter,tag_ignore,news_showtags;
                                    {template_legend:hide},news_metaFields,news_template,news_template_modal,customTpl;
                                    {image_legend:hide},imgSize;
                                    {share_legend},addShare;
                                    {youtube_legend},youtube_template;
                                    {media_legend},media_template,media_posterSRC;
                                    {protected_legend:hide},protected;
                                    {expert_legend:hide},guests,cssID,space';

$arrDca['palettes']['newsarchive_plus'] = '
                                    {title_legend},name,headline,type;
                                    {config_legend},news_archives,news_jumpToCurrent,news_readerModule,perPage,news_format,news_format_reference;
                                    {template_legend:hide},news_metaFields,news_template,customTpl;
                                    {image_legend:hide},imgSize;
                                    {protected_legend:hide},protected;
                                    {expert_legend:hide},guests,cssID,space';

$arrDca['palettes']['newsmenu_plus'] = '
									{title_legend},name,headline,type;
									{config_legend},news_archives,news_showQuantity,news_jumpToCurrent,news_format,news_format_reference,news_startDay,news_order;
									{redirect_legend},jumpTo;
									{template_legend:hide},customTpl;
									{protected_legend:hide},protected;
									{expert_legend:hide},guests,cssID,space';

$arrDca['palettes']['membernewslist'] = $arrDca['palettes']['newslist'];

$arrDca['palettes']['__selector__'][] = 'news_archiveTitleAppendCategories';
$arrDca['palettes']['__selector__'][] = 'news_useInfiniteScroll';
$arrDca['palettes']['__selector__'][] = 'news_changeTriggerText';
/**
 * SubPalettes
 */

$arrDca['subpalettes']['news_archiveTitleAppendCategories'] = 'news_archiveTitleCategories';
$arrDca['subpalettes']['news_useInfiniteScroll']            = 'news_useAutoTrigger, news_changeTriggerText';
$arrDca['subpalettes']['news_changeTriggerText']            = 'news_triggerText';
/**
 * Fields
 */
$arrDca['fields'] = array_merge(
    [
        'news_format_reference'             => [
            'exclude'   => true,
            'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_format_reference'],
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'date', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql'       => "varchar(10) NOT NULL default ''",
        ],
        'news_filterTemplate'               => [
            'label'            => &$GLOBALS['TL_LANG']['tl_module']['news_filterTemplate'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => ['tl_module_news_plus', 'getFilterTemplates'],
            'reference'        => &$GLOBALS['TL_LANG']['tl_module'],
            'eval'             => ['includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'              => "varchar(64) NOT NULL default ''",
        ],
        'news_filterCategoryTemplate'       => [
            'label'            => &$GLOBALS['TL_LANG']['tl_module']['news_filterCategoryTemplate'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => ['tl_module_news_plus', 'getFilterCategoriesTemplates'],
            'reference'        => &$GLOBALS['TL_LANG']['tl_module'],
            'eval'             => ['includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'              => "varchar(64) NOT NULL default ''",
        ],
        'news_filterShowSearch'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_filterShowSearch'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'news_filterUseSearchIndex'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_filterUseSearchIndex'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'news_filterFuzzySearch'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_filterFuzzySearch'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'news_filterSearchQueryType'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_filterSearchQueryType'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w100 clr'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'news_filterShowCategories'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_filterShowCategories'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'news_filterNewsCategoryArchives'   => [
            'label'            => &$GLOBALS['TL_LANG']['tl_module']['news_filterNewsCategoryArchives'],
            'exclude'          => true,
            'inputType'        => 'checkbox',
            'options_callback' => ['tl_module_news', 'getNewsArchives'],
            'eval'             => ['multiple' => true],
            'sql'              => "blob NULL",
        ],
        'news_readerModule'                 => [
            'label'            => &$GLOBALS['TL_LANG']['tl_module']['news_readerModule'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => ['tl_module_news_plus', 'getReaderModules'],
            'reference'        => &$GLOBALS['TL_LANG']['tl_module'],
            'eval'             => ['includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'              => "int(10) unsigned NOT NULL default '0'",
        ],
        'news_template_modal'               => [
            'label'            => &$GLOBALS['TL_LANG']['tl_module']['news_template_modal'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => ['tl_module_news_plus', 'getNewsModalTemplates'],
            'eval'             => ['tl_class' => 'w50', 'includeBlankOption' => true],
            'sql'              => "varchar(64) NOT NULL default ''",
        ],
        'news_showInModal'                  => [
            'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_showInModal'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50 m12'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'news_filterModule'                 => [
            'label'            => &$GLOBALS['TL_LANG']['tl_module']['news_filterModule'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => ['tl_module_news_plus', 'getFilterModules'],
            'reference'        => &$GLOBALS['TL_LANG']['tl_module'],
            'eval'             => ['includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'              => "int(10) unsigned NOT NULL default '0'",
        ],
        'news_archiveTitleAppendCategories' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_archiveTitleAppendCategories'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'clr', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'news_archiveTitleCategories'       => [
            'label'      => &$GLOBALS['TL_LANG']['tl_module']['news_archiveTitleCategories'],
            'exclude'    => true,
            'inputType'  => 'treePicker',
            'foreignKey' => 'tl_news_category.title',
            'eval'       => [
                'mandatory'    => true,
                'multiple'     => true,
                'fieldType'    => 'checkbox',
                'foreignTable' => 'tl_news_category',
                'titleField'   => 'title',
                'searchField'  => 'title',
                'managerHref'  => 'do=news&table=tl_news_category',
            ],
            'sql'        => "blob NULL",
        ],
        'news_useInfiniteScroll'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_useInfiniteScroll'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'clr', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'news_useAutoTrigger'               => [
            'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_useAutoTrigger'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'news_changeTriggerText'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_changeTriggerText'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'news_triggerText'                  => [
            'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_triggerText'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'jumpToDetails' => $GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo']
    ],
    is_array($arrDca['fields']) ? $arrDca['fields'] : []
);

$arrDca['fields']['news_archives']['options_callback']     = ['tl_module_news_plus', 'getNewsArchives'];
$arrDca['fields']['news_readerModule']['options_callback'] = ['tl_module_news_plus', 'getReaderModules'];

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
        $arrModules = [];
        $objModules = $this->Database->execute(
            "SELECT m.id, m.name, t.name AS theme FROM tl_module m LEFT JOIN tl_theme t ON m.pid=t.id WHERE m.type='newsfilter' ORDER BY t.name, m.name"
        );

        while ($objModules->next())
        {
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
        $arrModules = [];
        $objModules = $this->Database->execute(
            "SELECT m.id, m.name, t.name AS theme FROM tl_module m LEFT JOIN tl_theme t ON m.pid=t.id WHERE m.type LIKE 'newsreader%' ORDER BY t.name, m.name"
        );

        while ($objModules->next())
        {
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

        if (!$this->User->isAdmin && !is_array($this->User->news))
        {
            return [];
        }

        $arrArchives = [];
        $objArchives = $this->Database->execute("SELECT id, title, root FROM tl_news_archive ORDER BY root, title");

        while ($objArchives->next())
        {
            if ($this->User->hasAccess($objArchives->id, 'news'))
            {
                $strTitle = $objArchives->title;

                if (($objRoot = \PageModel::findByPk($objArchives->root)) !== null)
                {
                    $strTitle .= ' <strong> [' . $objRoot->title . '] </strong>';
                }

                $arrArchives[$objArchives->id] = $strTitle;
            }
        }

        return $arrArchives;
    }

}
