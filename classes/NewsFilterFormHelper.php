<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package news_plus
 * @author Oliver Janke <o.janke@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */
namespace HeimrichHannot\NewsPlus;

use Contao\DataContainer;
use Contao\Input;

class NewsFilterFormHelper extends \Controller
{
	public function getTrailInfoStartsFromPublishedNews(DataContainer $dc)
	{
		$arrPids = deserialize($dc->news_archives, true);
		$field = 'trailInfoStart';

		if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return array();

		$arrValues = array_unique($arrValues);
		sort($arrValues);

		$arrOption = array();

		foreach ($arrValues as $value)
		{
			$arrOption[] = strip_tags(\Controller::replaceInsertTags($value));
		}

		return $arrOption;
	}
	public function getTrailInfoDestinationsFromPublishedNews(DataContainer $dc)
	{
		$arrPids = deserialize($dc->news_archives, true);
		$field = 'trailInfoDestination';

		if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return array();

		$arrValues = array_unique($arrValues);
		sort($arrValues);

		$arrOption = array();

		foreach ($arrValues as $value)
		{
			$arrOption[] = strip_tags(\Controller::replaceInsertTags($value));
		}

		return $arrOption;
	}


	public function getTrailInfoMinDifficultyFromPublishedNews(DataContainer $dc)
	{
		$arrPids = deserialize($dc->news_archives, true);
		$field = 'trailInfoDifficultyMin';

		if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return array();

		$arrValues = array_unique($arrValues);
		sort($arrValues);

		return $arrValues;
	}
	public function getTrailInfoMaxDifficultyFromPublishedNews(DataContainer $dc)
	{
		$arrPids = deserialize($dc->news_archives, true);
		$field = 'trailInfoDifficultyMax';

		if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return array();

		$arrValues = array_unique($arrValues);
		sort($arrValues);

		return $arrValues;
	}


	public function getTrailInfoDistanceValue($objForm, $arrConfig)
	{
		if ($arrConfig['type'] == 'range')
		{
			return (Input::get('trailInfoDistance') != null) ? Input::get('trailInfoDistance') : '[' . NEWSPLUS_FILTER_SLIDER_DEFAULT_MIN.','.NEWSPLUS_FILTER_SLIDER_DEFAULT_MAX . ']';
		}
		else {
			return (Input::get('trailInfoDistance') != null) ? Input::get('trailInfoDistance') : NEWSPLUS_FILTER_SLIDER_DEFAULT_MIN;
		}
	}

	public function getTrailInfoMinDistanceMinValue($objForm)
	{
		$arrPids = deserialize($objForm->news_archives, true);
		$field = 'trailInfoDistanceMin';

		if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return NEWSPLUS_FILTER_SLIDER_DEFAULT_MIN;

		return ceil(min($arrValues));
	}
	public function getTrailInfoMinDistanceMaxValue($objForm)
	{
		$arrPids = deserialize($objForm->news_archives, true);
		$field = 'trailInfoDistanceMin';

		if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return NEWSPLUS_FILTER_SLIDER_DEFAULT_MIN;

		return ceil(max($arrValues));
	}
	public function getTrailInfoMinDistanceValue($objForm, $arrConfig)
	{
		return (Input::get('trailInfoDistanceMin') != null) ? Input::get('trailInfoDistanceMin') : NEWSPLUS_FILTER_SLIDER_DEFAULT_MIN;
	}


	public function getTrailInfoMaxDistanceMinValue($objForm)
	{
		$arrPids = deserialize($objForm->news_archives, true);
		$field = 'trailInfoDistanceMax';

		if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return NEWSPLUS_FILTER_SLIDER_DEFAULT_MIN;

		return ceil(min($arrValues));
	}
	public function getTrailInfoMaxDistanceMaxValue($objForm)
	{
		$arrPids = deserialize($objForm->news_archives, true);
		$field = 'trailInfoDistanceMax';

		if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return NEWSPLUS_FILTER_SLIDER_DEFAULT_MIN;

		return ceil(max($arrValues));
	}
	public function getTrailInfoMaxDistanceValue()
	{
		return (Input::get('trailInfoDistanceMax') != null) ? Input::get('trailInfoDistanceMax') : NEWSPLUS_FILTER_SLIDER_DEFAULT_MAX;
	}


	public function getTrailInfoDurationValue($objForm, $arrConfig)
	{
		if ($arrConfig['type'] == 'range')
		{
			return (Input::get('trailInfoDuration') != null) ? Input::get('trailInfoDuration') : '[' . NEWSPLUS_FILTER_SLIDER_DEFAULT_MIN.','.NEWSPLUS_FILTER_SLIDER_DEFAULT_MAX . ']';
		}
		else {
			return (Input::get('trailInfoDuration') != null) ? Input::get('trailInfoDuration') : NEWSPLUS_FILTER_SLIDER_DEFAULT_MIN;
		}
	}

	public function getTrailInfoMinDurationMinValue($objForm)
	{
		$arrPids = deserialize($objForm->news_archives, true);
		$field = 'trailInfoDurationMin';

		if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return NEWSPLUS_FILTER_SLIDER_DEFAULT_MIN;

		return ceil(min($arrValues));
	}
	public function getTrailInfoMinDurationMaxValue($objForm)
	{
		$arrPids = deserialize($objForm->news_archives, true);
		$field = 'trailInfoDurationMin';

		if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return NEWSPLUS_FILTER_SLIDER_DEFAULT_MIN;

		return ceil(max($arrValues));
	}
	public function getTrailInfoMinDurationValue()
	{
		return (Input::get('trailInfoDurationMin') != null) ? Input::get('trailInfoDurationMin') : NEWSPLUS_FILTER_SLIDER_DEFAULT_MIN;
	}


