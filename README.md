# Biblionumber Support for Omeka S

This module is useful to keep track of biblionumber (issued from Koha) and find
resources by biblionumber.

## Requirements

* Omeka S >= 3.0.0
* [Koha custom vocabulary](https://git.biblibre.com/omeka-s/custom-vocabularies#koha)

## Features

* Adds a route `/s/<site-slug>/get-biblio/<biblionumber>` that redirects to the corresponding item.
* Adds a `biblionumber` parameter to the search API for item and media
  resources. For items, it's only a shortcut for searching in
  `koha:biblionumber` property. For media, it searches in their items'
  `koha:biblionumber` property.
* Adds a search form field to use this parameter from the admin UI

## License

Copyright BibLibre, 2021-2025

Biblionumber Support is distributed under the GNU General Public License, version 3 (GPLv3).
The full text of this license is given in the `LICENSE` file.
