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
 * Selectors
 */
$dc['palettes']['__selector__'][] = 'addVenues';
$dc['palettes']['__selector__'][] = 'addTouristInfo';
$dc['palettes']['__selector__'][] = 'addOpeningHours';
$dc['palettes']['__selector__'][] = 'addTicketPrice';
$dc['palettes']['__selector__'][] = 'addArrivalInfo';
$dc['palettes']['__selector__'][] = 'addTrailInfo';
$dc['palettes']['__selector__'][] = 'addTrailInfoDistance';
$dc['palettes']['__selector__'][] = 'addTrailInfoDuration';
$dc['palettes']['__selector__'][] = 'addTrailInfoAltitude';
$dc['palettes']['__selector__'][] = 'addTrailInfoDifficulty';
$dc['palettes']['__selector__'][] = 'addTrailInfoStartDestination';
$dc['palettes']['__selector__'][] = 'addTrailInfoKmlData';


/**
 * Palettes
 */
$strLeisureTipFieldset        =
    '{venue_legend:hide},addVenues,addArrivalInfo;{touristInfo_legend:hide},addTouristInfo;{trailInfo_legend:hide},addTrailInfo;{openingHours_legend:hide},addOpeningHours;{ticketprice_legend:hide},addTicketPrice;';
$dc['palettes']['leisuretip'] = $dc['palettes']['default'];
$dc['palettes']['leisuretip'] = str_replace('addImage;', 'addImage;' . $strLeisureTipFieldset, $dc['palettes']['leisuretip']);

$strLeisureTipStageFieldset         = '{venue_legend:hide},addVenues;{trailInfo_legend:hide},addTrailInfo;';
$dc['palettes']['leisuretip_stage'] = $dc['palettes']['default'];
$dc['palettes']['leisuretip_stage'] = str_replace('addImage;', 'addImage;' . $strLeisureTipStageFieldset, $dc['palettes']['leisuretip_stage']);

/**
 * Subpalettes
 */
$dc['subpalettes']['addVenues']                    = 'venues';
$dc['subpalettes']['addArrivalInfo']               = 'arrivalName,arrivalStreet,arrivalPostal,arrivalCity,arrivalCountry,arrivalSingleCoords,arrivalText';
$dc['subpalettes']['addTouristInfo']               = 'touristInfoName,touristInfoPhone,touristInfoFax,touristInfoEmail,touristInfoWebsite,touristInfoText';
$dc['subpalettes']['addTrailInfo']                 =
    'addTrailInfoDistance,addTrailInfoDuration,addTrailInfoAltitude,addTrailInfoDifficulty,addTrailInfoStartDestination,addTrailInfoKmlData';
$dc['subpalettes']['addOpeningHours']              = 'openingHoursText';
$dc['subpalettes']['addTicketPrice']               = 'ticketPriceText';
$dc['subpalettes']['addTrailInfoDistance']         = 'trailInfoDistanceMin,trailInfoDistanceMax';
$dc['subpalettes']['addTrailInfoDuration']         = 'trailInfoDurationMin,trailInfoDurationMax';
$dc['subpalettes']['addTrailInfoAltitude']         = 'trailInfoAltitudeMin,trailInfoAltitudeMax';
$dc['subpalettes']['addTrailInfoDifficulty']       = 'trailInfoDifficultyMin,trailInfoDifficultyMax';
$dc['subpalettes']['addTrailInfoStartDestination'] = 'trailInfoStart,trailInfoDestination';
$dc['subpalettes']['addTrailInfoKmlData']          = 'trailInfoKmlData,trailInfoShowElevationProfile';


/**
 * Fields
 */
