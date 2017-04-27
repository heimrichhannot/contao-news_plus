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


$GLOBALS['TL_DCA']['tl_newsfilter'] = array(
    'palettes' => array(
        'default'    => '{archive_legend},pid;{date_legend},startDate,endDate;{search_legend},q;{category_legend},cat;{submit_legend},submit',
        'leisuretip' => 'trailInfoDistance,trailInfoDistanceMin,trailInfoDistanceMax,trailInfoDuration,trailInfoDurationMin,trailInfoDurationMax,trailInfoDifficulty,trailInfoDifficultyMin,trailInfoDifficultyMax,trailInfoStart,trailInfoDestination',

    ),
    'fields'   => array(
        // default
        'q'                      => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_newsfilter']['q'],
            'inputType' => 'text',
            'eval'      => array(
                'placeholder' => &$GLOBALS['TL_LANG']['tl_newsfilter']['placeholder']['q'],
            ),
        ),
        'pid'                    => array(
            'label'            => &$GLOBALS['TL_LANG']['tl_newsfilter']['pid'],
            'inputType'        => 'select',
            'options_callback' => array('tl_newsfilter', 'getNewsArchives'),
            'eval'             => array(
                'includeBlankOption' => true,
                'blankOptionLabel'   => &$GLOBALS['TL_LANG']['tl_newsfilter']['blankOptionLabel']['pid'],
                'multiple'           => true,
            ),
        ),
        'cat'                    => array(
            'label'            => &$GLOBALS['TL_LANG']['tl_newsfilter']['cat'],
            'inputType'        => 'select',
            'options_callback' => array('tl_newsfilter', 'getNewsCategories'),
            'eval'             => array(
                'includeBlankOption' => true,
                'blankOptionLabel'   => &$GLOBALS['TL_LANG']['tl_newsfilter']['blankOptionLabel']['cat'],
                'multiple'           => true,
            ),
        ),
        'startDate'              => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_newsfilter']['startDate'],
            'inputType' => 'text',
            'eval'      => array('rgxp' => 'date', 'datepicker' => true),
        ),
        'endDate'                => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_newsfilter']['endDate'],
            'inputType' => 'text',
            'eval'      => array('rgxp' => 'date', 'datepicker' => true),
        ),
        'submit'                 => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_newsfilter']['submit'],
            'inputType' => 'submit',
            'eval'      => array('class' => 'btn btn-primary'),
        ),

        // leisuretip
        'trailInfoDistance'      => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_newsfilter']['trailInfoDistance'],
            'inputType' => 'text',
            'eval'      => array(
                'placeholder' => &$GLOBALS['TL_LANG']['tl_newsfilter']['placeholder']['trailInfoDistance'],
            ),
        ),
        'trailInfoDistanceMin'   => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_newsfilter']['trailInfoDistanceMin'],
            'inputType' => 'text',
            'eval'      => array(
                'placeholder' => &$GLOBALS['TL_LANG']['tl_newsfilter']['placeholder']['trailInfoDistanceMin'],
            ),
        ),
        'trailInfoDistanceMax'   => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_newsfilter']['trailInfoDistanceMax'],
            'inputType' => 'text',
            'eval'      => array(
                'placeholder' => &$GLOBALS['TL_LANG']['tl_newsfilter']['placeholder']['trailInfoDistanceMax'],
            ),
        ),
        'trailInfoDuration'      => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_newsfilter']['trailInfoDuration'],
            'inputType' => 'text',
            'eval'      => array(
                'placeholder' => &$GLOBALS['TL_LANG']['tl_newsfilter']['placeholder']['trailInfoDuration'],
            ),
        ),
        'trailInfoDurationMin'   => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_newsfilter']['trailInfoDurationMin'],
            'inputType' => 'text',
            'eval'      => array(
                'slider'      => array(
                    'value_callback' => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoMinDurationValue'),
                    'min_callback'   => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoMinDurationMinValue'),
                    'max_callback'   => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoMinDurationMaxValue'),
                    'step'           => 0.5,
                ),
                'placeholder' => &$GLOBALS['TL_LANG']['tl_newsfilter']['placeholder']['trailInfoDurationMin'],
            ),
        ),
        'trailInfoDurationMax'   => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_newsfilter']['trailInfoDurationMax'],
            'inputType' => 'text',
            'eval'      => array(
                'slider'      => array(
                    'value_callback' => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoMaxDurationValue'),
                    'min_callback'   => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoMaxDurationMinValue'),
                    'max_callback'   => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoMaxDurationMaxValue'),
                    'step'           => 0.5,
                ),
                'placeholder' => &$GLOBALS['TL_LANG']['tl_newsfilter']['placeholder']['trailInfoDurationMax'],
            ),
        ),
        'trailInfoDifficulty'    => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_newsfilter']['trailInfoDifficulty'],
            'inputType' => 'text',
            'options'   => array(0, 1, 2, 3, 4),
            'reference' => &$GLOBALS['TL_LANG']['tl_newsfilter']['trailInfoDifficulties'],
            'eval'      => array(
                'placeholder' => &$GLOBALS['TL_LANG']['tl_newsfilter']['placeholder']['trailInfoDifficulty'],
            ),
        ),
        'trailInfoDifficultyMin' => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_newsfilter']['trailInfoDifficultyMin'],
            'inputType' => 'select',
            'options'   => array(0, 1, 2, 3),
            'reference' => &$GLOBALS['TL_LANG']['tl_newsfilter']['trailInfoDifficulties'],
            'eval'      => array(
                'includeBlankOption' => true,
                'blankOptionLabel'   => &$GLOBALS['TL_LANG']['tl_newsfilter']['blankOptionLabel']['trailInfoDifficultyMin'],
            ),
        ),
        'trailInfoDifficultyMax' => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_newsfilter']['trailInfoDifficultyMax'],
            'inputType' => 'select',
            'options'   => array(0, 1, 2, 3),
            'reference' => &$GLOBALS['TL_LANG']['tl_newsfilter']['trailInfoDifficulties'],
            'eval'      => array(
                'includeBlankOption' => true,
                'blankOptionLabel'   => &$GLOBALS['TL_LANG']['tl_newsfilter']['blankOptionLabel']['trailInfoDifficultyMax'],
            ),
        ),
        'trailInfoStart'         => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_newsfilter']['trailInfoStart'],
            'inputType' => 'text',
            'eval'      => array(
                'placeholder' => &$GLOBALS['TL_LANG']['tl_newsfilter']['placeholder']['trailInfoStart'],
            ),
        ),
        'trailInfoDestination'   => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_newsfilter']['trailInfoDestination'],
            'inputType' => 'text',
            'eval'      => array(
                'placeholder' => &$GLOBALS['TL_LANG']['tl_newsfilter']['placeholder']['trailInfoDestination'],
            ),
        ),
    ),
);

