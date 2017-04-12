# Change Log
All notable changes to this project will be documented in this file.

## [1.0.43] - 2017-04-06

### Changed
- added php7 support. fixed contao.core dependency
- changed "String" to "StringUtil" and "->$callback[0]" to "->{callback[0]}"

## [1.0.42] - 2017-04-03

### Added
- limit headline, subheadline and teaser input length by archive restrictions

## [1.0.41] - 2017-03-14

### Changed
- ModuleNewsArchive, if `news_jumpToCurrent` is set to `show_current` and no news available for current period, jump to period based on latest news date
- ModuleNewsMenuPlus, if `news_jumpToCurrent` is set to `show_current` and no news available for current period, jump to period based on latest news date

## [1.0.40] - 2016-12-16

### Fixed
- syntax error within `/templates/block/block_modal_news.html5`, pdf print was empty

## [1.0.39] - 2016-12-05

### Fixed
- history url/title reworked
