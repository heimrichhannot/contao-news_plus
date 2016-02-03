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

$dc = &$GLOBALS['TL_DCA']['tl_news'];

/**
 * Config
 */
$dc['config']['onload_callback'][] = array('tl_news_plus', 'initDefaultPalette');

/**
 * Selectors
 */
$dc['palettes']['__selector__'][] = 'addVenues';
$dc['palettes']['__selector__'][] = 'addTouristInfo';
$dc['palettes']['__selector__'][] = 'addOpeningHours';
$dc['palettes']['__selector__'][] = 'addTicketPrice';
$dc['palettes']['__selector__'][] = 'addArrivalInfo';
/**
 * Palettes
 */
$strLeisureTipFieldset =
	'{venue_legend:hide},addVenues,addArrivalInfo;{touristInfo_legend:hide},addTouristInfo;{openingHours_legend:hide},addOpeningHours;{ticketprice_legend:hide},addTicketPrice;';

$dc['palettes']['leisuretip'] = $dc['palettes']['default'];
$dc['palettes']['leisuretip'] = str_replace('addImage;', 'addImage;' . $strLeisureTipFieldset, $dc['palettes']['leisuretip']);

/**
 * Subpalettes
 */
$dc['subpalettes']['addVenues']       = 'venues';
$dc['subpalettes']['addArrivalInfo']  = 'arrivalName,arrivalStreet,arrivalPostal,arrivalCity,arrivalCountry,arrivalSingleCoords,arrivalText';
$dc['subpalettes']['addTouristInfo']  = 'touristInfoName,touristInfoPhone,touristInfoFax,touristInfoEmail,touristInfoWebsite,touristInfoText';
$dc['subpalettes']['addOpeningHours'] = 'openingHoursText';
$dc['subpalettes']['addTicketPrice']  = 'ticketPriceText';


/**
 * Fields
 */
