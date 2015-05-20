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


		$strSql = "select id from tl_news where pid IN (" . implode(',', $this->news_archives) . ") AND tstamp = (select min(tstamp) from tl_news as f where f.pid = tl_news.pid and featured = '1')".
				  "ORDER BY date LIMIT 4";

		$objResult = $this->Database->prepare($strSql)->execute();

		$arrResult = $objResult->fetchAllAssoc();

		foreach ($arrResult as $rs) {
			$arr[] = $rs['id'];
		}

		$this->objArticles = NewsPlusModel::findPublishedByIds($arr);

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