	public function getTrailInfoMaxDurationMinValue($objForm)
	{
		$arrPids = deserialize($objForm->news_archives, true);
		$field = 'trailInfoDurationMax';

		if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return NEWSPLUS_FILTER_SLIDER_DEFAULT_MIN;

		return ceil(min($arrValues));
	}
	public function getTrailInfoMaxDurationMaxValue($objForm)
	{
		$arrPids = deserialize($objForm->news_archives, true);
		$field = 'trailInfoDurationMax';

		if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return NEWSPLUS_FILTER_SLIDER_DEFAULT_MIN;

		return ceil(max($arrValues));
	}
	public function getTrailInfoMaxDurationValue()
	{
		return (Input::get('trailInfoDurationMax') != null) ? Input::get('trailInfoDurationMax') : NEWSPLUS_FILTER_SLIDER_DEFAULT_MAX;
	}


	public function getTrailInfoDifficultyValue($objForm, $arrConfig)
	{
		if ($arrConfig['type'] == 'range')
		{
			return (Input::get('trailInfoDifficulty') != null) ? Input::get('trailInfoDifficulty') : '[' . NEWSPLUS_FILTER_SLIDER_DEFAULT_MIN.','.NEWSPLUS_FILTER_SLIDER_DEFAULT_MAX . ']';
		}
		else {
			return (Input::get('trailInfoDifficulty') != null) ? Input::get('trailInfoDifficulty') : NEWSPLUS_FILTER_SLIDER_DEFAULT_MAX;
		}
	}

	public function getTrailInfoMinDifficultyMinValue($objForm, $arrConfig)
	{
		if($arrConfig['type'] == 'ticks')
		{
			return NEWSPLUS_FILTER_SLIDER_DEFAULT_MIN;
		}
		else
		{
			$arrPids = deserialize($objForm->news_archives, true);
			$field = 'trailInfoDifficultyMin';

			if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return NEWSPLUS_FILTER_SLIDER_DEFAULT_MIN;

			return ceil(min($arrValues));
		}
	}
	public function getTrailInfoMinDifficultyMaxValue($objForm, $arrConfig)
	{
		if ($arrConfig['type'] == 'ticks')
		{
			$arrTicks = json_decode($arrConfig['ticks']);
			return $arrTicks[sizeof($arrTicks)-1];
		}
		else
		{
			$arrPids = deserialize($objForm->news_archives, true);
			$field = 'trailInfoDifficultyMin';

			if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return NEWSPLUS_FILTER_SLIDER_DEFAULT_MIN;

			return ceil(max($arrValues));
		}
	}
	public function getTrailInfoMinDifficultyValue($objForm, $arrConfig)
	{
		return (Input::get('trailInfoDifficultyMin') != null) ? Input::get('trailInfoDifficultyMin') : NEWSPLUS_FILTER_SLIDER_DEFAULT_MIN;
	}


	public function getTrailInfoMaxDifficultyMinValue($objForm, $arrConfig)
	{
		if($arrConfig['type'] == 'ticks')
		{
			return NEWSPLUS_FILTER_SLIDER_DEFAULT_MIN;
		}
		else
		{
			$arrPids = deserialize($objForm->news_archives, true);
			$field = 'trailInfoDifficultyMax';

			if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return NEWSPLUS_FILTER_SLIDER_DEFAULT_MIN;

			return ceil(min($arrValues));
		}
	}
	public function getTrailInfoMaxDifficultyMaxValue($objForm, $arrConfig)
	{
		if ($arrConfig['type'] == 'ticks')
		{
			$arrTicks = json_decode($arrConfig['ticks']);
			return $arrTicks[sizeof($arrTicks)-1];
		}
		else
		{
			$arrPids = deserialize($objForm->news_archives, true);
			$field = 'trailInfoDifficultyMax';

			if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return NEWSPLUS_FILTER_SLIDER_DEFAULT_MIN;

			return ceil(max($arrValues));
		}
	}
	public function getTrailInfoMaxDifficultyValue()
	{
		return (Input::get('trailInfoDifficultyMax') != null) ? Input::get('trailInfoDifficultyMax') : NEWSPLUS_FILTER_SLIDER_DEFAULT_MAX;
	}


	/**
	 * Get valid options for the given field from published news archives
	 * 
	 * @param $arrPids
	 * @param $field
	 * @param array $arrOptions
	 * 
	 * @return array|null
	 */
	private function getValidTrailInfoOptions($arrPids, $field, $arrOptions=array())
	{
		$arrNews = array();

		$objNewsCollection = NewsPlusModel::findPublishedByPid($arrPids, 0, $arrOptions);
		if ($objNewsCollection == null) return null;

		foreach ($objNewsCollection as $objNews)
		{
			if ($objNews->addTrailInfo == '1' && $objNews->addTrailInfoDistance == '1')
			{
				$arrNews[$objNews->{$field}] = $objNews->{$field};
			}
			if ($objNews->addTrailInfo == '1' && $objNews->addTrailInfoDuration == '1')
			{
				$arrNews[$objNews->{$field}] = $objNews->{$field};
			}
			if ($objNews->addTrailInfo == '1' && $objNews->addTrailInfoDifficulty == '1')
			{
				$arrNews[$objNews->{$field}] = $objNews->{$field};
			}
		}
		return $arrNews;
	}
}