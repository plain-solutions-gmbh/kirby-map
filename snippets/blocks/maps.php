<div id="embedded-map-<?= $block->id() ?>" class="block" style="width: 100%; height: 100%; min-height: 400px;"></div>

<script src="https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.js"></script>
<link href="https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.css" rel="stylesheet">

<?php $center = $block->center()->toLocation(); ?>

<script>
mapboxgl.accessToken = "<?= option('microman.map.token') ?>";
const map = new mapboxgl.Map({
  container: "embedded-map-<?= $block->id() ?>",
  style: "mapbox://styles/mapbox/<?= $block->style() ?>",
  center: [<?= str_replace(',', '.', $center->lng()) ?>, <?= str_replace(',', '.', $center->lat() ) ?>],
  zoom: <?= $block->zoom()->or(12) ?>
});

<?php foreach ($block->marker()->toBlocks() as $marker):
  // Fix renamed field `coors` -> `coordinates` in d77cfe05697c02075cf9f59a999dc2696c2f9cf6
  $coordinates = $marker->coordinates()->or($marker->coors())->toLocation();

  ?>
  <?php $markerid = 'elMarker' . substr($marker->id(), 0, 8) ?>
  let <?= $markerid ?> = null;

  <?php if ($image = $marker->image()->toFile()): ?>
    <?= $markerid ?> = document.createElement('div');
    <?= $markerid ?>.className = 'marker';
    <?= $markerid ?>.style.backgroundImage = "url(<?= $image->url() ?>)";
    <?= $markerid ?>.style.width = "<?= number_format(($image->width() / 100) * $marker->size()->int()) ?>px";
    <?= $markerid ?>.style.height = "<?= number_format(($image->height() / 100) * $marker->size()->int()) ?>px";
    <?= $markerid ?>.style.backgroundSize = '100%';
  <?php endif ?>

  new mapboxgl.Marker({
    anchor: "<?= $marker->anchor() ?>",
    element: <?= $markerid ?>
  })
    .setLngLat([<?= str_replace(',', '.', $coordinates->lng()) ?>, <?= str_replace(',', '.', $coordinates->lat()) ?>])
    .addTo(map)
  <?php if ($marker->hasPopup()->isTrue()): ?>
    .setPopup(new mapboxgl.Popup({
      offset: <?= $marker->popupOffset() ?>
    })
    .setHTML('<?= $marker->popup() ?>'));
  <?php endif ?>
<?php endforeach ?>
</script>
