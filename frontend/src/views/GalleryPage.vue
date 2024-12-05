<template>
  <div class="gallery-page">
    <!-- Header -->
    <div class="gallery-header">
      <h1>Our Portfolio</h1>
      <p>Browse through our collection of memorable events</p>
    </div>

    <!-- Filter Controls -->
    <div class="filter-controls">

      <div class="view-controls">
        <button
          class="view-btn"
          :class="{ active: viewMode === 'grid' }"
          @click="viewMode = 'grid'"
        >
          <i class="fas fa-th"></i>
          Grid
        </button>
        <button
          class="view-btn"
          :class="{ active: viewMode === 'masonry' }"
          @click="viewMode = 'masonry'"
        >
          <i class="fas fa-columns"></i>
          Masonry
        </button>
      </div>
    </div>

    <!-- Gallery Grid -->
    <div
      class="gallery-container"
      :class="{ 'masonry-layout': viewMode === 'masonry' }"
    >
      <div
        v-for="item in filteredItems"
        :key="item.id"
        class="gallery-item"
        :class="{ 'video-item': item.type === 'video' }"
        @click="openItem(item)"
      >
        <div class="item-thumbnail">
          <img :src="getImageUrl(item.image)" :alt="item.title" loading="lazy" />
          <div class="item-overlay">
            <div class="item-info">
              <h3>{{ item.title }}</h3>
              <p>{{ item.category }}</p>
            </div>
            <div class="item-type">
              <i
                class="fas"
                :class="item.type === 'video' ? 'fa-play-circle' : 'fa-image'"
              ></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Lightbox Modal -->
    <div v-if="selectedItem" class="lightbox" @click="closeLightbox">
      <div class="lightbox-content" @click.stop>
        <button class="close-btn" @click="closeLightbox">
          <i class="fas fa-times"></i>
        </button>

        <div class="lightbox-main">
          <img
            :src="getImageUrl(selectedItem.image)"
            :alt="selectedItem.title"
            class="lightbox-image"
          />
        </div>

        <div class="lightbox-info">
          <div class="info-header">
            <h2>{{ selectedItem.title }}</h2>
          </div>
          <p class="description">{{ selectedItem.description }}</p>
          <div class="meta-info">
            <span class="date">
              <i class="fas fa-calendar"></i>
              {{ formatDate(selectedItem.created_at) }}
            </span>
          </div>
        </div>

        <!-- Navigation -->
        <button
          class="nav-btn prev"
          @click="showPrevious"
          v-if="hasPrevious"
        >
          <i class="fas fa-chevron-left"></i>
        </button>
        <button
          class="nav-btn next"
          @click="showNext"
          v-if="hasNext"
        >
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
// import { useGallery } from '@/composables/useGallery';s
import axios from 'axios';

// const { galleryItems, isLoading, fetchGalleryItems, hasMoreItems, loadMore } = useGallery();

const viewMode = ref('grid');
const activeCategory = ref('all');
const selectedItem = ref(null);
const portfolios = ref([]);
const isLoading = ref(false);

const filteredItems = computed(() => portfolios.value);

const hasPrevious = computed(() => {
  if (!selectedItem.value) return false;
  const currentIndex = filteredItems.value.findIndex(item => item.id === selectedItem.value.id);
  return currentIndex > 0;
});

const defaultImageUrl = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDIwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjIwMCIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNFNUU3RUIiLz48cGF0aCBkPSJNODAgOTBIMTIwVjExMEg4MFY5MFoiIGZpbGw9IiM5Q0EzQUYiLz48cGF0aCBkPSJNNjUgNjBIMTM1VjgwSDY1VjYwWiIgZmlsbD0iIzlDQTNBRiIvPjwvc3ZnPg==';

const getImageUrl = (imagePath) => {
  if (imagePath) {
    return `${import.meta.env.VITE_API_URL}/storage/${imagePath}`;
  } else {
    return defaultImageUrl;
  }
};

const hasNext = computed(() => {
  if (!selectedItem.value) return false;
  const currentIndex = filteredItems.value.findIndex(item => item.id === selectedItem.value.id);
  return currentIndex < filteredItems.value.length - 1;
});

const fetchportfolios = async () => {
  isLoading.value = true;
  const response = await axios.get(`${import.meta.env.VITE_API_URL}/api/get-all-portfolios`);
  portfolios.value = response.data;
  console.log(response.data);
};

const setCategory = async (category) => {
  activeCategory.value = category;
  await fetchGalleryItems(category);
};

const openItem = (item) => {
  selectedItem.value = item;
  document.body.style.overflow = 'hidden';
};

const closeLightbox = () => {
  selectedItem.value = null;
  document.body.style.overflow = '';
};