$arrFields = array
(
	// make enclosures sortable
	'orderEnclosureSRC'   => array
	(
		'label' => &$GLOBALS['TL_LANG']['tl_news']['orderEnclosureSRC'],
		'sql'   => "blob NULL",
	),
	// venue
	'addVenues'           => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news']['addVenues'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'eval'      => array('submitOnChange' => true),
		'sql'       => "char(1) NOT NULL default ''",
	),
	'venues'              => array
	(
		'label'        => &$GLOBALS['TL_LANG']['tl_news']['venues'],
		'inputType'    => 'fieldpalette',
		'foreignKey'   => 'tl_fieldpalette.id',
		'relation'     => array('type' => 'hasMany', 'load' => 'eager'),
		'sql'          => "blob NULL",
		'fieldpalette' => array
		(
			'list'     => array
			(
				'label' => array
				(
					'fields' => array('venueName', 'venueStreet', 'venuePostal', 'venueCity'),
					'format' => '%s <span style="color:#b3b3b3;padding-left:3px">[%s, %s %s]</span>',
				),
			),
			'palettes' => array
			(
				'default' => 'venueName,venueStreet,venuePostal,venueCity,venueCountry,venueSingleCoords,venuePhone,venueFax,venueEmail,venueWebsite,venueText',
			),
			'fields'   => array
			(
				'venueName'         => array
				(
					'label'     => &$GLOBALS['TL_LANG']['tl_news']['venueName'],
					'exclude'   => true,
					'search'    => true,
					'inputType' => 'text',
					'eval'      => array('maxlength' => 255, 'tl_class' => 'long'),
					'sql'       => "varchar(255) NOT NULL default ''",
				),
				'venueStreet'       => array
				(
					'label'     => &$GLOBALS['TL_LANG']['tl_news']['venueStreet'],
					'exclude'   => true,
					'search'    => true,
					'inputType' => 'text',
					'eval'      => array('maxlength' => 255, 'tl_class' => 'w50'),
					'sql'       => "varchar(255) NOT NULL default ''",
				),
				'venuePostal'       => array
				(
					'label'     => &$GLOBALS['TL_LANG']['tl_news']['venuePostal'],
					'exclude'   => true,
					'search'    => true,
					'inputType' => 'text',
					'eval'      => array('maxlength' => 32, 'tl_class' => 'w50'),
					'sql'       => "varchar(32) NOT NULL default ''",
				),
				'venueCity'         => array
				(
					'label'     => &$GLOBALS['TL_LANG']['tl_news']['venueCity'],
					'exclude'   => true,
					'filter'    => true,
					'search'    => true,
					'sorting'   => true,
					'inputType' => 'text',
					'eval'      => array('maxlength' => 255, 'tl_class' => 'w50'),
					'sql'       => "varchar(255) NOT NULL default ''",
				),
				'venueCountry'      => array
				(
					'label'     => &$GLOBALS['TL_LANG']['tl_news']['venueCountry'],
					'exclude'   => true,
					'filter'    => true,
					'sorting'   => true,
					'inputType' => 'select',
					'options'   => System::getCountries(),
					'eval'      => array('includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'),
					'sql'       => "varchar(2) NOT NULL default ''",
				),
				'venueSingleCoords' => array
				(
					'label'         => &$GLOBALS['TL_LANG']['tl_news']['venueSingleCoords'],
					'exclude'       => true,
					'search'        => true,
					'inputType'     => 'text',
					'eval'          => array('maxlength' => 64),
					'sql'           => "varchar(64) NOT NULL default ''",
					'save_callback' => array
					(
						array('tl_news_plus', 'generateVenueCoords'),
					),
				),
				'venueText'         => array
				(
					'label'     => &$GLOBALS['TL_LANG']['tl_news']['venueText'],
					'exclude'   => true,
					'search'    => true,
					'inputType' => 'textarea',
					'eval'      => array('rte' => 'tinyMCE', 'tl_class' => 'clr'),
					'sql'       => "text NULL",
				),
			),
		),
	),
	// arrival infos
	'addArrivalInfo'      => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news']['addArrivalInfo'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'eval'      => array('submitOnChange' => true),
		'sql'       => "char(1) NOT NULL default ''",
	),
	'arrivalName'         => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news']['arrivalName'],
		'exclude'   => true,
		'search'    => true,
		'inputType' => 'text',
		'eval'      => array('maxlength' => 255, 'tl_class' => 'long'),
		'sql'       => "varchar(255) NOT NULL default ''",
	),
	'arrivalStreet'       => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news']['arrivalStreet'],
		'exclude'   => true,
		'search'    => true,
		'inputType' => 'text',
		'eval'      => array('maxlength' => 255, 'tl_class' => 'w50'),
		'sql'       => "varchar(255) NOT NULL default ''",
	),
	'arrivalPostal'       => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news']['arrivalPostal'],
		'exclude'   => true,
		'search'    => true,
		'inputType' => 'text',
		'eval'      => array('maxlength' => 32, 'tl_class' => 'w50'),
		'sql'       => "varchar(32) NOT NULL default ''",
	),
	'arrivalCity'         => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news']['arrivalCity'],
		'exclude'   => true,
		'filter'    => true,
		'search'    => true,
		'sorting'   => true,
		'inputType' => 'text',
		'eval'      => array('maxlength' => 255, 'tl_class' => 'w50'),
		'sql'       => "varchar(255) NOT NULL default ''",
	),
	'arrivalCountry'      => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news']['arrivalCountry'],
		'exclude'   => true,
		'filter'    => true,
		'sorting'   => true,
		'inputType' => 'select',
		'options'   => System::getCountries(),
		'eval'      => array('includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'),
		'sql'       => "varchar(2) NOT NULL default ''",
	),
	'arrivalSingleCoords' => array
	(
		'label'         => &$GLOBALS['TL_LANG']['tl_news']['arrivalSingleCoords'],
		'exclude'       => true,
		'search'        => true,
		'inputType'     => 'text',
		'eval'          => array('maxlength' => 64),
		'sql'           => "varchar(64) NOT NULL default ''",
		'save_callback' => array
		(
			array('tl_news_plus', 'generateArrivalCoords'),
		),
	),
	'arrivalText'         => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news']['arrivalText'],
		'exclude'   => true,
		'search'    => true,
		'inputType' => 'textarea',
		'eval'      => array('rte' => 'tinyMCE', 'tl_class' => 'clr'),
		'sql'       => "text NULL",
	),
	// tourist info
	'addTouristInfo'      => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news']['addTouristInfo'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'eval'      => array('submitOnChange' => true),
		'sql'       => "char(1) NOT NULL default ''",
	),
	'touristInfoName'     => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news']['touristInfoName'],
		'exclude'   => true,
		'search'    => true,
		'inputType' => 'text',
		'eval'      => array('maxlength' => 255, 'tl_class' => 'long'),
		'sql'       => "varchar(255) NOT NULL default ''",
	),
	'touristInfoPhone'    => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news']['touristInfoPhone'],
		'exclude'   => true,
		'search'    => true,
		'inputType' => 'text',
		'eval'      => array(
			'maxlength'      => 64,
			'rgxp'           => 'phone',
			'decodeEntities' => true,
			'tl_class'       => 'w50',
		),
		'sql'       => "varchar(64) NOT NULL default ''",
	),
	'touristInfoFax'      => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news']['touristInfoFax'],
		'exclude'   => true,
		'search'    => true,
		'inputType' => 'text',
		'eval'      => array(
			'maxlength'      => 64,
			'rgxp'           => 'phone',
			'decodeEntities' => true,
			'tl_class'       => 'w50',
		),
		'sql'       => "varchar(64) NOT NULL default ''",
	),
	'touristInfoEmail'    => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news']['touristInfoEmail'],
		'exclude'   => true,
		'search'    => true,
		'inputType' => 'text',
		'eval'      => array(
			'maxlength'      => 255,
			'rgxp'           => 'email',
			'decodeEntities' => true,
			'tl_class'       => 'w50',
		),
		'sql'       => "varchar(255) NOT NULL default ''",
	),
	'touristInfoWebsite'  => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news']['touristInfoWebsite'],
		'exclude'   => true,
		'search'    => true,
		'inputType' => 'text',
		'eval'      => array(
			'rgxp'      => 'url',
			'maxlength' => 255,
			'tl_class'  => 'w50',
		),
		'sql'       => "varchar(255) NOT NULL default ''",
	),
	'touristInfoText'     => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news']['touristInfoText'],
		'exclude'   => true,
		'search'    => true,
		'inputType' => 'textarea',
		'eval'      => array('rte' => 'tinyMCE', 'tl_class' => 'clr'),
		'sql'       => "text NULL",
	),
	// opening hours
	'addOpeningHours'     => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news']['addOpeningHours'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'eval'      => array('submitOnChange' => true),
		'sql'       => "char(1) NOT NULL default ''",
	),
	'openingHoursText'    => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news']['openingHoursText'],
		'exclude'   => true,
		'search'    => true,
		'inputType' => 'textarea',
		'eval'      => array('rte' => 'tinyMCE', 'tl_class' => 'clr', 'mandatory' => true),
		'sql'       => "text NULL",
	),
	// opening hours
	'addTicketPrice'      => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news']['addTicketPrice'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'eval'      => array('submitOnChange' => true),
		'sql'       => "char(1) NOT NULL default ''",
	),
	'ticketPriceText'     => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_news']['ticketPriceText'],
		'exclude'   => true,
		'search'    => true,
		'inputType' => 'textarea',
		'eval'      => array('rte' => 'tinyMCE', 'tl_class' => 'clr', 'mandatory' => true),
		'sql'       => "text NULL",
	),
);

