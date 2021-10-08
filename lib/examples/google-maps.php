<div id="embedded-map-<?= $block->id() ?>" class="block" style="width: 100%; height: 100%; min-height: 400px;"></div>

<?php $center = $block->center()->toLocation(); ?>

<script type="module">
let map;

window.kirbyMap.init = function () {
  map = new google.maps.Map(document.getElementById("embedded-map-<?= $block->id() ?>"), {
    center: {
      lat: <?= $center->lat() ?>,
      lng: <?= $center->lng() ?>
    },
    zoom: <?= $block->zoom()->or(12) ?>,
  });

  <?php
  foreach ($block->marker()->toBlocks() as $marker) :
    // Fix renamed field `coors` -> `coordinates` in d77cfe05697c02075cf9f59a999dc2696c2f9cf6
    $coordinates = $marker->coordinates()->or($marker->coors())->toLocation();
  ?>

    const marker<?= $marker->indexOf() ?> = new google.maps.Marker({
      position: {
        lat: <?= $coordinates->lat() ?>,
        lng: <?= $coordinates->lng() ?>,
      },
      map,
      title: "<?= $coordinates->name() ?>",
      <?php if ($image = $marker->image()->toFile()):
        $width = number_format($image->width() / 100 * $marker->size()->int());
        $height = number_format($image->height() / 100 * $marker->size()->int());
        switch ($marker->anchor()->value()) {
          case 'center':
            $anchor = [$width /  2, $height /  2];
            break;
          case 'top':
            $anchor = [$width /  2, 0];
            break;
          case 'left':
            $anchor = [0, $height /  2];
            break;
          case 'right':
            $anchor = [$width, $height /  2];
            break;
          case 'top-left':
            $anchor = [0, 0];
            break;
          case 'top-right':
            $anchor = [$width, 0];
            break;
          case 'bottom-left':
            $anchor = [0, $height];
            break;
          case 'bottom-right':
            $anchor = [$width, $height];
            break;
          default:
            $anchor = [$width /  2, $height];
        }
      ?>
      icon: {
        url: "<?= $image->url() ?>",
        scaledSize: new google.maps.Size(<?= $width ?>, <?= $height ?>),
        anchor: new google.maps.Point(<?= $anchor[0] ?>, <?= $anchor[1] ?>),
      }
      <?php endif  ?>
    });

    <?php if ($marker->hasPopup()->isTrue()) : ?>
      const infoWindow<?= $marker->indexOf() ?> = new google.maps.InfoWindow({
        content: `<?= $marker->popup() ?>`,
      });

      marker<?= $marker->indexOf() ?>.addEventListener("click", () => {
        infowindow<?= $marker->indexOf() ?>.open({
          anchor: marker<?= $marker->indexOf() ?>,
          map,
          shouldFocus: false,
        });
      });

    <?php endif  ?>
  <?php endforeach  ?>
};
</script>
<script script src="https://maps.googleapis.com/maps/api/js?key=[YOUR-API]&callback=kirbyMap.init&libraries=&v=weekly" async></script>
