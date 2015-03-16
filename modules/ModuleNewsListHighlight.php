<?php

namespace HeimrichHannot\NewsPlus;

class ModuleNewsListHighlight extends ModuleNewsListPlus
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_newslist_plus';

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

        return parent::generate();
    }


    /**
     * Generate the module
     */
    protected function compile()
    {
        $this->import('Database');

        $strSql = "select *
                      from tl_news t1
                      where (pid, date) in (select pid, max(date)
                           from tl_news t2
                           group by pid)
                      AND featured = 1 ORDER BY date DESC
                      LIMIT 4";

        $strSql = "select * from tl_news where tstamp = (select min(tstamp) from tl_news as f where f.pid = tl_news.pid and featured = '1')".
                    "ORDER BY date LIMIT 4;";

        $objResult = $this->Database->prepare($strSql)->execute();

        $GLOBALS['NEWS_LIST_EXCLUDE_RELATED'] = true;



        $this->Template->articles = $this->parseArticles($objResult);
    }
}