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
		if (\Input::get('reset')) {
			\Controller::redirect(\HeimrichHannot\Haste\Util\Url::removeQueryString(array('reset'), \Environment::get('request')));
		}

		// store submission in session and return
		if ($this->isSubmitted() && $objSubmission !== null) {
			$arrSubmission = $objSubmission->row();
		}

		return !empty($arrSubmission) ? $arrSubmission : null;
	}

	public function modifyDC()
	{
		parent::modifyDC();
		\Controller::loadLanguageFile('tl_news');

		if ($this->minDate !== null) {
			$this->dca['fields']['startDate']['eval']['minDate'] = $this->minDate;
			$this->dca['fields']['startDate']['default']         = $this->minDate;
		}

		if ($this->maxDate !== null) {
			$this->dca['fields']['endDate']['eval']['maxDate'] = $this->maxDate;
			$this->dca['fields']['endDate']['default']         = $this->maxDate;
		}

		$this->dca['fields']['trailInfoDistance']['inputType']      = 'slider';
		$this->dca['fields']['trailInfoDistance']['eval']['slider'] = array(
			'type'           => 'range',
			'step'           => 1,
			'value_callback' => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoDistanceValue'),
			'min_callback'   => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoMinDistanceMinValue'),
			'max_callback'   => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoMaxDistanceMaxValue'),
			'tooltip'        => 'hide',
		);

		$this->dca['fields']['trailInfoDuration']['inputType']      = 'slider';
		$this->dca['fields']['trailInfoDuration']['eval']['slider'] = array(
			'type'           => 'range',
			'step'           => 0.5,
			'value_callback' => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoDurationValue'),
			'min_callback'   => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoMinDurationMinValue'),
			'max_callback'   => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoMaxDurationMaxValue'),
			'tooltip'        => 'hide',
		);

		$this->dca['fields']['trailInfoDifficulty']['inputType']      = 'slider';
		$this->dca['fields']['trailInfoDifficulty']['eval']['slider'] = array(
			'type'           => 'range',
			'step'           => 1,
			'ticks'          => '[' . implode(',', $this->dca['fields']['trailInfoDifficulty']['options']) . ']',
			'ticks-labels'   => '["' . implode('","', $this->dca['fields']['trailInfoDifficulty']['reference']) . '"]',
			'value_callback' => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoDifficultyValue'),
			'min_callback'   => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoMinDifficultyMinValue'),
			'max_callback'   => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoMaxDifficultyMaxValue'),
			'tooltip'        => 'hide',
		);

		$this->dca['fields']['trailInfoStart']['inputType']                  = 'select';
		$this->dca['fields']['trailInfoStart']['eval']['chosen']             = true;
		$this->dca['fields']['trailInfoStart']['eval']['includeBlankOption'] = true;
		$this->dca['fields']['trailInfoStart']['eval']['blankOptionLabel']   = $GLOBALS['TL_LANG']['tl_news']['trailInfoStart'][0];
		$this->dca['fields']['trailInfoStart']['options_callback']           = array('HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoStartsFromPublishedNews');

		$this->dca['fields']['trailInfoDestination']['inputType']                  = 'select';
		$this->dca['fields']['trailInfoDestination']['eval']['chosen']             = true;
		$this->dca['fields']['trailInfoDestination']['eval']['includeBlankOption'] = true;
		$this->dca['fields']['trailInfoDestination']['eval']['blankOptionLabel']   = $GLOBALS['TL_LANG']['tl_news']['trailInfoDestination'][0];
		$this->dca['fields']['trailInfoDestination']['options_callback']           = array('HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoDestinationsFromPublishedNews');

		$this->dca['fields']['trailInfoDifficultyMin']['options_callback'] = array('HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoMinDifficultyFromPublishedNews');
		$this->dca['fields']['trailInfoDifficultyMax']['options_callback'] = array('HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoMaxDifficultyFromPublishedNews');

		if ($this->minDate !== null) {
			$this->dca['fields']['startDate']['eval']['minDate'] = $this->minDate;
			$this->dca['fields']['startDate']['default']         = $this->minDate;
		}

		if ($this->maxDate !== null) {
			$this->dca['fields']['endDate']['eval']['maxDate'] = $this->maxDate;
			$this->dca['fields']['endDate']['default']         = $this->maxDate;
		}

		// HOOK: modify dca
		if (isset($GLOBALS['TL_HOOKS']['modifyNewsFilterDca']) && is_array($GLOBALS['TL_HOOKS']['modifyNewsFilterDca'])) {
			foreach ($GLOBALS['TL_HOOKS']['modifyNewsFilterDca'] as $callback) {
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
		if (isset($GLOBALS['TL_HOOKS']['afterNewsFilterSubmitCallback']) && is_array($GLOBALS['TL_HOOKS']['afterNewsFilterSubmitCallback'])) {
			foreach ($GLOBALS['TL_HOOKS']['afterNewsFilterSubmitCallback'] as $callback) {
				$this->import($callback[0]);
				$this->{$callback[0]}->{$callback[1]}($this);
			}
		}
	}
}