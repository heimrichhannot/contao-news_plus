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

	public function modifyDC(&$arrDca = null)
	{
		parent::modifyDC();
		\Controller::loadLanguageFile('tl_news');

		if ($this->minDate !== null) {
			$arrDca['fields']['startDate']['eval']['minDate'] = $this->minDate;
			$arrDca['fields']['startDate']['default']         = $this->minDate;
		}

		if ($this->maxDate !== null) {
			$arrDca['fields']['endDate']['eval']['maxDate'] = $this->maxDate;
			$arrDca['fields']['endDate']['default']         = $this->maxDate;
		}

		$arrDca['fields']['trailInfoDistance']['inputType']      = 'slider';
		$arrDca['fields']['trailInfoDistance']['eval']['slider'] = array(
			'type'           => 'range',
			'step'           => 1,
			'value_callback' => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoDistanceValue'),
			'min_callback'   => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoMinDistanceMinValue'),
			'max_callback'   => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoMaxDistanceMaxValue'),
			'tooltip'        => 'hide',
		);

		$arrDca['fields']['trailInfoDuration']['inputType']      = 'slider';
		$arrDca['fields']['trailInfoDuration']['eval']['slider'] = array(
			'type'           => 'range',
			'step'           => 0.5,
			'value_callback' => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoDurationValue'),
			'min_callback'   => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoMinDurationMinValue'),
			'max_callback'   => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoMaxDurationMaxValue'),
			'tooltip'        => 'hide',
		);

		$arrDca['fields']['trailInfoDifficulty']['inputType']      = 'slider';
		$arrDca['fields']['trailInfoDifficulty']['eval']['slider'] = array(
			'type'           => 'range',
			'step'           => 1,
			'ticks'          => '[' . implode(',', $arrDca['fields']['trailInfoDifficulty']['options']) . ']',
			'ticks-labels'   => '["' . implode('","', $arrDca['fields']['trailInfoDifficulty']['reference']) . '"]',
			'value_callback' => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoDifficultyValue'),
			'min_callback'   => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoMinDifficultyMinValue'),
			'max_callback'   => array('\HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoMaxDifficultyMaxValue'),
			'tooltip'        => 'hide',
		);

		$arrDca['fields']['trailInfoStart']['inputType']                  = 'select';
		$arrDca['fields']['trailInfoStart']['eval']['chosen']             = true;
		$arrDca['fields']['trailInfoStart']['eval']['includeBlankOption'] = true;
		$arrDca['fields']['trailInfoStart']['eval']['blankOptionLabel']   = $GLOBALS['TL_LANG']['tl_news']['trailInfoStart'][0];
		$arrDca['fields']['trailInfoStart']['options_callback']           = array('HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoStartsFromPublishedNews');

		$arrDca['fields']['trailInfoDestination']['inputType']                  = 'select';
		$arrDca['fields']['trailInfoDestination']['eval']['chosen']             = true;
		$arrDca['fields']['trailInfoDestination']['eval']['includeBlankOption'] = true;
		$arrDca['fields']['trailInfoDestination']['eval']['blankOptionLabel']   = $GLOBALS['TL_LANG']['tl_news']['trailInfoDestination'][0];
		$arrDca['fields']['trailInfoDestination']['options_callback']           = array('HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoDestinationsFromPublishedNews');

		$arrDca['fields']['trailInfoDifficultyMin']['options_callback'] = array('HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoMinDifficultyFromPublishedNews');
		$arrDca['fields']['trailInfoDifficultyMax']['options_callback'] = array('HeimrichHannot\NewsPlus\NewsFilterFormHelper', 'getTrailInfoMaxDifficultyFromPublishedNews');

		if ($this->minDate !== null) {
			$arrDca['fields']['startDate']['eval']['minDate'] = $this->minDate;
			$arrDca['fields']['startDate']['default']         = $this->minDate;
		}

		if ($this->maxDate !== null) {
			$arrDca['fields']['endDate']['eval']['maxDate'] = $this->maxDate;
			$arrDca['fields']['endDate']['default']         = $this->maxDate;
		}

		// HOOK: modify dca
		if (isset($GLOBALS['TL_HOOKS']['modifyNewsFilterDca']) && is_array($GLOBALS['TL_HOOKS']['modifyNewsFilterDca'])) {
			foreach ($GLOBALS['TL_HOOKS']['modifyNewsFilterDca'] as $callback) {
				$this->import($callback[0]);
				$this->{$callback[0]}->{$callback[1]}($arrDca, $this);
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