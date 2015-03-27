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

}