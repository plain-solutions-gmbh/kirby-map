<template>
  <k-dialog
    class="k-dialog"
    v-bind="$props"
    :submit-button="false"
    size="medium"
    @cancel="$emit('cancel')"
    @submit="send()"
  >
    <k-text-field
      class="k-geolocation-search"
      type="text"
      theme="field"
      :label="$t('map.blocks.marker.location.name')"
      icon="search"
      :value="query"
      @input="searchLocation($event)"
    />

    <k-collection
      :items="geoData"
      :empty="{ icon: 'alert', text: $t(searchError, 1) }"
      @item="selectDropdown($event)"
    />
  </k-dialog>
</template>

<script>
export default {
  extends: "k-dialog",
  props: {
    token: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      geoData: [],
      query: "",
      error: null,
      searchError: "search.min",
    };
  },

  methods: {
    async searchLocation(query) {
      if (!this.token) {
        this.searchError = "maps.error.token";
        return;
      }

      if (query.length === 0) {
        this.searchError = "maps.search.empty";
        return;
      }

      //this.geoData = [];
      try {
        const response = await fetch(
          `https://api.mapbox.com/geocoding/v5/mapbox.places/${query}.json?types=address,country,postcode,place,locality&limit=5&access_token=${this.token}`
        );
        const data = await response.json();
        this.searchError = "maps.field.geolocation.error.empty";

        if (data.features) {
          const toShow = data.features.slice(0, 8);

          if (toShow) {
            this.geoData = toShow.map(({ place_name, place_type, center }) => ({
              text: place_name,
              name: place_name,
              info: this.$t(`maps.field.geolocation.${place_type[0]}`),
              lat: center[1],
              lng: center[0],
            }));
          } else {
            this.searchError = "maps.search.error";
          }
        } else {
          this.searchError = data?.message;
        }
      } catch (err) {
        this.searchError = "maps.search.error";
      }
      //Cause text field clear after geoData fill
      this.query = query;
    },

    selectDropdown(selection) {
      delete selection.type;
      delete selection.text;
      this.$emit("input", selection);
    },
  },
};
</script>

<style>
.k-geolocation-search {
  margin-bottom: var(--spacing-4);
}
</style>