$arrFields = array(
    // venue
    'addVenues'                     => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['addVenues'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => array('submitOnChange' => true),
        'sql'       => "char(1) NOT NULL default ''",
    ),
    'venues'                        => array(
        'label'        => &$GLOBALS['TL_LANG']['tl_news']['venues'],
        'inputType'    => 'fieldpalette',
        'foreignKey'   => 'tl_fieldpalette.id',
        'relation'     => array('type' => 'hasMany', 'load' => 'eager'),
        'sql'          => "blob NULL",
        'fieldpalette' => array(
            'list'     => array(
                'label' => array(
                    'fields' => array('venueName', 'venueStreet', 'venuePostal', 'venueCity'),
                    'format' => '%s <span style="color:#b3b3b3;padding-left:3px">[%s, %s %s]</span>',
                ),
            ),
            'palettes' => array(
                'default' => '{venue_address_legend},venueName,venueStreet,venuePostal,venueCity,venueCountry,venueSingleCoords;{venue_contact_legend},venuePhone,venueFax,venueEmail,venueWebsite;{venue_text_legend},venueText',
            ),
            'fields'   => array(
                'venueName'         => array(
                    'label'     => &$GLOBALS['TL_LANG']['tl_news']['venueName'],
                    'exclude'   => true,
                    'search'    => true,
                    'inputType' => 'text',
                    'eval'      => array('maxlength' => 128, 'tl_class' => 'long'),
                    'sql'       => "varchar(128) NOT NULL default ''",
                ),
                'venueStreet'       => array(
                    'label'     => &$GLOBALS['TL_LANG']['tl_news']['venueStreet'],
                    'exclude'   => true,
                    'search'    => true,
                    'inputType' => 'text',
                    'eval'      => array('maxlength' => 64, 'tl_class' => 'w50'),
                    'sql'       => "varchar(64) NOT NULL default ''",
                ),
                'venuePostal'       => array(
                    'label'     => &$GLOBALS['TL_LANG']['tl_news']['venuePostal'],
                    'exclude'   => true,
                    'search'    => true,
                    'inputType' => 'text',
                    'eval'      => array('maxlength' => 5, 'tl_class' => 'w50'),
                    'sql'       => "varchar(5) NOT NULL default ''",
                ),
                'venueCity'         => array(
                    'label'     => &$GLOBALS['TL_LANG']['tl_news']['venueCity'],
                    'exclude'   => true,
                    'filter'    => true,
                    'search'    => true,
                    'sorting'   => true,
                    'inputType' => 'text',
                    'eval'      => array('maxlength' => 64, 'tl_class' => 'w50'),
                    'sql'       => "varchar(64) NOT NULL default ''",
                ),
                'venueCountry'      => array(
                    'label'     => &$GLOBALS['TL_LANG']['tl_news']['venueCountry'],
                    'exclude'   => true,
                    'filter'    => true,
                    'sorting'   => true,
                    'inputType' => 'select',
                    'options'   => System::getCountries(),
                    'eval'      => array('includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'),
                    'sql'       => "varchar(2) NOT NULL default ''",
                ),
                'venueSingleCoords' => array(
                    'label'         => &$GLOBALS['TL_LANG']['tl_news']['venueSingleCoords'],
                    'exclude'       => true,
                    'search'        => true,
                    'inputType'     => 'text',
                    'eval'          => array('maxlength' => 64),
                    'sql'           => "varchar(64) NOT NULL default ''",
                    'save_callback' => array(
                        array('tl_news_plus_leisuretip', 'generateVenueCoords'),
                    ),
                ),
                'venuePhone'        => array(
                    'label'     => &$GLOBALS['TL_LANG']['tl_news']['venuePhone'],
                    'exclude'   => true,
                    'search'    => true,
                    'inputType' => 'text',
                    'eval'      => array(
                        'maxlength'      => 32,
                        'rgxp'           => 'phone',
                        'decodeEntities' => true,
                        'tl_class'       => 'w50',
                    ),
                    'sql'       => "varchar(32) NOT NULL default ''",
                ),
                'venueFax'          => array(
                    'label'     => &$GLOBALS['TL_LANG']['tl_news']['venueFax'],
                    'exclude'   => true,
                    'search'    => true,
                    'inputType' => 'text',
                    'eval'      => array(
                        'maxlength'      => 32,
                        'rgxp'           => 'phone',
                        'decodeEntities' => true,
                        'tl_class'       => 'w50',
                    ),
                    'sql'       => "varchar(32) NOT NULL default ''",
                ),
                'venueEmail'        => array(
                    'label'     => &$GLOBALS['TL_LANG']['tl_news']['venueEmail'],
                    'exclude'   => true,
                    'search'    => true,
                    'inputType' => 'text',
                    'eval'      => array(
                        'maxlength'      => 64,
                        'rgxp'           => 'email',
                        'decodeEntities' => true,
                        'tl_class'       => 'w50',
                    ),
                    'sql'       => "varchar(64) NOT NULL default ''",
                ),
                'venueWebsite'      => array(
                    'label'     => &$GLOBALS['TL_LANG']['tl_news']['venueWebsite'],
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
                'venueText'         => array(
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
    'addArrivalInfo'                => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['addArrivalInfo'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => array('submitOnChange' => true),
        'sql'       => "char(1) NOT NULL default ''",
    ),
    'arrivalName'                   => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['arrivalName'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'text',
        'eval'      => array('maxlength' => 128, 'tl_class' => 'long'),
        'sql'       => "varchar(128) NOT NULL default ''",
    ),
    'arrivalStreet'                 => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['arrivalStreet'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'text',
        'eval'      => array('maxlength' => 64, 'tl_class' => 'w50'),
        'sql'       => "varchar(64) NOT NULL default ''",
    ),
    'arrivalPostal'                 => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['arrivalPostal'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'text',
        'eval'      => array('maxlength' => 5, 'tl_class' => 'w50'),
        'sql'       => "varchar(5) NOT NULL default ''",
    ),
    'arrivalCity'                   => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['arrivalCity'],
        'exclude'   => true,
        'filter'    => true,
        'search'    => true,
        'sorting'   => true,
        'inputType' => 'text',
        'eval'      => array('maxlength' => 64, 'tl_class' => 'w50'),
        'sql'       => "varchar(64) NOT NULL default ''",
    ),
    'arrivalCountry'                => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['arrivalCountry'],
        'exclude'   => true,
        'filter'    => true,
        'sorting'   => true,
        'inputType' => 'select',
        'options'   => System::getCountries(),
        'eval'      => array('includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'),
        'sql'       => "varchar(2) NOT NULL default ''",
    ),
    'arrivalSingleCoords'           => array(
        'label'         => &$GLOBALS['TL_LANG']['tl_news']['arrivalSingleCoords'],
        'exclude'       => true,
        'search'        => true,
        'inputType'     => 'text',
        'eval'          => array('maxlength' => 64),
        'sql'           => "varchar(64) NOT NULL default ''",
        'save_callback' => array(
            array('tl_news_plus_leisuretip', 'generateArrivalCoords'),
        ),
    ),
    'arrivalText'                   => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['arrivalText'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'textarea',
        'eval'      => array('rte' => 'tinyMCE', 'tl_class' => 'clr'),
        'sql'       => "text NULL",
    ),
    // tourist info
    'addTouristInfo'                => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['addTouristInfo'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => array('submitOnChange' => true),
        'sql'       => "char(1) NOT NULL default ''",
    ),
    'touristInfoName'               => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['touristInfoName'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'text',
        'eval'      => array('maxlength' => 128, 'tl_class' => 'long'),
        'sql'       => "varchar(128) NOT NULL default ''",
    ),
    'touristInfoPhone'              => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['touristInfoPhone'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'text',
        'eval'      => array(
            'maxlength'      => 32,
            'rgxp'           => 'phone',
            'decodeEntities' => true,
            'tl_class'       => 'w50',
        ),
        'sql'       => "varchar(32) NOT NULL default ''",
    ),
    'touristInfoFax'                => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['touristInfoFax'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'text',
        'eval'      => array(
            'maxlength'      => 32,
            'rgxp'           => 'phone',
            'decodeEntities' => true,
            'tl_class'       => 'w50',
        ),
        'sql'       => "varchar(32) NOT NULL default ''",
    ),
    'touristInfoEmail'              => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['touristInfoEmail'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'text',
        'eval'      => array(
            'maxlength'      => 64,
            'rgxp'           => 'email',
            'decodeEntities' => true,
            'tl_class'       => 'w50',
        ),
        'sql'       => "varchar(64) NOT NULL default ''",
    ),
    'touristInfoWebsite'            => array(
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
    'touristInfoText'               => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['touristInfoText'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'textarea',
        'eval'      => array('rte' => 'tinyMCE', 'tl_class' => 'clr'),
        'sql'       => "text NULL",
    ),
    // trail info
    'addTrailInfo'                  => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['addTrailInfo'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => array('submitOnChange' => true),
        'sql'       => "char(1) NOT NULL default ''",
    ),
    'addTrailInfoDistance'          => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['addTrailInfoDistance'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => array('submitOnChange' => true, 'tl_class' => 'long'),
        'sql'       => "char(1) NOT NULL default ''",
    ),
    'trailInfoDistanceMin'          => array(
        'label'         => &$GLOBALS['TL_LANG']['tl_news']['trailInfoDistanceMin'],
        'exclude'       => true,
        'search'        => true,
        'inputType'     => 'text',
        'save_callback' => array(array('tl_news_plus_leisuretip', 'formatCommaToDot')),
        'eval'          => array('tl_class' => 'w50'),
        'sql'           => "float(4,1) NOT NULL default '0.0'",
    ),
    'trailInfoDistanceMax'          => array(
        'label'         => &$GLOBALS['TL_LANG']['tl_news']['trailInfoDistanceMax'],
        'exclude'       => true,
        'search'        => true,
        'inputType'     => 'text',
        'save_callback' => array(array('tl_news_plus_leisuretip', 'formatCommaToDot')),
        'eval'          => array('tl_class' => 'w50', 'mandatory' => true),
        'sql'           => "float(4,1) NOT NULL default '0.0'",
    ),
    'addTrailInfoDuration'          => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['addTrailInfoDuration'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => array('submitOnChange' => true, 'tl_class' => 'long'),
        'sql'       => "char(1) NOT NULL default ''",
    ),
    'trailInfoDurationMin'          => array(
        'label'         => &$GLOBALS['TL_LANG']['tl_news']['trailInfoDurationMin'],
        'exclude'       => true,
        'search'        => true,
        'inputType'     => 'text',
        'save_callback' => array(array('tl_news_plus_leisuretip', 'formatCommaToDot')),
        'eval'          => array('tl_class' => 'w50'),
        'sql'           => "float(4,1) NOT NULL default '0.0'",
    ),
    'trailInfoDurationMax'          => array(
        'label'         => &$GLOBALS['TL_LANG']['tl_news']['trailInfoDurationMax'],
        'exclude'       => true,
        'search'        => true,
        'inputType'     => 'text',
        'save_callback' => array(array('tl_news_plus_leisuretip', 'formatCommaToDot')),
        'eval'          => array('tl_class' => 'w50', 'mandatory' => true),
        'sql'           => "float(4,1) NOT NULL default '0.0'",
    ),
    'addTrailInfoAltitude'          => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['addTrailInfoAltitude'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => array('submitOnChange' => true, 'tl_class' => 'long'),
        'sql'       => "char(1) NOT NULL default ''",
    ),
    'trailInfoAltitudeMin'          => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['trailInfoAltitudeMin'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'text',
        'eval'      => array('tl_class' => 'w50'),
        'sql'       => "int(10) unsigned NOT NULL default '0'",
    ),
    'trailInfoAltitudeMax'          => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['trailInfoAltitudeMax'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'text',
        'eval'      => array('tl_class' => 'w50', 'mandatory' => true),
        'sql'       => "int(10) unsigned NOT NULL default '0'",
    ),
    'addTrailInfoDifficulty'        => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['addTrailInfoDifficulty'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => array('submitOnChange' => true, 'tl_class' => 'long'),
        'sql'       => "char(1) NOT NULL default ''",
    ),
    'trailInfoDifficultyMin'        => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['trailInfoDifficultyMin'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'select',
        'options'   => array(0, 1, 2, 3),
        'reference' => &$GLOBALS['TL_LANG']['tl_news']['trailInfoDifficulties'],
        'eval'      => array('tl_class' => 'w50', 'includeBlankOption' => true),
        'sql'       => "int(10) unsigned NOT NULL default '0'",
    ),
    'trailInfoDifficultyMax'        => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['trailInfoDifficultyMax'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'select',
        'options'   => array(0, 1, 2, 3),
        'reference' => &$GLOBALS['TL_LANG']['tl_news']['trailInfoDifficulties'],
        'eval'      => array('tl_class' => 'w50', 'mandatory' => true),
        'sql'       => "int(10) unsigned NOT NULL default '1'",
    ),
    'addTrailInfoStartDestination'  => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['addTrailInfoStartDestination'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => array('submitOnChange' => true, 'tl_class' => 'long'),
        'sql'       => "char(1) NOT NULL default ''",
    ),
    'trailInfoStart'                => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['trailInfoStart'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'text',
        'eval'      => array('maxlength' => 128, 'tl_class' => 'w50', 'mandatory' => true),
        'sql'       => "varchar(128) NOT NULL default ''",
    ),
    'trailInfoDestination'          => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['trailInfoDestination'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'text',
        'eval'      => array('maxlength' => 128, 'tl_class' => 'w50', 'mandatory' => true),
        'sql'       => "varchar(128) NOT NULL default ''",
    ),
    'addTrailInfoKmlData'           => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['addTrailInfoKmlData'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => array('submitOnChange' => true, 'tl_class' => 'long'),
        'sql'       => "char(1) NOT NULL default ''",
    ),
    'trailInfoKmlData'              => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['trailInfoKmlData'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'fileTree',
        'eval'      => array(
            'extensions' => 'kml',
            'fieldType'  => 'radio',
            'files'      => true,
            'mandatory'  => true,
            'tl_class'   => 'w50',
        ),
        'sql'       => "blob NULL",
    ),
    'trailInfoShowElevationProfile' => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['trailInfoShowElevationProfile'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => array('tl_class' => 'w50'),
        'sql'       => "char(1) NOT NULL default ''",
    ),
    // opening hours
    'addOpeningHours'               => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['addOpeningHours'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => array('submitOnChange' => true),
        'sql'       => "char(1) NOT NULL default ''",
    ),
    'openingHoursText'              => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['openingHoursText'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'textarea',
        'eval'      => array('rte' => 'tinyMCE', 'tl_class' => 'clr', 'mandatory' => true),
        'sql'       => "text NULL",
    ),
    // tickets
    'addTicketPrice'                => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['addTicketPrice'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => array('submitOnChange' => true),
        'sql'       => "char(1) NOT NULL default ''",
    ),
    'ticketPriceText'               => array(
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['ticketPriceText'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'textarea',
        'eval'      => array('rte' => 'tinyMCE', 'tl_class' => 'clr', 'mandatory' => true),
        'sql'       => "text NULL",
    ),
);

$dc['fields'] = array_merge($dc['fields'], $arrFields);

class tl_news_plus_leisuretip extends tl_news_plus
{
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
        if ($varValue != '')
        {
            return $varValue;
        }

        $strAddress = '';

        if ($dc->activeRecord->venueStreet != '')
        {
            $strAddress .= $dc->activeRecord->venueStreet;
        }

        if ($dc->activeRecord->venuePostal != '' && $dc->activeRecord->venueCity)
        {
            $strAddress .= ($strAddress ? ',' : '') . $dc->activeRecord->venuePostal . ' ' . $dc->activeRecord->venueCity;
        }

        if (($strCoords = $this->generateCoordsFromAddress($strAddress, $dc->activeRecord->venueCountry ?: 'de')) !== false)
        {
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
        if ($varValue != '')
        {
            return $varValue;
        }

        $strAddress = '';

        if ($dc->activeRecord->arrivalStreet != '')
        {
            $strAddress .= $dc->activeRecord->arrivalStreet;
        }

        if ($dc->activeRecord->arrivalPostal != '' && $dc->activeRecord->arrivalCity)
        {
            $strAddress .= ($strAddress ? ',' : '') . $dc->activeRecord->arrivalPostal . ' ' . $dc->activeRecord->arrivalCity;
        }

        if (($strCoords = $this->generateCoordsFromAddress($strAddress, $dc->activeRecord->arrivalCountry ?: 'de')) !== false)
        {
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
        if (!in_array('dlh_geocode', \ModuleLoader::getActive()))
        {
            return false;
        }

        return \delahaye\GeoCode::getCoordinates($strAddress, $strCountry, 'de');
    }

    public static function formatCommaToDot($value)
    {
        return str_replace(',', '.', $value);
    }
}