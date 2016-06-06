# News Plus

A collection of enhancements for the contao news module.

## Features

- Group news archive view by root (page-tree root), must be selected
- Dummy Image by news archive

##### Newsarchive

- added option values jump to day, year, month of latest published news for news_jumpToCurrent 

### Modules

| Name | Description |
|---|---|
| ModuleNewsFilter | filter list by chosen palette and fields (filter list by date, archive and search term, use contao search) |
| ModuleNewsListPlus | extend the contao news list |
| ModuleNewsPlus | extend the contao news module |
| ModuleNewsReaderPlus | extend the contao news reader (possibility to show details in modal window, with next/prev event navigation and browser history support) |
| ModuleNewsMenuPlus | extend the contao newsarchive menu |


#### ModuleNewsMenuPlus - Features
- add news_jumpToCurrent to newsarchive-menu as well, to provide active state in menu, when no user selection was made

### Connected Modules

| Module  |  Description  |
|---|---|
| heimrichhannot/contao-share | Add share functionality for sorcial media services and pdf download of news articles.
 
 
### Fields

**Bold** fields represent a `__selector__` and the fields contained in its subpalette are listed below.

tl_module:

| Fieldname | InputType | Description | Palettes |
|---|---|---|---|
| news_filterUseSearchIndex | checkbox | the keywordfilter will use the contao search index | newsfilter |
| news_filterFuzzySearch | checkbox | search for similar words | newsfilter |
| news_filterSearchQueryType | checkbox | 'and' or 'or' search for keywords | newsfilter |
| news_filterNewsCategoryArchives | checkbox | news archives for categories to show in the filter | newsfilter |
| news_readerModule | select | the reader to show the news from the list | newslist_plus, newslist_highlight |
| news_template_modal | select | the template for the modal window | newsreader_plus |
| news_showInModal | checkbox | show the news in a modal window | newslist_plus, newslist_highlight |
| news_filterModule | select | the filter connected to the list | newslist_plus |
| news_filterDefaultExclude | treepicker | exclude all news with the selected categories | newslist_plus |
| **news_archiveTitleAppendCategories** | checkbox | append the title of the category to the title of the archive | newslist_plus |
| news_archiveTitleCategories | treepicker | the categories whose titles will be added to the title of the archive | news_archiveTitleAppendCategories |
| **news_addNavigation** | checkbox | add a navigation to the reader | newsreader_plus |
| news_navigation_template | select | the template for the navigation | news_addNavigation |
| news_navigation_infinite | checkbox | infinite navigation through the news | news_addNavigation |

tl_news:

