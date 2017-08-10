# News Plus

A collection of enhancements for the contao news module.

## Features

- Group news archive view by root (page-tree root), must be selected
- Dummy Image by news archive
- Module NewsListPlus
- Module NewsReaderPlus (possibility to show details in modal window, with next/prev event navigation and browser history support)
- Module Newsfilter (filter list by date, archive and search term, use contao search)
- override news archive's jump to in NewsListPlus or in the primary news category assigned to the concrete news

### Newsarchive

- added option values jump to day, year, month of latest published news for news_jumpToCurrent 

### InsertTags

The following inserttags should be used if jump to is changed in the primary news category assigned to the concrete news:

- news_plus
- news_plus_open
- news_plus_url