$dc['fields'] = array_merge($dc['fields'], $arrFields);

$dc['fields']['enclosure']['eval']['orderField'] = 'orderEnclosureSRC'; // make enclosures sortable

class tl_news_plus extends Backend
{
	/**
	 * If news archive has replaceNewsPalette set and a newsPalette given,
	 * replace the default news palette with the given one
	 *
	 * @param DataContainer $dc
	 *
	 * @return bool
	 */
	public function initDefaultPalette(DataContainer $dc)
	{
		$objNews = \HeimrichHannot\NewsPlus\NewsPlusModel::findByPk($dc->id);

		if ($objNews === null) {
			return false;
		}

		$objArchive = $objNews->getRelated('pid');

		if ($objArchive === null) {
			return false;
		}

		if ($objArchive->replaceNewsPalette && $objArchive->newsPalette != '') {
			if (!isset($GLOBALS['TL_DCA']['tl_news']['palettes'][$objArchive->newsPalette])) {
				return false;
			}


			$GLOBALS['TL_DCA']['tl_news']['palettes']['default'] = $GLOBALS['TL_DCA']['tl_news']['palettes'][$objArchive->newsPalette];
		}

		// HOOK: loadDataContainer must be triggerd after onload_callback, otherwise slick slider wont work anymore
		if (isset($GLOBALS['TL_HOOKS']['loadDataContainer']) && is_array($GLOBALS['TL_HOOKS']['loadDataContainer'])) {
			foreach ($GLOBALS['TL_HOOKS']['loadDataContainer'] as $callback) {
				$this->import($callback[0]);
				$this->{$callback[0]}->{$callback[1]}($dc->table);
			}
		}
	}

