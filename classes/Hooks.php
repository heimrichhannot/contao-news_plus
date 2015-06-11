<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package news_plus
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsPlus;


class Hooks extends \Controller
{
    public function parseArticlesHook(&$objTemplate, $arrArticle, $objModule)
    {
        $this->addDummyImage($objTemplate, $arrArticle, $objModule);
        $this->sortEnclosures($objTemplate, $arrArticle, $objModule);
    }

    protected function addDummyImage(&$objTemplate, $arrArticle, $objModule)
    {
        if($objTemplate->addImage) return;

        $objArchive = \NewsArchiveModel::findByPk($arrArticle['pid']);

        if($objArchive === null) return;

        $objTemplate->addDummyImage = false;

        if($objArchive->addDummyImage && $objArchive->dummyImageSingleSRC != '')
        {
            $objModel = \FilesModel::findByUuid($objArchive->dummyImageSingleSRC);

            if ($objModel === null)
            {
                if (!\Validator::isUuid($objArchive->dummyImageSingleSRC))
                {
                    $objTemplate->text = '<p class="error">'.$GLOBALS['TL_LANG']['ERR']['version2format'].'</p>';
                }
            }
            elseif (is_file(TL_ROOT . '/' . $objModel->path))
            {
                // Override the default image size
                if ($objModule->imgSize != '')
                {
                    $size = deserialize($objModule->imgSize);

                    if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]))
                    {
                        $arrArticle['size'] = $objModule->imgSize;
                    }
                }

                $arrArticle['singleSRC'] = $objModel->path;
                $this->addImageToTemplate($objTemplate, $arrArticle);
                $objTemplate->class .= ' dummy-image';
                $objTemplate->addDummyImage = true;
                $objTemplate->addImage = false;
            }
        }
    }


    /**
     * Sortiert die Anhänge im Template nach der Reihenfolge wie im Artikel angegeben (analog ContentGallery.php)
     *
     * Die Sortierung ist im $arrArticle definiert. Der Array in $objTemplate muss danach sortiert werden.
     *
     * @param $objTemplate
     * @param $arrArticle
     * @param $objModule
     */
    protected function sortEnclosures(&$objTemplate, $arrArticle, $objModule)
    {
        if (!is_array($objTemplate->enclosure) || empty($objTemplate->enclosure)) return;

        $arrEnclosuresSorted = array();
        $arrEnclosures = $objTemplate->enclosure;

        // get uuids of files in correct order
        $arrUuids = deserialize($arrArticle['orderEnclosureSRC']);

        if (!is_array($arrUuids)) return;

        foreach( $arrUuids as $item)
        {
            $objFile = \FilesModel::findByUuid($item);

            $idx = $this->getIndexByValue($objFile->path, $arrEnclosures);

            $arrEnclosuresSorted[] = $arrEnclosures[$idx];
        }

        $objTemplate->enclosure = $arrEnclosuresSorted;
    }


    /**
     * Liefert den Index eines Elements im multidimensionalen Array anhand eines gesuchten Wertes (hier Pfad der Datei)
     *
     * @param $needle
     * @param $array
     * @return int|null|string
     */
    private function getIndexByValue($needle, $array) {
        foreach ($array as $key => $val) {
            if ($val['enclosure'] === $needle) {
                return $key;
            }
        }
        return null;
    }
}