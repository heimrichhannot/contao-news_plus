# Changelog
All notable changes to this project will be documented in this file.

## [1.4.1] - 2024-12-11
- Fixed: php8 warning

## [1.4.0] - 2024-03-14
- Changed: require contao 4.13
- Changed: allow php 8
- Removed: tiny mce template

## [1.3.0] - 2022-01-12
- Changed: removed contao 3 support
- Changed: migrated to codefog/contao-news_categories v3
- Changed: migrated to heimrichhannot/contao-be_explanation-bundle
- Fixed: missing tags dependency
- Fixed: some deprecation warning

## [1.2.4] - 2021-03-08
- throw a 404 if the event couldn't be found

## [1.2.3] - 2021-02-03
- fixed dependencies in Backend\News class

## [1.2.2] - 2020-07-14
- fixed dependencies in NewsPlusModel class and News class

## [1.2.1] - 2020-04-22
- fixed different news urls used in some cases in ModuleNewsPlus

## [1.2.0] - 2020-04-20
- added be_tinyMCE template for contao 4 support
- fixed contao 4 tinyMCE support

## [1.1.9] - 2020-02-06
- restored fix from 1.1.6

## [1.1.7] - 2020-01-24
- model issues

## [1.1.6] - 2019-12-11
- fixed an warning in ModuleNewsPlus

## [1.1.5] - 2019-01-24

### Fixed
- bootstrap 3.4 compatibility for dropdowns,  `data-target` must contain `dropdown` not #

## [1.1.4] - 2018-05-23

### Fixed
- always use `maxlength::xxx` rgxp to limit maxlength, because contao core count special characters as encoded entities

## [1.1.3] - 2017-11-30

### Fixed
- fixed year issue

## [1.1.2] - 2017-10-06

### Removed
- check for frontendedit in member news list

## [1.1.1] - 2017-08-07

### Added
- insert tags respecting the overriding of a news archive's jump to by category
- fixed search index for the case of overriding of a news archive's jump to by category

## [1.1.0] - 2017-08-07

### Added
- support for category based jump to urls -> a primary category has to be set in the concrete news

## [1.0.50] - 2017-07-31

### Added
- support for showing tags in news list
- support for differing jump to page in news list
- ModuleMemberNewsList

## [1.0.49] - 2017-07-18

### Changed
- Renamed `NewsPlusModel::findPublishedByPids` to `NewsPlusModel::findPublishedByPidsAndCategories`
- Renamed `NewsPlusModel::countPublishedByPids` to `NewsPlusModel::countPublishedByPidsAndCategories`

## [1.0.48] - 2017-05-08

### Added
- do not render modal when share-print template is set within module config

## [1.0.47] - 2017-04-13

### Changed

- fixed maxlength limit for teaser rte input

## [1.0.46] - 2017-04-13

### Changed

- fixed maxlength limit for html input length by archive restrictions

## [1.0.45] - 2017-04-13

### Changed

- removed .idea folder

## [1.0.44] - 2017-04-12
- created new tag

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
