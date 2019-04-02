<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @package news_plus
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$dc = &$GLOBALS['TL_DCA']['tl_news'];

/**
 * Config
 */
$dc['config']['onload_callback'][] = ['tl_news_plus', 'initDefaultPalette'];
$dc['config']['onload_callback'][] = ['HeimrichHannot\NewsPlus\Backend\News', 'modifyDC'];

/**
 * Selectors
 */
$dc['palettes']['__selector__'][] = 'customLinkText';
$dc['palettes']['__selector__'][] = 'addSubNews';
$dc['palettes']['__selector__'][] = 'add_related_news';

/**
 * Palettes
 */
$dc['palettes']['default'] = str_replace('categories;', 'categories;{related_news_legend:hide},add_related_news;{subNews_legend},addSubNews;', $dc['palettes']['default']);
$dc['palettes']['default'] = str_replace('source;', 'source,customLinkText;', $dc['palettes']['default']);

\Controller::loadDataContainer('tl_leisuretip');
\Controller::loadLanguageFile('tl_leisuretip');

/**
 * Subpalettes
 */
$dc['subpalettes']['customLinkText']   = 'moreLinkText';
$dc['subpalettes']['addSubNews']       = 'subNewsArchives,subNews,subNewsTemplate';
$dc['subpalettes']['add_related_news'] = 'related_news';

/**
 * Fields
 */
$arrFields = [
    // make enclosures sortable
    'orderEnclosureSRC' => [
        'label' => &$GLOBALS['TL_LANG']['tl_news']['orderEnclosureSRC'],
        'sql'   => "blob NULL",
    ],
    'customLinkText'    => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['customLinkText'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'moreLinkText'      => [
        'label'            => &$GLOBALS['TL_LANG']['tl_news']['moreLinkText'],
        'exclude'          => true,
        'search'           => true,
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\NewsPlus\Backend\News', 'getMoreLinkText'],
        'eval'             => ['tl_class' => 'w50 clr'],
        'sql'              => "varchar(128) NOT NULL default ''",
    ],
    'addSubNews'        => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['addSubNews'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['submitOnChange' => true, 'tl_class' => 'long'],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'subNews'           => [
        'label'        => &$GLOBALS['TL_LANG']['tl_news']['subNews'],
        'inputType'    => 'fieldpalette',
        'foreignKey'   => 'tl_fieldpalette.id',
        'relation'     => ['type' => 'hasMany', 'load' => 'eager'],
        'sql'          => "blob NULL",
        'fieldpalette' => [
            'list'     => [
                'label' => [
                    'fields'         => ['nid', 'news_template'],
                    'format'         => '%s <span style="color:#b3b3b3;padding-left:3px">[%s]</span>',
                    'label_callback' => ['tl_news_plus', 'getSubNewsLabel'],
                ],
            ],
            'palettes' => [
                'default' => '{news_legend},nid;{template_legend},news_template',
            ],
            'fields'   => [
                'nid'           => [
                    'label'            => &$GLOBALS['TL_LANG']['tl_news']['nid'],
                    'exclude'          => true,
                    'search'           => true,
                    'inputType'        => 'select',
                    'foreignKey'       => 'tl_news.headline',
                    'relation'         => ['type' => 'hasMany', 'load' => 'eager'],
                    'options_callback' => ['tl_news_plus', 'getNewsGroupedByArchive'],
                    'eval'             => ['tl_class' => 'long', 'mandatory' => true, 'includeBlankOption' => true],
                    'sql'              => "int(10) unsigned NOT NULL default '0'",
                ],
                'news_template' => [
                    'label'            => &$GLOBALS['TL_LANG']['tl_news']['news_template'],
                    'default'          => 'news_subnews_default',
                    'exclude'          => true,
                    'inputType'        => 'select',
                    'options_callback' => ['tl_news_plus', 'getNewsTemplates'],
                    'eval'             => ['tl_class' => 'w50'],
                    'sql'              => "varchar(64) NOT NULL default ''",
                ],
            ],
        ],
    ],
    'add_related_news'  => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['add_related_news'],
        'inputType' => 'checkbox',
        'exclude'   => true,
        'sql'       => "char(1) NOT NULL default ''",
        'eval'      => ['submitOnChange' => true],
    ],
    'related_news'      => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['related_news'],
        'inputType' => 'tagsinput',
        'exclude'   => true,
        'sql'       => "blob NULL",
        'eval'      => [
            'placeholder'   => &$GLOBALS['TL_LANG']['tl_news']['placeholders']['related_news'],
            'freeInput'     => false,
            'multiple'      => true,
            'mode'          => \TagsInput::MODE_REMOTE,
            'tags_callback' => [['tl_news_plus', 'getRelatedNews']],
            'remote'        => [
                'fields'       => ['headline', 'id'],
                'format'       => '%s [ID:%s]',
                'queryField'   => 'headline',
                'queryPattern' => '%QUERY%',
                'foreignKey'   => 'tl_news.id',
                'limit'        => 10,
            ],
        ],
    ],
];

