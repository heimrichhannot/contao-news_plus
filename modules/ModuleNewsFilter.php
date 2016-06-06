<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package news_plus
 * @author Mathias Arzberger <develop@pdir.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsPlus;

class ModuleNewsFilter extends ModuleNewsPlus
{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_newsfilter';

    /**
     * CategroyTemplate
     * @var string
     */
    protected $strCategoryTemplate = 'filter_cat_default';


	public function generate()
	{
		if (TL_MODE == 'BE') {
			$objTemplate           = new \BackendTemplate('be_wildcard');
			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD'][$this->type][0]) . ' ###';
			$objTemplate->title    = $this->headline;
			$objTemplate->id       = $this->id;
			$objTemplate->link     = $this->name;
			$objTemplate->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		$this->news_archives = $this->sortOutProtected(deserialize($this->news_archives));

		// Return if there are no archives
		if (!is_array($this->news_archives) || empty($this->news_archives))
		{
			return '';
		}

		return parent::generate();
	}

    protected function compile()
    {
		$objForm = new NewsFilterForm($this->objModel);

		/** @var NewsFilterRegistry $objFilter */
		$objFilter = NewsFilterRegistry::getInstance($this->arrData);

		$blnFeatured = false; // TODO

		$objNews = NewsPlusModel::findPublishedByFilter($objFilter, $blnFeatured, 0, 0, array());

		$arrIds = array();

		if($objNews !== null)
		{
			$arrIds = $objNews->fetchEach('id');
		}

		$objNewsFirst = NewsPlusModel::findFirstPublishedByIds($arrIds);

		if($objNewsFirst !== null)
		{
			$objForm->minDate = $objNewsFirst->date;
		}

		$objNewsLast = NewsPlusModel::findLastPublishedByIds($arrIds);

		if($objNewsLast !== null)
		{
			$objForm->maxDate = $objNewsLast->date;
		}

		$this->Template->form = $objForm->generate();
    }
}