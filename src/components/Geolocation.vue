<template>
  <k-field
    class="k-geolocation-field"
    v-bind="$props"
    :label="label"
    :required="required"
    :disabled="disabled"
  >
    <template #options>
      <k-button :text="$t('search')" icon="search" @click="openSearch()" />
    </template>

    <k-input
      type="text"
      theme="field"
      :value="location.name"
      :before="$t('maps.field.geolocation.name')"
      @input="setValue($event, 'name')"
    />

    <k-input
      type="text"
      theme="field"
      :value="location.lat"
      :before="$t('maps.field.geolocation.lat')"
      @input="setValue($event, 'lat')"
    />

    <k-input
      type="text"
      theme="field"
      :value="location.lng"
      :before="$t('maps.field.geolocation.lng')"
      @input="setValue($event, 'lng')"
    />

    <k-box v-if="error" :text="error" theme="negative" />
  </k-field>
</template>

<script>
export default {
  props: {
    token: {
      type: String,
      required: true,
    },
    label: {
      type: String,
      required: true,
    },
    value: {
      type: Object,
      default() {
        return {
          name: undefined,
          lat: undefined,
          lng: undefined,
        };
      },
    },
    required: Boolean,
    disabled: Boolean,
    default: {
      type: Object,
      default() {
        return {
          name: "",
          lat: 0,
          lng: 0,
        };
      },
    },
  },

  data() {
    return {
      error: null,
      searchError: "search.min",
    };
  },

  computed: {
    mapId() {
      return `map-${this._uid}`;
    },

    location() {
      return {
        name: this.value?.name ?? this.default?.name ?? "",
        lat: this.value?.lat ?? this.default?.lat ?? 0,
        lng: this.value?.lng ?? this.default?.lng ?? 0,
      };
    },
  },

  methods: {
    openSearch() {
      this.searchError = "maps.search.empty";
      const _this = this;
      this.$panel.dialog.open({
        component: "geolocationSearchDialog",
        props: {
          token: this.token,
        },
        on: {
          input(val) {
            _this.$emit("input", val);
            _this.$panel.dialog.close();
          },
        },
      });
    },

    setValue(value, key) {
      let v = {
        name: key === "name" ? value : this.location.name,
        lat: key === "lat" ? value : this.location.lat,
        lng: key === "lng" ? value : this.location.lng,
      };

      this.$emit("input", v);

      if (this.required && (!v.name || !v.lat || !v.lng)) {
        this.error = this.$t("maps.field.geolocation.error");
        return;
      }

      if (!v.name) {
        this.error = this.$t("maps.field.geolocation.name.error");
        return;
      }

      if (!v.lat || isNaN(v.lat) || Math.abs(v.lat) > 90) {
        this.error = this.$t("maps.field.geolocation.lat.error");
        return;
      }

      if (!v.lng || isNaN(v.lng) || Math.abs(v.lng) > 180) {
        this.error = this.$t("maps.field.geolocation.lng.error");
        return;
      }

      this.error = null;
    },
  },
};
</script>
