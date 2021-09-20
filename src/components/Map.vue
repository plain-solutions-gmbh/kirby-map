<template>

    <div class="map-wrapper">
        <div @update="update" :id="mapId" class="map"></div>
    </div>

</template>

<script>
  export default {
    data() {
        return {
            map: null,
            attachedMarker: [],
            attachedPopup: [],
            options: null
        }
    },
    computed: {
        mapId() {
            return 'map-' + this._uid
        },

        style() {
            return this.content.style;
        },

        zoom() {
            return this.content.zoom;
        },

        marker() {
            return this.content.marker.map(a => {
                let imagesize = (a.content.image.length > 0 ) ? a.content.image[0].info.split(" × ") : [0,0];

                return {
                    
                    image: (a.content.image.length > 0 ) ? a.content.image[0].url : false,
                    width: imagesize[0] / 100 * a.content.size,
                    height: imagesize[1] / 100 * a.content.size,
                    lat: (a.content.coors) ? a.content.coors.lat : 0,
                    lng: (a.content.coors) ? a.content.coors.lng : 51,
                    anchor: a.content.anchor || null,
                    haspopup:a.content.haspopup,
                    popup:a.content.popup || "",
                    popupoffset:a.content.popupoffset || 40

                }
            })
        },

        center() {
            if (this.content.center) {

                // Set default values for new markers
                    let markerNode = this.field('marker').fieldsets.marker.tabs.content.fields.coors;
                    markerNode.defaultName = this.content.center.name;
                    markerNode.defaultLat = this.content.center.lat;
                    markerNode.defaultLng = this.content.center.lng;

                return [this.content.center.lng, this.content.center.lat];
            }
            return [0, 20]
        }
    },

    created() {

        // Get MapBox-Library
            
            let mapboxStyle = document.createElement('link');
            mapboxStyle.href = 'https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.css';
            mapboxStyle.rel = 'stylesheet';
            document.head.appendChild(mapboxStyle);

            let mapboxScript = document.createElement('script');
            mapboxScript.type = "text/javascript";

            mapboxScript.onload = () => {

                // Get Options and Init

                    this.$api.get('map/options').then(res => {
                        this.options = res;
                        this.initMap()
                    });

            }

            mapboxScript.src = 'https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.js';
            document.body.appendChild(mapboxScript)

    },
    watch: {
        style: {
            handler(value) {
                if (this.map)
                    this.map.setStyle("mapbox://styles/mapbox/" + value);
            },
        },
        zoom: {
            handler(value) {
                if (this.map)
                    this.map.setZoom(value);
            },
        },
        center: {
            handler(value) {
                if (this.map)
                    this.map.setCenter(value);
            },
        },
        marker: {
            handler(value){

                this.attachedPopup.forEach((marker) => marker.remove());
                this.attachedPopup = [];

                this.attachedMarker.forEach((marker) => marker.remove());
                this.attachedMarker = [];

                this.setMarker(value)
            },
        }
    },
    methods: {
        initMap() {

            mapboxgl.accessToken = this.options.token;
            this.map = new mapboxgl.Map({
                container: this.mapId,
                center: this.center,
                style: "mapbox://styles/mapbox/" + (this.style || this.options.defaultStyle),
                zoom: this.zoom || 1
            })
            
            this.map.scrollZoom.disable();

            this.setMarker(this.marker);

        },
        setMarker (markerdata) {

            if (markerdata) 

                    markerdata.forEach((marker) => {

                        if (marker.image) {
                            var markerEl = document.createElement('div');
                            markerEl.className = 'marker';
                            markerEl.style.backgroundImage = 'url(' + marker.image + ')';
                            markerEl.style.width = marker.width + 'px';
                            markerEl.style.height = marker.height + 'px';
                            markerEl.style.backgroundSize = '100%';
                        }

                        let curMarker = new mapboxgl.Marker({
                                anchor: marker.anchor || null,
                                element: markerEl || null
                            })
                            .setLngLat([marker.lng,marker.lat])
                            .addTo(this.map)
                            
                            this.attachedMarker.push(curMarker);

                        if (marker.haspopup) {

                            let curPopup = new mapboxgl.Popup({
                                        offset: parseInt(marker.popupoffset),
                                        focusAfterOpen: false
                                    })
                                    .setLngLat([marker.lng,marker.lat])

                                this.attachedPopup.push(curPopup)

                                this.$api.post('map/converter', { markdown: marker.popup}).then(res => {

                                    curPopup.setHTML(res.html).addTo(this.map)

                                });

                        };

                    })
                
                
        }
    },
  };
</script>

<style lang="scss">
    .map-wrapper {
        position: relative;
        .map { min-height: 400px; overflow:hidden;}
    }
</style>
