<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @package ${CARET}
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsPlus;

class NewsFilterForm extends \HeimrichHannot\FormHybrid\Form
{
	protected $isFilterForm = true;

	public function getSubmission($blnFormatted = true, $blnSkipDefaults = false)
	{
		$strSessionKey = NEWSPLUS_SESSION_NEWS_FILTER . '_' . $this->objModule->id;

		$objSubmission = parent::getSubmission($blnFormatted, $blnSkipDefaults);
		$arrSubmission = \Session::getInstance()->get($strSessionKey);;

		// reset the filter by get parameter
		if(\Input::get('reset'))
		{
			\Session::getInstance()->remove($strSessionKey);
			\Controller::redirect(\HeimrichHannot\Haste\Util\Url::removeQueryString(array('reset'), \Environment::get('request')));
		}


		// store submission in session and return
		if($this->isSubmitted() && $objSubmission !== null)
		{
			$arrSubmission = $objSubmission->row();
			\Session::getInstance()->set($strSessionKey, $arrSubmission);
		}

		return !empty($arrSubmission) ? $arrSubmission : null;
	}

	protected function modifyDC()
	{
		parent::modifyDC();

		// HOOK: modify dca
		if (isset($GLOBALS['TL_HOOKS']['modifyNewsFilterDca']) && is_array($GLOBALS['TL_HOOKS']['modifyNewsFilterDca']))
		{
			foreach ($GLOBALS['TL_HOOKS']['modifyNewsFilterDca'] as $callback)
			{
				$this->import($callback[0]);
				$this->{$callback[0]}->{$callback[1]}($this->dca, $this);
			}
		}
	}

	protected function compile()
	{

		$this->Template->resetTitle = $GLOBALS['TL_LANG']['tl_newsfilter']['resetTitle'];
	}

	protected function afterSubmitCallback($dc)
	{
		// HOOK: modify dca
		if (isset($GLOBALS['TL_HOOKS']['afterNewsFilterSubmitCallback']) && is_array($GLOBALS['TL_HOOKS']['afterNewsFilterSubmitCallback']))
		{
			foreach ($GLOBALS['TL_HOOKS']['afterNewsFilterSubmitCallback'] as $callback)
			{
				$this->import($callback[0]);
				$this->{$callback[0]}->{$callback[1]}($this);
			}
		}
	}
}