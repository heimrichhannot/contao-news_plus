<?php

namespace HeimrichHannot\NewsPlus;

class ModuleNewsListHighlight extends ModuleNewsListPlus
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_newslist_plus';

	protected $objArticles;

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['newslist_plus'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		$this->news_archives = $this->sortOutProtected(deserialize($this->news_archives));

		// Return if there are no archives
		if (!is_array($this->news_archives) || empty($this->news_archives))
		{
			return '';
		}

		$this->news_featured = 'featured';

		// unset search string for highlighted section
		\Input::setGet('searchKeywords', null);

		$this->objArticles = NewsPlusModel::findPublishedByPids($this->news_archives, array(), $this->news_featured == 'featured', $this->numberOfItems, 0);

		if($this->objArticles === null)
		{
			return '';
		}

		return parent::generate();
	}


    /**
     * Generate the module
     */
    protected function compile()
    {
        $this->Template->articles = $this->parseArticles($this->objArticles);
    }
}
