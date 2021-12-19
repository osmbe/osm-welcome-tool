# Contributing to OpenStreetMap Welcome Tool

## Translation

**Your want to translate the tool in your own language ?**

All translations are stored here: <https://github.com/osmbe/osm-welcome-tool/tree/2.x/translations>

To add a new language, copy the [`messages+intl-icu.en.xlf` file](https://github.com/osmbe/osm-welcome-tool/blob/2.x/translations/messages%2Bintl-icu.en.xlf) and update all the `target` properties to the correct translation in your language.

You don't need to provide translations of `security.en.xlf` and `validators.en.xlf` as these are part of the Symfony framework.

Once done, save your file as `messages+intl-icu.##.xlf` by replacing `##` by the [ISO 639-1 language code](https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes) of your language.

Upload your new file to our [`translations/`](https://github.com/osmbe/osm-welcome-tool/tree/2.x/translations) folder and create a Pull Request, we'll take it from there!

---

## Add a new region

**You want to use the Welcome Tool in your region and the region is not available yet ?**

A region can be **any** geographical region defined by a geometry. It can be a whole country, a province, a city, a neighborhood, ...

There are only 2 steps to add a new region to the tool:

- Add your region to the [`config/regions.yaml`](https://github.com/osmbe/osm-welcome-tool/blob/2.x/config/regions.yaml) file:
  - Create a new key (lowerspace, not special characters, in English) ;
  - Add the `name` property with the name (in English) of your region ;
  - Add the `flag` property with the flag emoji of the country your region is in ;
  - You don't have to worry about the `osmcha.id` id, we'll take care of that ;
- Add a GeoJSON for your region in the [`assets/regions`](https://github.com/osmbe/osm-welcome-tool/tree/2.x/assets/regions) folder.  
You can use <http://polygons.openstreetmap.fr/> to easily create a (simplified) GeoJSON file for a specific OSM relation.

Create a Pull Request with those 2 steps and we'll take it from there!

---

## Add or update message template(s) for your region

**You want to add or update a message template for your region ?**

All messages templates are [Markdown](https://daringfireball.net/projects/markdown/) files stored in the [`templates/messages`](https://github.com/osmbe/osm-welcome-tool/tree/2.x/templates/messages) folder.

The messages templates Markdown files are classified under

- a region folder (key of the region in [`regions.yaml`](https://github.com/osmbe/osm-welcome-tool/blob/2.x/config/regions.yaml) file)
- a language folder ([ISO 639-1 language code](https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes))

The filename can be anything you want but should be a "slug" version of the template `name` property (see below): all lowercase, separated with dashes, no special characters.

Each message template file has a YAML front matter containing the following properties:

- `name`: name of the template (that's what will be displayed in the templates list) ;
- `title`: title of the message that will be sent to the user ;

After the YAML front matter, you can put any text using Markdown formatting. You can also use placeholders (for example, `{{ mapper.displayName }}` will be replaced by the username of the mapper).

Create a Pull Request with the new (or updated files)!