const showPrevious = () => {
  const currentIndex = filteredItems.value.findIndex(item => item.id === selectedItem.value.id);
  if (currentIndex > 0) {
    selectedItem.value = filteredItems.value[currentIndex - 1];
  }
};

const showNext = () => {
  const currentIndex = filteredItems.value.findIndex(item => item.id === selectedItem.value.id);
  if (currentIndex < filteredItems.value.length - 1) {
    selectedItem.value = filteredItems.value[currentIndex + 1];
  }
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

onMounted(async () => {
  await fetchportfolios();
});
</script>

<style scoped>
.gallery-page {
  padding: 4rem 2rem;
  background: var(--background-color);
}

.gallery-header {
  text-align: center;
  margin-bottom: 3rem;
}

.gallery-header h1 {
  font-size: 2.5rem;
  color: var(--text-color);
  margin-bottom: 1rem;
}

.gallery-header p {
  font-size: 1.2rem;
  color: var(--text-muted);
}

.filter-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.category-filters {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.filter-btn,
.view-btn {
  padding: 0.75rem 1.5rem;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  background: var(--card-background);
  color: var(--text-color);
  font-size: 1rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s;
}

.filter-btn:hover,
.view-btn:hover,
.filter-btn.active,
.view-btn.active {
  background: var(--primary-color);
  color: white;
  border-color: var(--primary-color);
}

.view-controls {
  display: flex;
  gap: 0.5rem;
}

.gallery-container {
  display: grid;
  gap: 1.5rem;
  margin-bottom: 3rem;
}

.gallery-container:not(.masonry-layout) {
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
}

.masonry-layout {
  column-count: 3;
  column-gap: 1.5rem;
}

@media (max-width: 1024px) {
  .masonry-layout {
    column-count: 2;
  }
}

@media (max-width: 640px) {
  .masonry-layout {
    column-count: 1;
  }
}

.gallery-item {
  break-inside: avoid;
  margin-bottom: 1.5rem;
}

.item-thumbnail {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
}

.item-thumbnail img {
  width: 100%;
  height: auto;
  display: block;
  transition: transform 0.3s;
}

.item-thumbnail:hover img {
  transform: scale(1.05);
}

.item-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 1.5rem;
  opacity: 0;
  transition: opacity 0.3s;
}

.item-thumbnail:hover .item-overlay {
  opacity: 1;
}

.item-info {
  color: white;
}

.item-info h3 {
  font-size: 1.25rem;
  margin-bottom: 0.5rem;
}

.item-info p {
  font-size: 0.875rem;
  opacity: 0.9;
}

.item-type {
  align-self: flex-end;
  font-size: 2rem;
  color: white;
}

.load-more {
  text-align: center;
}

.load-more-btn {
  padding: 0.75rem 2rem;
  background: var(--primary-color);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  transition: background-color 0.2s;
}

.load-more-btn:hover:not(:disabled) {
  background: var(--primary-hover);
}

.load-more-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* Lightbox */
.lightbox {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.lightbox-content {
  position: relative;
  width: 90%;
  max-width: 1200px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.close-btn {
  position: absolute;
  top: -2rem;
  right: 0;
  background: none;
  border: none;
  color: white;
  font-size: 1.5rem;
  cursor: pointer;
  z-index: 1;
}

.lightbox-main {
  position: relative;
  width: 100%;
  height: 70vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

.lightbox-image,
.lightbox-video {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.lightbox-info {
  background: var(--card-background);
  padding: 1.5rem;
  border-radius: 12px;
}

.info-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.info-header h2 {
  font-size: 1.5rem;
  color: var(--text-color);
}

.category-badge {
  padding: 0.5rem 1rem;
  background: var(--primary-color);
  color: white;
  border-radius: 20px;
  font-size: 0.875rem;
}

.description {
  color: var(--text-muted);
  margin-bottom: 1rem;
}

.meta-info {
  display: flex;
  gap: 1.5rem;
  color: var(--text-muted);
  font-size: 0.875rem;
}

.meta-info span {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.nav-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0, 0, 0, 0.5);
  color: white;
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background-color 0.2s;
}

.nav-btn:hover {
  background: rgba(0, 0, 0, 0.8);
}

.nav-btn.prev {
  left: -60px;
}

.nav-btn.next {
  right: -60px;
}

@media (max-width: 768px) {
  .gallery-page {
    padding: 2rem 1rem;
  }

  .filter-controls {
    flex-direction: column;
    align-items: stretch;
  }

  .category-filters,
  .view-controls {
    justify-content: center;
  }

  .lightbox-content {
    width: 95%;
  }

  .nav-btn {
    top: auto;
    bottom: -60px;
    transform: none;
  }

  .nav-btn.prev {
    left: 0;
  }

  .nav-btn.next {
    right: 0;
  }
}
</style> 