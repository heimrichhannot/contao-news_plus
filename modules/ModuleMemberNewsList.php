<?php

namespace HeimrichHannot\NewsPlus;

use Contao\ModuleNewsList;

class ModuleMemberNewsList extends ModuleNewsList
{

    /**
     * Template
     *
     * @var string
     */
    protected $strTemplate = 'mod_membernewslist';


    /**
     * Display a wildcard in the back end
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['membernewslist'][0]) . ' ###';
            $objTemplate->title    = $this->headline;
            $objTemplate->id       = $this->id;
            $objTemplate->link     = $this->name;
            $objTemplate->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        // Set the item from the auto_item parameter
        if (!isset($_GET['items']) && \Config::get('useAutoItem') && isset($_GET['auto_item']))
        {
            \Input::setGet('items', \Input::get('auto_item'));
        }

        // Do not index or cache the page if no news item has been specified
        if (!\Input::get('items'))
        {
            /** @var \PageModel $objPage */
            global $objPage;

            $objPage->noSearch = 1;
            $objPage->cache = 0;

            return '';
        }

        return parent::generate();
    }

    protected function countItems($newsArchives, $blnFeatured)
    {
        global $objPage;

        if (!in_array('frontendedit', \ModuleLoader::getActive()))
        {
            throw new \Exception('For usage of ModuleMemberNewsReader you need heimrichhannot/contao-frontendedit installed.');
        }

        // Get the news item
        if (($objMember = \MemberModel::findBy(['disable!=1', 'alias=?'], [\Input::get('items')])) === null)
        {
            /** @var \PageError404 $objHandler */
            $objHandler = new $GLOBALS['TL_PTY']['error_404']();
            $objHandler->generate($objPage->id);
        }

        return NewsPlusModel::countPublishedByPidsAndMemberAuthor($newsArchives, $objMember->id, $blnFeatured);
    }

    protected function fetchItems($newsArchives, $blnFeatured, $limit, $offset)
    {
        global $objPage;

        if (!in_array('frontendedit', \ModuleLoader::getActive()))
        {
            throw new \Exception('For usage of ModuleMemberNewsReader you need heimrichhannot/contao-frontendedit installed.');
        }

        // Get the news item
        if (($objMember = \MemberModel::findBy(['disable!=1', 'alias=?'], [\Input::get('items')])) === null)
        {
            /** @var \PageError404 $objHandler */
            $objHandler = new $GLOBALS['TL_PTY']['error_404']();
            $objHandler->generate($objPage->id);
        }

        return NewsPlusModel::findPublishedByPidsAndMemberAuthor($newsArchives, $objMember->id, $blnFeatured, $limit, $offset);
    }
}