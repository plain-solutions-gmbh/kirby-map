# Kirby Map

## Overview
1-in-2-Plugin: All you need to set up a Map into your Website. This Plugin has two Feature included:

 - **Geolocation-Field:**
	 - Search for your location and get results (Name, Lat, Long) into fields

 - **Map-Block:**
	 - Build-In Mapbox-Instance
	 - Set the following Values for...
		 - Design
		 - (Center)-Location
		 - Zoom
	 - Insert unlimited numbers of Marker
		 - Location
		 - Icon
		 - Anchor of the Icon (Top-Left, Center-Center, Bottom-Right, etc...)
		 - Size of the Icon (100% = Original-Size)
		 - Popup: Text and Horizontal Offset to the Location

> This plugin is free to use and published under the MIT license. If you use this plugin for commercial purposes and you want to show your appreciation, don't hesitate to [support me with a donation](https://www.paypal.com/donate?hosted_button_id=LBCLZVHS4K2R6).

## Installation
Download and copy this repository to you Plugin-Folder: `/site/plugins/kirby-map`


## Config

Kirby Map uses [Mapbox](https://www.mapbox.com/) for Geolocation and Map-View. For internal purpose, a default token is already set.
To use Open-Map on your Website get your [Access-Token](https://docs.mapbox.com/help/getting-started/access-tokens) and set it in your Config-File: `/site/config/config.php`

    'microman.map.token' => '[your-token]'

## Geolocation-Field

![Geolocation-Field](./.github/screenshot-geolocation.png)

Here's a example how to use the Geolocation-Field on your blueprint:

```yaml

mygeolocation:
  label: My Geolocation
  type: geolocation
  default:
    name: "Berlin"
    Lat: 13.38333
    Lng: 52.51667
```

**How to use it in your Template?**

```php

    <?php $mylocation = $page->mygeolocation()->toLocation() ?>

    Name: <?= $mylocation->name() ?>
    Latitude: <?= $mylocation->lat() ?>
    Longitude: <?= $mylocation->lng() ?>

```

## Map-Block

![Map-Block](./.github/screenshot-maps.png)

With the Map-Block, you can setup your Map with a live preview. Here's how you can add this Block to your Blueprint:

```yaml
mycontent:
    type: blocks
    fieldsets:
        - maps

```
To customize the default block-blueprints, copy the 2 files in `/site/plugins/kirby-map/blueprints/blocks/`
 to your sitefolder: `/site/blueprints/blocks`

### Markers

In your Map-Block, you can add as many markers you want. Try to place your Marker in the visible preview.

![Map-Block-Marker](./.github/screenshot-marker.png)

### How to use it in your Template?

The Map will be rendered with your Block-Field.

By default the Block outputs an Open Source Mapbox-Instance.
If you want to use the [Google Maps JavaScript API](https://developers.google.com/maps/documentation/javascript/overview) copy and rename `/site/plugins/kirby-map/lib/examples/google_maps.php` to `site/snippets/blocks/maps.php`

**Don't forget to replace [YOUR-API] with your [Google-API](https://developers.google.com/maps/documentation/javascript/get-api-key) on the last line.**

## Credits

Powered by [Mapbox](https://www.mapbox.com/). Inspired by [Sylvain's Kirby-Locator](https://github.com/sylvainjule/kirby-locator)

## Licence
MIT

> [Buy me a](https://www.paypal.com/donate?hosted_button_id=LBCLZVHS4K2R6) ☕️
