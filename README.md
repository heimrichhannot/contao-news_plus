# News Plus

A collection of enhancements for the contao news module.

## Features

- Group news archive view by root (page-tree root), must be selected
- Dummy Image by news archive
- Module NewsListPlus
- Module NewsReaderPlus (possibility to show details in modal window, with next/prev event navigation and browser history support)
- Module Newsfilter (filter list by date, archive and search term, use contao search)


### Newsarchive

- added option values jump to day, year, month of latest published news for news_jumpToCurrent 

### Connected Modules

| Module  |  Description  |
|---|---|
heimrichhannot/contao-share | Add share functionality for sorcial media services and pdf download of news articles.
 
 
### Additional palettes & fields

Additional fields and palettes are listed below.

| Fieldname  | InputType | Description  | Palettes  | Reference
|---|---|---|---|
| orderEnclosureSRC  | none | Make news enclosures sortable. | default | - | 
| **addVenues** | checkbox | Add a venue to the news. | leisuretip | - | 
| venues | fieldpalette | The ids of multiple venues provided within its fieldpalette. | leisuretip | - |
| venueName | text | Name of the venue. | leisuretip | tl_fieldpalette
| venueStreet | text | Street of the venue. | leisuretip | tl_fieldpalette
| venuePostal | text | Postal code of the venue. | leisuretip | tl_fieldpalette
| venueCity | text | City of the venue. | leisuretip | tl_fieldpalette
| venueCountry | text | Country of the venue. | leisuretip | tl_fieldpalette
| venueSingleCoords | text | Geo coordinates of the venue (if dlh_geocode is installed, the coordinates are ascertain from venueStreet, venueCity and venuePostal . | leisuretip | tl_fieldpalette
| venuePhone | text | Phone of venue. | leisuretip | tl_fieldpalette
| venueFax | text | Fax of the venue. | leisuretip | tl_fieldpalette
| venueEmail | text | Email of the venue. | leisuretip | tl_fieldpalette
| venueWebsite | text | Website of the venue. | leisuretip | tl_fieldpalette
| venueText | textarea | Freetext field for venue informations. | leisuretip | tl_fieldpalette
| **addArrivalInfo** | checkbox | Add a arrival informations to the news.  | leisuretip | - |
| arrivalName | text | Name of the arrival location. | leisuretip | - |
| arrivalStreet | text | Street of the arrival location. | leisuretip | - |
| arrivalPostal | text | Postal code of the arrival location. | leisuretip | - |
| arrivalCity | text | City of the arrival location. | leisuretip | - |
| arrivalCountry | text | Country of the arrival location. | leisuretip | - |
| arrivalSingleCoords | text | Geo coordinates of the arrival location (if dlh_geocode is installed, the coordinates are ascertain from arrivalStreet, arrivalCity and arrivalPostal .  | leisuretip | - |
| arrivalText | textarea | Freetext field for arrival informations. | leisuretip | - |
| **addTouristInfo** | checkbox | Add tourist infos to the news.  | leisuretip | - |
| touristInfoName | text | Name of the tourist info. | leisuretip | - |
| touristInfoPhone | text | Phone of the tourist info. | leisuretip | - |
| touristInfoFax | text | Fax of the tourist info. | leisuretip | - |
| touristInfoEmail | text | Email of the tourist info. | leisuretip | - |
| touristInfoWebsite | text | Website of the tourist info. | leisuretip | - |
| touristInfoText | textarea | Additional text of the tourist info using tinyMCE.  | leisuretip | - |
| **addOpeningHours** | checkbox | Add opening hourse to the news. | leisuretip | - |
| openingHoursText | textarea | Freetext field for opening hours. | leisuretip | - |
| **addTicketPrice** | checkbox | Add ticket prices to the news. | leisuretip | - |
| ticketPriceText | textarea | Freetext field for ticket prices. | leisuretip | - |


**Bold** fields represent a `__selector__` and the fields contained in its subpalette are listed below.**    
