<div id='map_<?= $block->id() ?>' class="block" style='width:100%; height:100%; min-height:400px;'></div>

<?php $center = $block->center()->toLocation(); ?>

<script>
    let map;

    function initMap() {
        map = new google.maps.Map(document.getElementById("map_<?= $block->id() ?>"), {
            center: {
                lat: <?= $center->lat() ?>,
                lng: <?= $center->lng() ?>
            },
            zoom: <?= ($block->zoom()->isNotEmpty()) ? $block->zoom() :  12  ?>,
        });

        <?php foreach ($block->marker()->toBlocks() as $marker) :
            $coors = $marker->coors()->toLocation()
        ?>

            marker<?= $marker->indexOf() ?> = new google.maps.Marker({
                position: {
                    lat: <?= $coors->lat() ?>,
                    lng: <?= $coors->lng() ?>
                },
                map,
                title: '<?= $coors->name() ?>',

                <?php if ($image = $marker->image()->toFile()) :
                    $width = $image->width() /  100  * $marker->size()->int();
                    $height = $image->height() /  100  * $marker->size()->int();
                    switch ($marker->anchor()) {
                        case "center":
                            $anchor = [$width /  2, $height /  2];
                            break;
                        case "top":
                            $anchor = [$width /  2, 0];
                            break;
                        case "left":
                            $anchor = [0, $height /  2];
                            break;
                        case "right":
                            $anchor = [$width, $height /  2];
                            break;
                        case  "top-left":
                            $anchor = [0, 0];
                            break;
                        case  "top-right":
                            $anchor = [$width, 0];
                            break;
                        case  "bottom-left":
                            $anchor = [0, $height];
                            break;
                        case  "bottom-right":
                            $anchor = [$width, $height];
                            break;
                        default:
                            $anchor = [$width /  2, $height];
                    }
                ?>

                    icon: {
                        url: "<?= $image->url() ?>",
                        scaledSize: new google.maps.Size(<?= $width ?>, <?= $height ?>),
                        anchor: new google.maps.Point(<?= $anchor[0] ?>, <?= $anchor[1] ?>)
                    }

                <?php endif  ?>
            });

            <?php if ($marker->popup()->isNotEmpty()) : ?>

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

            <?php endif  ?>
        <?php endforeach  ?>
    }
</script>
<script script src="https://maps.googleapis.com/maps/api/js?key=[YOUR-API]&callback=initMap&libraries=&v=weekly" async></script>