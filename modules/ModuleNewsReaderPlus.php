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
use Contao\NewsArchiveModel;
use HeimrichHannot\CalendarPlus\EventsPlusHelper;


/**
 * Class ModuleNewsReader
 *
 * Front end module "news reader".
 * @copyright  Leo Feyer 2005-2014
 * @author     Leo Feyer <https://contao.org>
 * @package    News
 */
class ModuleNewsReaderPlus extends ModuleNewsPlus
{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_newsreader_plus';


    /**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['newsreader'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        global $objPage;

        if($this->news_template_modal)
        {
            $this->strTemplate = 'mod_news_modal';
            $this->news_template = $this->news_template_modal;

            // list config
            $this->news_showInModal = true;
            $this->news_readerModule = $this->id;

            // set modal css ID for generateModal() and parent::generate()
            $arrCss = deserialize($this->cssID, true);
            $arrCss[0] = NewsPlusHelper::getCSSModalID($this->id);
            $this->cssID = $arrCss;
            $this->base = \Controller::generateFrontendUrl($objPage->row());

            if($this->Environment->isAjaxRequest && !$this->isSearchIndexer())
            {
                $this->strTemplate = 'mod_news_modal_ajax';
                $this->generateAjax();
            }

            if(!$this->checkConditions())
            {
                return $this->generateModal();
            }
        }

        return parent::generate();
    }

    protected function generateAjax()
    {
        if($this->checkConditions())
        {
            parent::generate();
            die($this->replaceInsertTags($this->Template->output())); // use output, otherwise page will not be added to search index
        }
    }

    protected function checkConditions()
    {
        // Set the item from the auto_item parameter
        if (!isset($_GET['items']) && \Config::get('useAutoItem') && isset($_GET['auto_item']))
        {
            \Input::setGet('items', \Input::get('auto_item'));
        }

        // Do not index or cache the page if no news item has been specified
        if (!\Input::get('items'))
        {
            global $objPage;
            $objPage->noSearch = 1;
            $objPage->cache = 0;
            return '';
        }

        $this->news_archives = $this->sortOutProtected(deserialize($this->news_archives));

        // Do not index or cache the page if there are no archives
        if (!is_array($this->news_archives) || empty($this->news_archives))
        {
            global $objPage;
            $objPage->noSearch = 1;
            $objPage->cache = 0;
            return '';
        }

        return true;
    }

    protected function generateModal()
    {
        $this->Template = new \FrontendTemplate($this->strTemplate);
        $this->Template->setData($this->arrData);
        $this->Template->class = trim('mod_' . $this->type . ' ' . $this->cssID[1]);
        $this->Template->cssID = ($this->cssID[0] != '') ? ' id="' . $this->cssID[0] . '"' : '';
        $this->Template->base = $this->base;

        if (!empty($this->objModel->classes) && is_array($this->objModel->classes))
        {
            $this->Template->class .= ' ' . implode(' ', $this->objModel->classes);
        }

        return $this->Template->parse();
    }

    /**
     * Generate the module
     */
    protected function compile()
    {
        global $objPage;

        $this->Template->articles = '';
        $this->Template->referer = 'javascript:history.go(-1)';
        $this->Template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];

        // Get the news item
        $objArticle = \NewsModel::findPublishedByParentAndIdOrAlias(\Input::get('items'), $this->news_archives);

        if ($objArticle === null)
        {
            // Do not index or cache the page
            $objPage->noSearch = 1;
            $objPage->cache = 0;

            // Send a 404 header
            header('HTTP/1.1 404 Not Found');
            $this->Template->articles = '<p class="error">' . sprintf($GLOBALS['TL_LANG']['MSC']['invalidPage'], \Input::get('items')) . '</p>';
            return;
        }

        $arrArticle = $this->parseArticle($objArticle);
        $this->Template->articles = $arrArticle;

        // Overwrite the page title (see #2853 and #4955)
        if ($objArticle->headline != '')
        {
            $objPage->pageTitle = strip_tags(strip_insert_tags($objArticle->headline));
        }

        // Overwrite the page description
        if ($objArticle->teaser != '')
        {
            $objPage->description = $this->prepareMetaDescription($objArticle->teaser);
        }

        // HOOK: comments extension required
        if ($objArticle->noComments || !in_array('comments', \ModuleLoader::getActive()))
        {
            $this->Template->allowComments = false;
            return;
        }

        $objArchive = $objArticle->getRelated('pid');
        $this->Template->allowComments = $objArchive->allowComments;

        // Comments are not allowed
        if (!$objArchive->allowComments)
        {
            return;
        }

        // Adjust the comments headline level
        $intHl = min(intval(str_replace('h', '', $this->hl)), 5);
        $this->Template->hlc = 'h' . ($intHl + 1);

        $this->import('Comments');
        $arrNotifies = array();

        // Notify the system administrator
        if ($objArchive->notify != 'notify_author')
        {
            $arrNotifies[] = $GLOBALS['TL_ADMIN_EMAIL'];
        }

        // Notify the author
        if ($objArchive->notify != 'notify_admin')
        {
            if (($objAuthor = $objArticle->getRelated('author')) !== null && $objAuthor->email != '')
            {
                $arrNotifies[] = $objAuthor->email;
            }
        }

        $objConfig = new \stdClass();

        $objConfig->perPage = $objArchive->perPage;
        $objConfig->order = $objArchive->sortOrder;
        $objConfig->template = $this->com_template;
        $objConfig->requireLogin = $objArchive->requireLogin;
        $objConfig->disableCaptcha = $objArchive->disableCaptcha;
        $objConfig->bbcode = $objArchive->bbcode;
        $objConfig->moderate = $objArchive->moderate;

        $this->Comments->addCommentsToTemplate($this->Template, $objConfig, 'tl_news', $objArticle->id, $arrNotifies);
    }

    protected function isSearchIndexer()
    {
        return (strpos($_SERVER['HTTP_REFERER'], 'main.php?act=index&do=maintenance') !== false);
    }
}
