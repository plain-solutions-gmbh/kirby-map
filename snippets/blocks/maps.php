
<?php
 $center = $block->center()->toLocation(); 
 $map_id = 'map' . Kirby\Toolkit\Str::slug($block->id(), '_');
?>

<div id="embedded-map-<?= $map_id ?>" class="block" style="width: 100%; height: 100%; min-height: 400px; text-align:center"><p style="position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);">Unable to load map!</p></div>

<script src="https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.js"></script>
<link href="https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.css" rel="stylesheet">

<script>
mapboxgl.accessToken = "<?= option('plain.map.token') ?>";
const <?= $map_id ?> = new mapboxgl.Map({
  container: "embedded-map-<?= $map_id ?>",
  style: "mapbox://styles/mapbox/<?= $block->style() ?>",
  center: [<?= str_replace(',', '.', $center->lng()) ?>, <?= str_replace(',', '.', $center->lat() ) ?>],
  zoom: <?= $block->zoom()->or(12) ?>
});

<?php if (!$block->enable_zoom()->isTrue()) : ?>
  <?= $map_id ?>.scrollZoom.disable()
<?php endif ?>

<?php foreach ($block->marker()->toBlocks() as $marker):
  // Fix renamed field `coors` -> `coordinates` in d77cfe05697c02075cf9f59a999dc2696c2f9cf6
  $coordinates = $marker->coordinates()->or($marker->coors())->toLocation();

  ?>
  <?php $markerElement = 'elMarker' . substr($marker->id(), 0, 8) ?>
  let <?= $markerElement ?> = null;

  <?php if ($image = $marker->image()->toFile()): ?>
    <?= $markerElement ?> = document.createElement("div");
    <?= $markerElement ?>.className = "marker";
    <?= $markerElement ?>.style.backgroundImage = "url(<?= $image->url() ?>)";
    <?= $markerElement ?>.style.width = "<?= number_format(($image->width() / 100) * $marker->size()->int()) ?>px";
    <?= $markerElement ?>.style.height = "<?= number_format(($image->height() / 100) * $marker->size()->int()) ?>px";
    <?= $markerElement ?>.style.backgroundSize = "100%";
  <?php endif ?>


  <?php $markerObj = 'objMarker' . substr($marker->id(), 0, 8) ?>

  let <?= $markerObj ?> = new mapboxgl.Marker({
    anchor: "<?= $marker->anchor() ?>",
    element: <?= $markerElement ?>
  })
  <?php if($marker->uid()->isNotEmpty()): ?>
    <?= $markerObj ?>.getElement().id = "<?= $marker->uid()->value(); ?>" 
  <?php endif ?>
    <?= $markerObj ?>.setLngLat([<?= str_replace(',', '.', $coordinates->lng()) ?>, <?= str_replace(',', '.', $coordinates->lat()) ?>])
    <?= $markerObj ?>.addTo(<?= $map_id ?>)
  <?php if ($marker->hasPopup()->isTrue()): ?>
    <?= $markerObj ?>.setPopup(new mapboxgl.Popup({
      offset: <?= $marker->popupOffset() ?>
    })
    .setHTML('<?= $marker->popup() ?>'));
  <?php endif ?>
<?php endforeach ?>
</script>
