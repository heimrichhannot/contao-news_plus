<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @package news_plus
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsPlus;

class NewsFilterForm extends \HeimrichHannot\FormHybrid\Form
{
	protected $isFilterForm = true;

	public $minDate = null;

	public $maxDate = null;

	public function getSubmission($blnFormatted = true, $blnSkipDefaults = false)
	{
		$objSubmission = parent::getSubmission($blnFormatted, $blnSkipDefaults);

		// reset the filter by get parameter
		if(\Input::get('reset'))
		{
			\Controller::redirect(\HeimrichHannot\Haste\Util\Url::removeQueryString(array('reset'), \Environment::get('request')));
		}

		// store submission in session and return
		if($this->isSubmitted() && $objSubmission !== null)
		{
			$arrSubmission = $objSubmission->row();
		}

		return !empty($arrSubmission) ? $arrSubmission : null;
	}

	public function modifyDC()
	{
		parent::modifyDC();

		if($this->minDate !== null)
		{
			$this->dca['fields']['startDate']['eval']['minDate'] = $this->minDate;
			$this->dca['fields']['startDate']['default'] = $this->minDate;
		}

		if($this->maxDate !== null)
		{
			$this->dca['fields']['endDate']['eval']['maxDate'] = $this->maxDate;
			$this->dca['fields']['endDate']['default'] = $this->maxDate;
		}

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