class tl_newsfilter extends Backend
{

    public function __construct()
    {
        parent::__construct();
        $this->import('FrontendUser', 'User');
    }

    public function getNewsCategories(\HeimrichHannot\FormHybrid\DC_Hybrid $dc)
    {
        $arrOptions = array();

        if (TL_MODE != 'FE')
        {
            return $arrOptions;
        }

        $objModule = $dc->getConfig()->getModule();

        if ($objModule === null)
        {
            return $arrOptions;
        }

        $arrNewsArchives = $this->getNewsArchives($dc);

        // Return if there are no archives
        if (!is_array($arrNewsArchives) || empty($arrNewsArchives))
        {
            return $arrOptions;
        }

        $objModule->news_categories = deserialize($objModule->news_categories);

        // Return if there are no categories
        if (!is_array($objModule->news_categories) || empty($objModule->news_categories))
        {
            return $arrOptions;
        }

        $arrCategories = $objModule->news_categories;

        foreach ($arrCategories as $id)
        {
            $arrChildren = \Database::getInstance()->getChildRecords($id, 'tl_news_category');

            if (!is_array($arrChildren) || empty($arrChildren))
            {
                continue;
            }

            $arrCategories = array_merge($arrCategories, $arrChildren);
        }

        $objCategories = \NewsCategories\NewsCategoryModel::findPublishedByParent(array_keys($arrNewsArchives), $arrCategories);

        if ($objCategories === null)
        {
            return $arrOptions;
        }

        while ($objCategories->next())
        {
            $arrOptions[$objCategories->id] = $objCategories->frontendTitle ?: $objCategories->title;
        }

        return $arrOptions;
    }

    public function getNewsArchives(\HeimrichHannot\FormHybrid\DC_Hybrid $dc)
    {
        $arrOptions = array();

        if (TL_MODE != 'FE')
        {
            return $arrOptions;
        }

        $objModule = $dc->getConfig()->getModule();

        if ($objModule === null)
        {
            return $arrOptions;
        }

        $objModule->news_archives = deserialize($objModule->news_archives);

        // Return if there are no archives
        if (!is_array($objModule->news_archives) || empty($objModule->news_archives))
        {
            return $arrOptions;
        }

        $objArchive = \NewsArchiveModel::findMultipleByIds($objModule->news_archives);

        if ($objArchive === null)
        {
            return $arrOptions;
        }

        while ($objArchive->next())
        {
            if ($objArchive->protected)
            {
                if (!FE_USER_LOGGED_IN)
                {
                    continue;
                }

                $groups = deserialize($objArchive->groups);

                if (!is_array($groups) || empty($groups) || !count(array_intersect($groups, $this->User->groups)))
                {
                    continue;
                }
            }

            $arrOptions[$objArchive->id] = $objArchive->displayTitle ?: $objArchive->title;
        }

        return $arrOptions;
    }

}