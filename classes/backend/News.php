<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsPlus\Backend;


class News extends \Backend
{
    public function modifyDC(\DataContainer $dc)
    {
        $arrDca = &$GLOBALS['TL_DCA']['tl_news'];

        if (TL_MODE != 'BE' || \Input::get('do') != 'news')
        {
            return;
        }

        $intId   = strlen(\Input::get('id')) ? \Input::get('id') : CURRENT_ID;
        $objNews = \NewsModel::findByPk($intId);

        if ($objNews === null || ($objArchive = $objNews->getRelated('pid')) === null)
        {
            return;
        }

        if ($objArchive->limitInputCharacterLength && !empty($arrLimits = deserialize($objArchive->inputCharacterLengths, true)))
        {
            foreach ($arrLimits as $arrConfig)
            {
                $strField  = $arrConfig['field'];
                $intLength = $arrConfig['length'];

                if ($intLength > 0 && isset($arrDca['fields'][$strField]))
                {
                    $arrData = &$arrDca['fields'][$strField];

                    if(isset($arrData['eval']['maxlength'])){
                        unset($arrData['eval']['maxlength']); // contao core does not count special characters as decoded entities
                    }

                    $arrData['eval']['data-maxlength'] = $intLength;
                    $arrData['eval']['rgxp'] = 'maxlength::' . $intLength;
                    $arrData['eval']['data-count-characters'] = true;


                    if (version_compare(VERSION, '4.0', '<') && $arrData['eval']['rte'])
                    {
                        $arrData['eval']['rte'] = '../modules/news_plus/config/tinyMCE';
                    }
                }
            }
        }

    }
}