$dc['fields'] = array_merge($dc['fields'], $arrFields);

$dc['fields']['enclosure']['eval']['orderField'] = 'orderEnclosureSRC'; // make enclosures sortable

class tl_news_plus extends Backend
{
    /**
     * Manipulate related news from `tl_news.related_news` remote tagsinput call.
     *
     * @param                $arrOption
     * @param \DataContainer $dc
     * @return array
     */
    public function getRelatedNews($arrOption, DataContainer $dc)
    {
        if ($arrOption['value'] == $dc->id) {
            return null;
        }

        return $arrOption;
    }

    /**
     * If news archive has replaceNewsPalette set and a newsPalette given,
     * replace the default news palette with the given one
     *
     * @param DataContainer $dc
     * @return bool
     * @throws Exception
     */
    public function initDefaultPalette(DataContainer $dc)
    {
        $objNews = \HeimrichHannot\NewsPlus\NewsPlusModel::findByPk($dc->id);

        if ($objNews === null) {
            return false;
        }

        $objArchive = $objNews->getRelated('pid');

        if ($objArchive === null) {
            return false;
        }

        if ($objArchive->replaceNewsPalette && $objArchive->newsPalette != '') {
            if (!isset($GLOBALS['TL_DCA']['tl_news']['palettes'][$objArchive->newsPalette])) {
                return false;
            }

            $GLOBALS['TL_DCA']['tl_news']['palettes']['default'] = $GLOBALS['TL_DCA']['tl_news']['palettes'][$objArchive->newsPalette];
        }

        // HOOK: loadDataContainer must be triggerd after onload_callback, otherwise slick slider wont work anymore
        if (isset($GLOBALS['TL_HOOKS']['loadDataContainer']) && is_array($GLOBALS['TL_HOOKS']['loadDataContainer'])) {
            foreach ($GLOBALS['TL_HOOKS']['loadDataContainer'] as $callback) {
                $this->import($callback[0]);
                $this->{$callback[0]}->{$callback[1]}($dc->table);
            }
        }
    }

    public function getNewsGroupedByArchive(DataContainer $dc)
    {
        $arrOptions = [];

        $objCurrentNews = \HeimrichHannot\NewsPlus\NewsPlusModel::findByPk($dc->activeRecord->pid);

        if ($objCurrentNews === null) {
            return $arrOptions;
        }

        if (($objCurrentNewsArchive = $objCurrentNews->getRelated('pid')) === null) {
            return $arrOptions;
        }

        $arrPids = [];

        if ($objCurrentNewsArchive->limitSubNews) {
            $arrPids = deserialize($objCurrentNewsArchive->subNewsArchives, true);
        }


        if (empty($arrPids)) {
            $objNews = \HeimrichHannot\NewsPlus\NewsPlusModel::findAll();
        } else {
            $objNews = \HeimrichHannot\NewsPlus\NewsPlusModel::findByPids($arrPids);
        }

        if ($objNews === null) {
            return $arrOptions;
        }

        while ($objNews->next()) {
            if (($objArchive = $objNews->getRelated('pid')) === null) {
                continue;
            }

            $arrOptions[$objArchive->title][$objNews->id] = $objNews->headline . ' [ID: ' . $objNews->id . ']';
        }

        return $arrOptions;
    }

    public function getSubNewsLabel($row, $label, $dc = null, $imageAttribute = '', $blnReturnImage = false, $blnProtected = false)
    {
        $objNews = \HeimrichHannot\NewsPlus\NewsPlusModel::findByPk($row['nid']);

        if ($objNews === null) {
            return $label;
        }

        return sprintf('%s <span style="color:#b3b3b3;padding-left:3px">[%s]</span>', $objNews->headline, $row['news_template']);
    }

    /**
     * Return all news templates as array
     *
     * @return array
     */
    public function getNewsTemplates()
    {
        return $this->getTemplateGroup('news_');
    }
}
