<div id='map_<?= $block->id() ?>' class="block" style='width: 100%; height: 100%;min-height: 400px;'></div>

<script src="https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.js"></script>
<link href="https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.css" rel="stylesheet">

<?php $center = $block->center()->toLocation(); ?>

<script>
    mapboxgl.accessToken = '<?= $kirby->option('microman.map.token') ?>';
    var map = new mapboxgl.Map({
        container: 'map_<?= $block->id() ?>',
        style: 'mapbox://styles/mapbox/<?= $block->style() ?>',
        center: [<?= $center->lng() ?>, <?= $center->lat() ?>],
        zoom: <?= ($block->zoom()->isNotEmpty()) ? $block->zoom() : 12 ?>
    });

    <?php foreach ($block->marker()->toBlocks() as $marker) : ?>

        <?php $coors = $marker->coors()->toLocation() ?>

        let elMarker = null,
            popupoffset = 30

        <?php if ($image = $marker->image()->toFile()) : ?>

            elMarker = document.createElement('div');
            elMarker.className = 'marker';
            elMarker.style.backgroundImage = "url('<?= $image->url() ?>')";
            elMarker.style.width = "<?= $image->width() / 100 * $marker->size()->int() ?>px";
            elMarker.style.height = "<?= $image->height() / 100 * $marker->size()->int() ?>px";
            elMarker.style.backgroundSize = '100%';

        <?php endif ?>


        let curMarker = new mapboxgl.Marker({
                anchor: '<?= $marker->anchor() ?>',
                element: elMarker
            })
            .setLngLat([<?= $coors->lng() ?>, <?= $coors->lat() ?>])
            .addTo(map)

        <?php if ($marker->popup()->isNotEmpty()) : ?>
                .setPopup(new mapboxgl.Popup({
                        offset: <?= $marker->popupoffset() ?>
                    })
                    .setHTML(`<?= $marker->popup()->kt() ?>`));
        <?php endif ?>

    <?php endforeach ?>
</script>