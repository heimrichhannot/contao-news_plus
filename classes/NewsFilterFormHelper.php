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

		return $arrValues;
	}
	public function getTrailInfoDestinationsFromPublishedNews(DataContainer $dc)
	{
		$arrPids = deserialize($dc->news_archives, true);
		$field = 'trailInfoDestination';

		if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return array();

		$arrValues = array_unique($arrValues);
		sort($arrValues);

		return $arrValues;
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


	public function getTrailInfoMinDistanceMinValue($objForm)
	{
		$arrPids = deserialize($objForm->news_archives, true);
		$field = 'trailInfoDistanceMin';

		if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return 0;

		return ceil(min($arrValues));
	}
	public function getTrailInfoMinDistanceMaxValue($objForm)
	{
		$arrPids = deserialize($objForm->news_archives, true);
		$field = 'trailInfoDistanceMin';

		if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return 0;

		return ceil(max($arrValues));
	}
	public function getTrailInfoMinDistanceValue()
	{
		return (Input::get('trailInfoDistanceMin') != null) ? Input::get('trailInfoDistanceMin') : 0;
	}


	public function getTrailInfoMaxDistanceMinValue($objForm)
	{
		$arrPids = deserialize($objForm->news_archives, true);
		$field = 'trailInfoDistanceMax';

		if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return 0;

		return ceil(min($arrValues));
	}
	public function getTrailInfoMaxDistanceMaxValue($objForm)
	{
		$arrPids = deserialize($objForm->news_archives, true);
		$field = 'trailInfoDistanceMax';

		if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return 0;

		return ceil(max($arrValues));
	}
	public function getTrailInfoMaxDistanceValue()
	{
		return (Input::get('trailInfoDistanceMax') != null) ? Input::get('trailInfoDistanceMax') : 100;
	}


	public function getTrailInfoMinDurationMinValue($objForm)
	{
		$arrPids = deserialize($objForm->news_archives, true);
		$field = 'trailInfoDurationMin';

		if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return 0;

		return ceil(min($arrValues));
	}
	public function getTrailInfoMinDurationMaxValue($objForm)
	{
		$arrPids = deserialize($objForm->news_archives, true);
		$field = 'trailInfoDurationMin';

		if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return 0;

		return ceil(max($arrValues));
	}
	public function getTrailInfoMinDurationValue()
	{
		return (Input::get('trailInfoDurationMin') != null) ? Input::get('trailInfoDurationMin') : 0;
	}


	public function getTrailInfoMaxDurationMinValue($objForm)
	{
		$arrPids = deserialize($objForm->news_archives, true);
		$field = 'trailInfoDurationMax';

		if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return 0;

		return ceil(min($arrValues));
	}
	public function getTrailInfoMaxDurationMaxValue($objForm)
	{
		$arrPids = deserialize($objForm->news_archives, true);
		$field = 'trailInfoDurationMax';

		if (($arrValues = $this->getValidTrailInfoOptions($arrPids, $field)) === null) return 0;

		return ceil(max($arrValues));
	}
	public function getTrailInfoMaxDurationValue()
	{
		return (Input::get('trailInfoDurationMax') != null) ? Input::get('trailInfoDurationMax') : 100;
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
				$arrNews[$objNews->$field] = $objNews->$field;
			}
		}
		return $arrNews;
	}
}