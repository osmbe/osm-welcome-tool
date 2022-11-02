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
     * Function based on https://github.com/cliffordsnow/sql/blob/master/editors.sql.
     */
    public function shortenEditorName(?string $name): string
    {
        if (null === $name) {
            return '';
        }

        if (1 === preg_match('/^iD/', $name)) {
            $shortName = 'iD';
        } elseif (1 === preg_match('/^(reverter;)?JOSM/', $name)) {
            $shortName = 'JOSM';
        } elseif (1 === preg_match('/^ArcGIS/', $name)) {
            $shortName = 'ArcGIS Editor for OSM';
        } elseif (1 === preg_match('/^Citypedia/', $name)) {
            $shortName = 'Citypedia';
        } elseif (1 === preg_match('/^Data4All/', $name)) {
            $shortName = 'Data4All';
        } elseif (1 === preg_match('/^Dutch/', $name)) {
            $shortName = 'Dutch';
        } elseif (1 === preg_match('/^FireYak/', $name)) {
            $shortName = 'FireYak';
        } elseif (1 === preg_match('/^gnome-maps/', $name)) {
            $shortName = 'Gnome-Maps';
        } elseif (1 === preg_match('/^Go Map/', $name)) {
            $shortName = 'GoMap!!';
        } elseif (1 === preg_match('/^IsraelHiking/', $name)) {
            $shortName = 'IsraelHiking';
        } elseif (1 === preg_match('/^Level0/', $name)) {
            $shortName = 'Level0';
        } elseif (1 === preg_match('/^MapComplete/', $name)) {
            $shortName = 'MapComplete';
        } elseif (1 === preg_match('/^MAPS.ME/', $name)) {
            $shortName = 'MAPS.ME';
        } elseif (1 === preg_match('/^Merkaartor/', $name)) {
            $shortName = 'Merkaartor';
        } elseif (1 === preg_match('/^(OMaps|Organic Maps)/', $name)) {
            $shortName = 'Organic Maps';
        } elseif (1 === preg_match('/^OsmAnd/', $name)) {
            $shortName = 'OsmAnd';
        } elseif (1 === preg_match('/^OsmHydrant/', $name)) {
            $shortName = 'OsmHydrant';
        } elseif (1 === preg_match('/^Osmose/', $name)) {
            $shortName = 'Osmose_Editor';
        } elseif (1 === preg_match('/^POI/', $name)) {
            $shortName = 'POI+';
        } elseif (1 === preg_match('/^Potlatch/', $name)) {
            $shortName = 'Potlatch';
        } elseif (1 === preg_match('/^Pushpin/', $name)) {
            $shortName = 'Pushpin';
        } elseif (1 === preg_match('/^PythonOsmApi/', $name)) {
            $shortName = 'PythonOsmApi';
        } elseif (1 === preg_match('/^rosemary/', $name)) {
            $shortName = 'Rosemary';
        } elseif (1 === preg_match('/^Route4u/', $name)) {
            $shortName = 'Route4u';
        } elseif (1 === preg_match('/^SC_extra/', $name)) {
            $shortName = 'SC_extra';
        } elseif (1 === preg_match('/^Services_OpenStreetMap/', $name)) {
            $shortName = 'Services_OpenStreetMap';
        } elseif (1 === preg_match('/^StreetComplete/', $name)) {
            $shortName = 'StreetComplete';
        } elseif (1 === preg_match('/^Vespucci/', $name)) {
            $shortName = 'Vespucci';
        }

        return isset($shortName) ?
            sprintf('<span class="cursor-help" title="%s">%s</span>', $name, $shortName) :
            $name;
    }
}
