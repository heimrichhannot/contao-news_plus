<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package news_plus
 * @author Mathias Arzberger <develop@pdir.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

/**
 * Front end modules
 */
$GLOBALS['TL_LANG']['FMD']['newslist_plus'] = array('Nachrichtenliste Plus', 'Listet alle Nachrichten eines bestimmten Zeitraums auf, mit erweiterten Konfigurationsmöglichkeiten.');
$GLOBALS['TL_LANG']['FMD']['newsreader_plus'] = array('Nachrichtenleser Plus', 'Stellt eine einzelne Nachricht dar mit erweiterten Konfigurationsmöglichkeiten.');
$GLOBALS['TL_LANG']['FMD']['newsfilter'] = array('Nachrichtenfilter', 'Zeigt einen Filter an, der die Ausgabe der erweiterten Nachrichtenliste manipuliert.');
$GLOBALS['TL_LANG']['FMD']['newsmenu_plus'] = array('Nachrichtenarchiv-Menü Plus', 'Erzeugt ein erweitertes Nachrichtenarchiv-Menü.');

/**
 * Legends
 */

$GLOBALS['TL_LANG']['tl_module']['filter_legend'] = 'Filtereinstellungen';
$GLOBALS['TL_LANG']['tl_module']['navigation_legend'] = 'Navigation-Einstellungen';

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_module']['news_filterTemplate'] = array('Filtertemplate', 'Legen Sie das Template des Nachrichtenfilters fest.');
$GLOBALS['TL_LANG']['tl_module']['news_filterCategoryTemplate'] = array('Kategorietemplate', 'Legen Sie das Template des Kategoriefilters fest.');
$GLOBALS['TL_LANG']['tl_module']['news_filterShowSearch'] = array('Suche anzeigen', 'Soll die Suchfunktion im Filter aktiviert werden?');
$GLOBALS['TL_LANG']['tl_module']['news_filterShowCategories'] = array('Nach Kategorien filtern', 'Soll das Filtern nach Kategorien möglich sein?');
$GLOBALS['TL_LANG']['tl_module']['news_filterUseSearchIndex'] = array('Suchindex verwenden', 'Die Suche verwendet den Suchindex oder die Tabelle tl_news');
$GLOBALS['TL_LANG']['tl_module']['news_filterFuzzySearch'] = array('Fuzzy-Suche verwenden', 'Schließe ähnliche Wörter in die Suche mit ein.');
$GLOBALS['TL_LANG']['tl_module']['news_filterSearchQueryType'] = array('Finde alle Wörter', 'Finde alle Wörter des Suchstrings.');
$GLOBALS['TL_LANG']['tl_module']['news_showInModal'] = array('Nachrichtendetails im Modalfenster anzeigen', '<b>Achtung:</b> Der Nachrichtenleser muß entsprechend angegeben werden.');
$GLOBALS['TL_LANG']['tl_module']['news_pdfJumpTo'] = array('Seite für den PDF-Druck', 'Wählen Sie hier die Seite, die zur Ausgabe der PRINT-TO-PDF-Funktion verwendet werden soll.');
$GLOBALS['TL_LANG']['tl_module']['news_filterNewsCategoryArchives'] = array('Nachrichtenarchive mit Kategorien', 'Bitte wählen Sie ein oder mehrere Nachrichtenarchive aus für die Kategorien im Filter angezeigt werden sollen.');
$GLOBALS['TL_LANG']['tl_module']['news_filterModule'] = array('Nachrichtenfilter', 'Bitte wählen Sie einen Nachrichtenfilter um die Filtereingrenzungen für die Liste bereitzustellen.');
$GLOBALS['TL_LANG']['tl_module']['news_archiveTitleAppendCategories'] = array('Kategorien-Titel an Archiv-Titel anhängen', 'Fügt die Kategorien getrennt an den Archiv-Titel an.');
$GLOBALS['TL_LANG']['tl_module']['news_archiveTitleCategories'] = array('Anzuhängende Kategorien', 'Bitte wählen Sie die Kategorien aus, die an den Titel Ihrer Nachrichtenarchive angehangen werden sollen.');
$GLOBALS['TL_LANG']['tl_module']['news_filterDefaultExclude'] = array('Standard-Filter (ausschließen)', 'Hier kann der Standard-Filter für die Nachrichtenliste zum Ausschließen von Beiträgen mit gewählten Kategorien erstellt werden.');
$GLOBALS['TL_LANG']['tl_module']['news_addNavigation'] = array('Nachrichtennavigation aktivieren', 'Eine einfache Navigation zur nächsten und vorherigen Nachricht einbinden.');
$GLOBALS['TL_LANG']['tl_module']['news_navigation_template'] = array('Nachrichtennavigation Template', 'Hier können Sie das Nachrichtennavigation Template auswählen.');
$GLOBALS['TL_LANG']['tl_module']['news_navigation_infinite'] = array('Endlosnavigation', 'Endloses klicken durch die Nachrichtennavigation aktivieren.');