	/**
	 *
	 * Get geo coodinates for the venue address
	 *
	 * @param               $varValue
	 * @param DataContainer $dc
	 *
	 * @return String The coordinates
	 */
	function generateVenueCoords($varValue, DataContainer $dc)
	{
		if ($varValue != '') {
			return $varValue;
		}

		$strAddress = '';

		if ($dc->activeRecord->venueStreet != '') {
			$strAddress = $dc->activeRecord->venueStreet;
		}

		if ($dc->activeRecord->venuePostal != '' && $dc->activeRecord->venueCity) {
			$strAddress = ($strAddress ? ($strAddress . ',') : '') . $dc->activeRecord->venuePostal . ' ' . $dc->activeRecord->venueCity;
		}

		if (($strCoords = $this->generateCoordsFromAddress($strAddress, $dc->activeRecord->venueCountry ?: 'de')) !== false) {
			$varValue = $strCoords;
		}

		return $varValue;
	}


	/**
	 *
	 * Get geo coodinates for the arrival address
	 *
	 * @param               $varValue
	 * @param DataContainer $dc
	 *
	 * @return String The coordinates
	 */
	public function generateArrivalCoords($varValue, DataContainer $dc)
	{
		if ($varValue != '') {
			return $varValue;
		}

		$strAddress = '';

		if ($dc->activeRecord->arrivalStreet != '') {
			$strAddress = $dc->activeRecord->arrivalStreet;
		}

		if ($dc->activeRecord->arrivalPostal != '' && $dc->activeRecord->arrivalCity) {
			$strAddress = ($strAddress ? ($strAddress . ',') : '') . $dc->activeRecord->arrivalPostal . ' ' . $dc->activeRecord->arrivalCity;
		}

		if (($strCoords = $this->generateCoordsFromAddress($strAddress, $dc->activeRecord->arrivalCountry ?: 'de')) !== false) {
			$varValue = $strCoords;
		}

		return $varValue;
	}

	/**
	 * @param $strAddress Address string
	 * @param $strCountry Country ISO 3166 code
	 *
	 * @return bool|string False if dlh_geocode is not installed, otherwise return the coordinates from address string
	 */
	private function generateCoordsFromAddress($strAddress, $strCountry)
	{
		if (!in_array('dlh_geocode', \ModuleLoader::getActive())) {
			return false;
		}
		
		return \delahaye\GeoCode::getCoordinates($strAddress, $strCountry, 'de');
	}
}