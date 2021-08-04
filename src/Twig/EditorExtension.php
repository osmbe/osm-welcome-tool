<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class EditorExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('editor', [$this, 'shortenEditorName'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Function based on https://github.com/cliffordsnow/sql/blob/master/editors.sql
     *
     * @param string $name
     */
    public function shortenEditorName(string $name): string
    {
             if (preg_match('/^iD/', $name) === 1) { $shortName = 'iD'; }
        else if (preg_match('/^(reverter;)?JOSM/', $name) === 1) { $shortName = 'JOSM'; }
        else if (preg_match('/^ArcGIS/', $name) === 1) { $shortName = 'ArcGIS Editor for OSM'; }
        else if (preg_match('/^Citypedia/', $name) === 1) { $shortName = 'Citypedia'; }
        else if (preg_match('/^Data4All/', $name) === 1) { $shortName = 'Data4All'; }
        else if (preg_match('/^Dutch/', $name) === 1) { $shortName = 'Dutch'; }
        else if (preg_match('/^FireYak/', $name) === 1) { $shortName = 'FireYak'; }
        else if (preg_match('/^gnome-maps/', $name) === 1) { $shortName = 'Gnome-Maps'; }
        else if (preg_match('/^Go Map/', $name) === 1) { $shortName = 'GoMap!!'; }
        else if (preg_match('/^IsraelHiking/', $name) === 1) { $shortName = 'IsraelHiking'; }
        else if (preg_match('/^Level0/', $name) === 1) { $shortName = 'Level0'; }
        else if (preg_match('/^MapComplete/', $name) === 1) { $shortName = 'MapComplete'; }
        else if (preg_match('/^MAPS.ME/', $name) === 1) { $shortName = 'MAPS.ME'; }
        else if (preg_match('/^Merkaartor/', $name) === 1) { $shortName = 'Merkaartor'; }
        else if (preg_match('/^(OMaps|Organic Maps)/', $name) === 1) { $shortName = 'Organic Maps'; }
        else if (preg_match('/^OsmAnd/', $name) === 1) { $shortName = 'OsmAnd'; }
        else if (preg_match('/^OsmHydrant/', $name) === 1) { $shortName = 'OsmHydrant'; }
        else if (preg_match('/^Osmose/', $name) === 1) { $shortName = 'Osmose_Editor'; }
        else if (preg_match('/^POI/', $name) === 1) { $shortName = 'POI+'; }
        else if (preg_match('/^Potlatch/', $name) === 1) { $shortName = 'Potlatch'; }
        else if (preg_match('/^Pushpin/', $name) === 1) { $shortName = 'Pushpin'; }
        else if (preg_match('/^PythonOsmApi/', $name) === 1) { $shortName = 'PythonOsmApi'; }
        else if (preg_match('/^rosemary/', $name) === 1) { $shortName = 'Rosemary'; }
        else if (preg_match('/^Route4u/', $name) === 1) { $shortName = 'Route4u'; }
        else if (preg_match('/^SC_extra/', $name) === 1) { $shortName = 'SC_extra'; }
        else if (preg_match('/^Services_OpenStreetMap/', $name) === 1) { $shortName = 'Services_OpenStreetMap'; }
        else if (preg_match('/^StreetComplete/', $name) === 1) { $shortName = 'StreetComplete'; }
        else if (preg_match('/^Vespucci/', $name) === 1) { $shortName = 'Vespucci'; }

        return isset($shortName) ?
            sprintf('<span class="cursor-help" title="%s">%s</span>', $name, $shortName) :
            $name;
    }
}
