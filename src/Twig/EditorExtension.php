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
     *
     * @param string $name
     */
    public function shortenEditorName(string $name): string
    {
        if (preg_match('/^iD/', $name) === 1) {
            $shortName = 'iD';
        } elseif (preg_match('/^(reverter;)?JOSM/', $name) === 1) {
            $shortName = 'JOSM';
        } elseif (preg_match('/^ArcGIS/', $name) === 1) {
            $shortName = 'ArcGIS Editor for OSM';
        } elseif (preg_match('/^Citypedia/', $name) === 1) {
            $shortName = 'Citypedia';
        } elseif (preg_match('/^Data4All/', $name) === 1) {
            $shortName = 'Data4All';
        } elseif (preg_match('/^Dutch/', $name) === 1) {
            $shortName = 'Dutch';
        } elseif (preg_match('/^FireYak/', $name) === 1) {
            $shortName = 'FireYak';
        } elseif (preg_match('/^gnome-maps/', $name) === 1) {
            $shortName = 'Gnome-Maps';
        } elseif (preg_match('/^Go Map/', $name) === 1) {
            $shortName = 'GoMap!!';
        } elseif (preg_match('/^IsraelHiking/', $name) === 1) {
            $shortName = 'IsraelHiking';
        } elseif (preg_match('/^Level0/', $name) === 1) {
            $shortName = 'Level0';
        } elseif (preg_match('/^MapComplete/', $name) === 1) {
            $shortName = 'MapComplete';
        } elseif (preg_match('/^MAPS.ME/', $name) === 1) {
            $shortName = 'MAPS.ME';
        } elseif (preg_match('/^Merkaartor/', $name) === 1) {
            $shortName = 'Merkaartor';
        } elseif (preg_match('/^(OMaps|Organic Maps)/', $name) === 1) {
            $shortName = 'Organic Maps';
        } elseif (preg_match('/^OsmAnd/', $name) === 1) {
            $shortName = 'OsmAnd';
        } elseif (preg_match('/^OsmHydrant/', $name) === 1) {
            $shortName = 'OsmHydrant';
        } elseif (preg_match('/^Osmose/', $name) === 1) {
            $shortName = 'Osmose_Editor';
        } elseif (preg_match('/^POI/', $name) === 1) {
            $shortName = 'POI+';
        } elseif (preg_match('/^Potlatch/', $name) === 1) {
            $shortName = 'Potlatch';
        } elseif (preg_match('/^Pushpin/', $name) === 1) {
            $shortName = 'Pushpin';
        } elseif (preg_match('/^PythonOsmApi/', $name) === 1) {
            $shortName = 'PythonOsmApi';
        } elseif (preg_match('/^rosemary/', $name) === 1) {
            $shortName = 'Rosemary';
        } elseif (preg_match('/^Route4u/', $name) === 1) {
            $shortName = 'Route4u';
        } elseif (preg_match('/^SC_extra/', $name) === 1) {
            $shortName = 'SC_extra';
        } elseif (preg_match('/^Services_OpenStreetMap/', $name) === 1) {
            $shortName = 'Services_OpenStreetMap';
        } elseif (preg_match('/^StreetComplete/', $name) === 1) {
            $shortName = 'StreetComplete';
        } elseif (preg_match('/^Vespucci/', $name) === 1) {
            $shortName = 'Vespucci';
        }

        return isset($shortName) ?
            sprintf('<span class="cursor-help" title="%s">%s</span>', $name, $shortName) :
            $name;
    }
}
