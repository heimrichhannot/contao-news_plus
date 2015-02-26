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
									{news_test},news_filterTemplate,news_showSearch;
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
    'news_showSearch'	=> array
    (
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_showSearch'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'		=> array('tl_class' => 'w50'),
        'sql'       => "char(1) NOT NULL default ''",
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
     * Return all filter modules as array
     * @return array
     */
    public function getFilterTemplates()
    {
        return $this->getTemplateGroup('form_news');
    }

    /**
     * Return all news modal templates as array
     * @return array
     */
    public function getNewsModalTemplates()
    {
        return $this->getTemplateGroup('news_');
    }
}
