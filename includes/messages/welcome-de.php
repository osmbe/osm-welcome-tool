<?php
/* This file is part of osm-welcome: a platform to coordinate welcoming of OpenStreetMap mappers
 * Copyright © 2018  Midgard and osm-welcome contributors
 *
 * This program is free software: you can redistribute it and/or modify it under the terms of the
 * GNU Affero General Public License as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License along with this
 * program. If not, see <https://www.gnu.org/licenses/>.
 */

$language = 'de';
$welcome[$language] = array();


$welcome[$language]['language_name'] = 'Deutsch';

$welcome[$language]['hi'] = 'Hallo %1$s';

$welcome[$language]['bravo'] = 'Ich habe gesehen, dass Sie Ihren ersten Edit auf OpenStreetMap gemacht haben. Herzlichen Glückwunsch und Danke! Sie sind jetzt Mitglied der OSM-Gemeinschaft.';

$welcome[$language]['reality'] = 'Wir existieren nicht nur '.markdown_link('online', 'http://osm.be/').', sondern auch '.markdown_link('im wirklichen Leben', 'https://www.meetup.com/OpenStreetMap-Belgium/').'.';

$welcome[$language]['questions'] = 'Wenn Sie Fragen haben, können wir Ihnen helfen. Wenn Sie denken, dass Sie etwas kaputt gemacht haben, nicht sicher sind, wie man etwas kartografieren soll, oder einfach nur mehr über OpenStreetMap wissen wollen, können Sie und kontaktieren.';

$welcome[$language]['helpintro'] = 'Hier ist etwas Hilfe, um Ihnen den Einstieg zu erleichtern:';

$welcome[$language]['info_wiki'] = 'Sie finden viele Informationen zum Kartografieren auf '.markdown_link('der Wiki', 'https://wiki.openstreetmap.org/wiki/').'.';
    
$welcome[$language]['info_iD'] = 'Wenn Sie etwas mit iD kartografieren möchten und es nicht finden können, ist die schnellste Lösung vielleicht'; // Followed by info_solution
$welcome[$language]['info_Potlatch'] = 'Wenn Sie etwas mit Potlatch kartografieren möchten und es nicht finden können, ist die schnellste Lösung vielleicht'; // Followed by info_solution
$welcome[$language]['info_JOSM'] = 'Wenn Sie etwas mit JOSM kartografieren möchten, können die Presets hilfreich sein. Es ist empfehlenswert, die Wiki-Seite des Tags zu besuchen, damit Sie sehen können, wie es verwendet werden soll. In der Preset-Oberfläche gibt es dazu Hyperlinks. Der schnellste Weg, etwas anderes im Wiki zu finden, ist vielleicht'; // Followed by info_solution
$welcome[$language]['info_MAPSME'] = 'In Maps.me ist die Bearbeitungsmöglichkeit sehr eingeschränkt. Gehen Sie auf '.markdown_link('openstreetmap.org', 'https://www.openstreetmap.org').' und klicken Sie auf Bearbeiten, um volle Bearbeitungsmöglichkeiten zu haben. Wenn Sie wissen möchten, wie man etwas taggen soll, ist die schnellste Lösung vielleicht'; // Followed by info_solution
$welcome[$language]['info_other'] = 'Wenn Sie wissen möchten, wie man etwas taggen soll, ist die schnellste Lösung vielleicht'; // Followed by info_solution

$welcome[$language]['info_solution'] = 'die Suche nach dem “site:wiki.openstreetmap.org [Suchbegriff]” mit Ihrer Lieblingssuchmaschine. Sie können auch im Fragenkatalog '.markdown_link('der Hilfeseite', 'https://help.openstreetmap.org').' nachschlagen, selbst eine Frage stellen, oder einen neuen Beitrag '.markdown_link('im Forum', 'https://forum.openstreetmap.org/viewforum.php?id=29').' erstellen.';

$welcome[$language]['news'] = 'Wenn Sie Neuigkeiten aus der belgischen OSM-Community erhalten möchten, dann registrieren Sie sich auf der '.markdown_link('belgischen Email-Verteilerliste', 'https://lists.openstreetmap.org/listinfo/talk-be').', registrieren Sie sich für die '.markdown_link('belgische Newsletter', 'http://osm.us13.list-manage.com/subscribe?u=cc6632a49e784f67574e50269&id=5c2416bba6').' oder chatten Sie mit uns auf '.markdown_link('Riot', 'https://riot.im/app/#/group/+osmbe:matrix.org').'. Sie können auch '.markdown_link('@osm_be', 'https://twitter.com/osm_be').' auf Twitter folgen oder ein Mitglied der '.markdown_link('Meetup Gruppe', 'https://www.meetup.com/OpenStreetMap-Belgium/').' oder '.markdown_link('Facebook Gruppe', 'https://www.facebook.com/groups/1419016881706058/').' werden.';

$welcome[$language]['resultmaps'] = 'Um aktive Kartografierer in Ihrer Nähe zu sehen oder um herauszufinden, wie viel Sie beigetragen haben, schauen Sie sich die Karten und Statistiken unter '.markdown_link('resultmaps.neis-one.org', 'http://resultmaps.neis-one.org/').' an.';

$welcome[$language]['weeklyosm'] = 'Es gibt auch einen '.markdown_link('wöchentlichen, globalen Newsletter über die OSM-Welt', 'http://www.weeklyosm.eu/').' den Sie abonnieren können. (Es hat einen RSS-Feed für Fans.)';

$welcome[$language]['endingsentence'] = 'Happy Mapping!';

$welcome[$language]['osm-be'] = 'OpenStreetMap Belgien';

$welcome[$language]['multiple_langs'] = 'Wir haben diese Nachricht in mehreren Sprachen gesendet, da wir Ihre Sprache nicht erkennen konnten.';
