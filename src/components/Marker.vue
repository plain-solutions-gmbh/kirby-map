<template>
  <div class="marker-item" @click="open">
    <div v-if="url" class="marker-item-image">
      <img :src="url" />
    </div>

    <div class="marker-item-description">
      <p>{{ description ?? $t("empty") }}</p>
      <p>{{ coordinates }}</p>
    </div>
  </div>
</template>

<script>
export default {
  computed: {
    url() {
      return this.content.image?.[0]?.icon?.url;
    },

    description() {
      return this.content.coors?.name;
    },

    coordinates() {
      return this.content.coors
        ? `${this.content.coors.lat},${this.content.coors.lng}`
        : null;
    },
  },
};
</script>

<style lang="scss">
.marker-item {
  display: flex;
  align-items: center;
}

.marker-item-image {
  width: 2.5rem;
  height: 2.5rem;

  img {
    width: 100%;
    height: 100%;
    object-fit: contain;
  }
}

.marker-item-description {
  padding-left: 1rem;

  > p:last-child {
    color: var(--color-border);
    font-style: italic;
  }
}
</style>