| Fieldname  | InputType | Description  | Palettes  | 
|---|---|---|---|
| orderEnclosureSRC  | none | Make news enclosures sortable. | default  |
| **addVenues** | checkbox | Add a venue to the news. | leisuretip | - | 
| venues | fieldpalette | The ids of multiple venues provided within its fieldpalette. | leisuretip | - |
| venueName | text | Name of the venue. | leisuretip |
| venueStreet | text | Street of the venue. | leisuretip |
| venuePostal | text | Postal code of the venue. | leisuretip |
| venueCity | text | City of the venue. | leisuretip |
| venueCountry | text | Country of the venue. | leisuretip |
| venueSingleCoords | text | Geo coordinates of the venue (if dlh_geocode is installed, the coordinates are ascertain from venueStreet, venueCity and venuePostal . | leisuretip |
| venuePhone | text | Phone of venue. | leisuretip |
| venueFax | text | Fax of the venue. | leisuretip |
| venueEmail | text | Email of the venue. | leisuretip |
| venueWebsite | text | Website of the venue. | leisuretip |
| venueText | textarea | Freetext field for venue informations. | leisuretip |
| **addArrivalInfo** | checkbox | Add a arrival informations to the news.  | leisuretip |
| arrivalName | text | Name of the arrival location. | leisuretip |
| arrivalStreet | text | Street of the arrival location. | leisuretip |
| arrivalPostal | text | Postal code of the arrival location. | leisuretip |
| arrivalCity | text | City of the arrival location. | leisuretip |
| arrivalCountry | text | Country of the arrival location. | leisuretip |
| arrivalSingleCoords | text | Geo coordinates of the arrival location (if dlh_geocode is installed, the coordinates are ascertain from arrivalStreet, arrivalCity and arrivalPostal .  | leisuretip |
| arrivalText | textarea | Freetext field for arrival informations. | leisuretip |
| **addTouristInfo** | checkbox | Add tourist infos to the news.  | leisuretip |
| touristInfoName | text | Name of the tourist info. | leisuretip |
| touristInfoPhone | text | Phone of the tourist info. | leisuretip |
| touristInfoFax | text | Fax of the tourist info. | leisuretip |
| touristInfoEmail | text | Email of the tourist info. | leisuretip |
| touristInfoWebsite | text | Website of the tourist info. | leisuretip |
| touristInfoText | textarea | Additional text of the tourist info using tinyMCE.  | leisuretip |
| **addOpeningHours** | checkbox | Add opening hourse to the news. | leisuretip |
| openingHoursText | textarea | Freetext field for opening hours. | leisuretip |
| **addTicketPrice** | checkbox | Add ticket prices to the news. | leisuretip |
| ticketPriceText | textarea | Freetext field for ticket prices. | leisuretip |
| **addTrailInfo** | checkbox | Add trail information to the news | leisuretip |
| **addTrailInfoDistance** | checkbox | add distance information | addTrailInfo |
| trailInfoDistanceMin | text | minimal distance of the trail | addTrailInfoDistance |
| trailInfoDistanceMax | text | maximum distance of the trail | addTrailInfoDistance |
| **addTrailInfoDuration** | checkbox | add duration information | addTrailInfo |
| trailInfoDurationMin | text | minimal duration of the trail | addTrailInfoDuration |
| trailInfoDurationMax | text | maximum duration of the trail | addTrailInfoDuration |
| **addTrailInfoAltitude** | checkbox | add altitude information | addTrailInfo |
| trailInfoAltitudeMin | text | minimal altitude of the trail | addTrailInfoAltitude |
| trailInfoAltitudeMax | text | maximum altitude of the trail | addTrailInfoAltitude |
| **addTrailInfoDifficulty** | checkbox | add difficulty information | addTrailInfo |
| trailInfoDifficultyMin | select | minimal difficulty of the trail | addTrailInfoDifficulty |
| trailInfoDifficultyMax | select | maximum difficulty of the trail | addTrailInfoDifficulty |
| **addTrailInfoStartDestination** | checkbox | add start and destination information | addTrailInfo |
| trailInfoStart | text | start of the trail | addTrailInfoStartDestination |
| trailInfoDestination | text | destination of the trail | addTrailInfoStartDestination |
| **addTrailInfoKmlData** | checkbox | add KML data and show them as route on google maps | addTrailInfo |
| trailInfoKmlData | fileTree | KML data | addTrailInfoKmlData |
| trailInfoShowElevationProfile | checkbox | shows an elevation profile of the KML route | addTrailInfoKmlData |

tl_news_archive:

| Fieldname | InputType | Description | Palettes |
|---|---|---|---|
| displayTitle | text | the displayed title of the archive | default |
| root | select | the affiliation of the archive to the selected starting point in the pagetree | default |
| **addDummyImage** | checkbox | add a placeholder image to news | default |
| dummyImageSingleSRC | filetree | the placeholder image | addDummyImage |
| **replaceNewsPalette** | checkbox | replace the default palette | default |
| newsPalette | select | the palette used to replace | replaceNewsPalette |

tl_newsfilter:

| Fieldname | InputType | Description | Palettes |
|---|---|---|---|
| q | text | search for keywords | default |
| pid | select | search for archives | default |
| cat | select | search for categories | default |
| startDate | text | search for start date | default |
| endDate | text | search for end date | default |
| submit | submit | submit button label | default |
| trailInfoDistanceMin | text | search for minimal distance | leisuretip |
| trailInfoDistanceMax | text | search for maximum distance | leisuretip |
| trailInfoDurationMin | text | search for minimal duration | leisuretip |
| trailInfoDurationMax | text | search for maximum duration | leisuretip |
| trailInfoDifficultyMin | select | search for minimal difficulty | leisuretip |
| trailInfoDifficultyMax | select | search for maximum difficulty | leisuretip |
| trailInfoStart | text | search for start location | leisuretip |
| trailInfoDestination | text | search for destination location | leisuretip |


### Field Callbacks

tl_module:

| Type | Description |
|----|-----------|
| onload_callback | set default values for the filter |


tl_news:

| Type | Description |
|---|---|
| onload_callback | archive has replaceNewsPalette set to 'true' and a newsPalette given -> replace the default news palette with the given one |


### Hooks

used:

| Hook name | Function name | Description |
|---|---|---|
|parseArticles | parseArticlesHook | add placeholder images to news; sort enclosures in the template according to the order as specified in the article |

offered:

| Name | Arguments | Description |
|---|---|---|
| modifyNewsFilterDca | $objNewsFilterForm->dca, $objNewsFilterForm | Triggered in FormHybrid\DC_Hybrid::loadDC |
| afterNewsFilterSubmitCallback | $objNewsFilterForm | Triggered in FormHybrid\Form::processForm after the submit of the form |
| loadDataContainer | $objDataContainer->table | Triggered in tl_news::initDefaultPalette in the onload_callback of the dca |
| newsListPlusFetchItems | $newsArchives, $blnFeatured, $limit, $offset, $arrFilterIds, $newsCategories, $this->startDate, $this->endDate, $objNewsListPlus | Triggered in ModuleNewsListPlus::fetchItems before finding the news |
| parseArticles | $objFrontendTemplate, $objArticle->row(), $objNewsPlus | Triggered in ModuleNewsPlus::parseArticle right before parsing the template |
| parseAllArticles | $arrArticles, $blnAddArchive, $objNewsPlus | Triggered in ModuleNewsPlus::parseArticles after parsing all articles |
