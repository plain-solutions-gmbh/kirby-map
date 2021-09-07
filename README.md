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

![Geolocation-Field](https://github.com/youngcut/kirby-map/raw/6c95c30865bfde58e2fcb9c9b22c556b6047e90c/lib/img/geolocation_screenshot.png)

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

![Map-Block](https://github.com/youngcut/kirby-map/raw/6c95c30865bfde58e2fcb9c9b22c556b6047e90c/lib/img/maps_screenshot.png)

With the Map-Block, you can setup your Map with a live preview. Here's how you can add this Block to your Blueprint:

```yaml

    fields:
        text:
            type: blocks
            fieldsets:
                - maps

```
To customize the default block, copy following files...

 `/site/plugins/kirby-map/blueprints/blocks/maps.yml`
  `/site/plugins/kirby-map/blueprints/blocks/marker.yml` 
  
 ...to your sitefolder: `/site/blueprints/blocks`

### Markers

In your Map-Block, you can add as many markers you want. Try to place your Marker in the visible preview.

![Map-Block-Marker](https://github.com/youngcut/kirby-map/raw/6c95c30865bfde58e2fcb9c9b22c556b6047e90c/lib/img/marker_screenshot.png)

**How to use it in your Template?**

By default the Block outputs an Open Source Mapbox-Instance.
Use following example, if you want to use the [Google Maps JavaScript API](https://developers.google.com/maps/documentation/javascript/overview):

*site/snippets/blocks/maps.php*:
```PHP
<div id='map_<?= $block->id() ?>' class="block" style='width:100%; height:100%; min-height:400px;'></div>
    
    <?php $center = $block->center()->toLocation(); ?>
    
    <script>
    
	    let map;
	    function  initMap() {
	        map =  new google.maps.Map(document.getElementById("map_<?= $block->id() ?>"), {
			    center: { lat: <?= $center->lat() ?>, lng: <?= $center->lng() ?>},
			    zoom: <?= ($block->zoom()->isNotEmpty()) ? $block->zoom() :  12  ?>,
		    });
	    
		    <?php  foreach ($block->marker()->toBlocks() as $marker) : 
				$coors = $marker->coors()->toLocation()
			?>
	    
			    marker<?= $marker->indexOf() ?> = new google.maps.Marker({
				    position: { lat: <?= $coors->lat() ?>, lng: <?= $coors->lng() ?> },
				    map,
				    title: '<?= $coors->name() ?>',
		    
				    <?php  if ($image = $marker->image()->toFile()) :
					    $width = $image->width() /  100  * $marker->size()->int();
					    $height = $image->height() /  100  * $marker->size()->int();
					    switch ($marker->anchor()) {
						    case "center":  $anchor = [$width /  2, $height /  2]; break;
						    case "top": $anchor = [$width /  2, 0]; break;
						    case "left": $anchor = [0, $height /  2]; break;
						    case "right": $anchor = [$width, $height /  2]; break;
					        case  "top-left": $anchor = [0, 0]; break;
						    case  "top-right": $anchor = [$width, 0]; break;
						    case  "bottom-left": $anchor = [0, $height]; break;
						    case  "bottom-right": $anchor = [$width, $height]; break;
						    default: $anchor = [$width /  2, $height];
					    }
					?>
					
					    icon: {
						    url: "<?= $image->url() ?>",
						    scaledSize: new google.maps.Size(<?= $width ?>, <?= $height ?>),
						    anchor: new google.maps.Point(<?= $anchor[0] ?>, <?= $anchor[1] ?>)
					    }
				    
				    <?php  endif  ?>
			    });
			    
			    <?php  if ($marker->popup()->isNotEmpty()) : ?>
				    
				    const infowindow<?= $marker->indexOf() ?> = new google.maps.InfoWindow({
					    content: `<?= $marker->popup()->kt() ?>`,
				    });
				    
				    marker<?= $marker->indexOf() ?>.addListener("click", () => {
					    infowindow<?= $marker->indexOf() ?>.open({
						    anchor: marker<?= $marker->indexOf() ?>,
					        map,
						    shouldFocus: false,
					    });
					});
		    
			    <?php  endif  ?>
		    <?php  endforeach  ?>
	    }
    
	</script>
    <script  script  src="https://maps.googleapis.com/maps/api/js?key=[YOUR-API]&callback=initMap&libraries=&v=weekly"  async></script>

```

Don't forget to replace [YOUR-API] with your [Google-API](https://developers.google.com/maps/documentation/javascript/get-api-key) on the last line.

## Credits

Powered by [Mapbox](https://www.mapbox.com/)
Inspired by [Sylvain's Kirby-Locator](https://github.com/sylvainjule/kirby-locator)

## Licence
MIT

> [Buy me a](https://www.paypal.com/donate?hosted_button_id=LBCLZVHS4K2R6) ☕️
