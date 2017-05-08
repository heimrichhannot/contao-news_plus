# Change Log
All notable changes to this project will be documented in this file.

## [2.4.23] - 2017-05-08

### Fixed
- slider issue (missing quared brackets)
- palette issues for leisure tips

## [2.4.22] - 2017-05-08

### Added
- do not render modal when share-print template is set within module config

## [2.4.21] - 2017-04-27

### Changed
- varchar lengths to reduce db size

## [2.4.20] - 2017-04-13

### Fixed
- fixed maxlength limit for html teaser

## [2.4.19] - 2017-04-13

### Changed
- fixed maxlength limit for html input length by archive restrictions

## [2.4.18] - 2017-04-03

### Added
- limit headline, subheadline and teaser input length by archive restrictions

## [2.4.16] - 2017-03-14

### Changed
- ModuleNewsArchive, if `news_jumpToCurrent` is set to `show_current` and no news available for current period, jump to period based on latest news date
- ModuleNewsMenuPlus, if `news_jumpToCurrent` is set to `show_current` and no news available for current period, jump to period based on latest news date

## [2.4.15] - 2017-02-15

### Fixed
- get correct marker action if useModal is active

## [2.4.14] - 2017-02-15

### Fixed
- get correct coordinates of multiple venues in one news article

## [2.4.13] - 2017-02-14

### Fixed
- resolved error in link on map marker

## [2.4.12] - 2017-02-14

### Added
- useModal to palette to enable opening of linked news in modal-module

### Fixed
- multiple venues for one news article

## [2.4.11] - 2017-02-09

### Added
- added alternative search for news-items if search_index is not used

## [2.4.10] - 2016-12-19

### Added
- `tl_module.news_format_reference` : Overwrite jumpTo default date within `newsmenu_plus` and `news_archive_plus`
- Added `HeimrichHannot\NewsPlus\ModuleNewsArchive`
