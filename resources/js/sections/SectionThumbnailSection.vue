<template>
  <div class="image-slider-container" 
       @mouseenter="handleMouseEnter"
       @mouseleave="handleMouseLeave">
    <!-- Main Image Display -->
    <div class="main-slider mb-4">
      <Swiper
        class="main-swiper"
        :modules="[Controller, Navigation]"
        :slides-per-view="1"
        :space-between="10"
        :loop="props.attachments.length > 1"
        :grab-cursor="true"
        :navigation="false"
        :speed="400"
        @swiper="setMainSwiper"
        @slideChange="onSlideChange"
        :controller="{ control: thumbsSwiper }"
      >
        <SwiperSlide v-for="(image, index) in props.attachments" :key="index">
          <div class="main-image-wrapper">
            <img 
              :src="image" 
              :alt="`Image ${index + 1}`" 
              loading="lazy" 
              class="main-image img-fluid"
            />
          </div>
        </SwiperSlide>
      </Swiper>
    </div>

    <!-- Thumbnail Navigation -->
    <div class="thumb-slider" v-if="props.attachments.length > 1">
      <Swiper
        class="thumb-swiper"
        :modules="[Controller]"
        :slides-per-view="4"
        :space-between="10"
        :slide-to-clicked-slide="true"
        :watch-slides-progress="true"
        :controller="{ control: mainSwiper }"
        :breakpoints="{
          320: { slidesPerView: 3, spaceBetween: 8 },
          640: { slidesPerView: 4, spaceBetween: 10 },
          768: { slidesPerView: 5, spaceBetween: 12 },
          1024: { slidesPerView: 6, spaceBetween: 15 }
        }"
        @swiper="setThumbsSwiper"
        @slideChange="onThumbSlideChange"
      >
        <SwiperSlide v-for="(image, index) in props.attachments" :key="index">
          <div 
            class="thumb-wrapper" 
            :class="{ active: currentSlide === index }"
            @click="goToSlide(index)"
          >
            <img 
              :src="image" 
              :alt="`Thumbnail ${index + 1}`" 
              loading="lazy" 
              class="thumb-image img-fluid"
            />
          </div>
        </SwiperSlide>
      </Swiper>
    </div>
  </div>
</template>

<script setup>
import { ref, onBeforeUnmount, nextTick, onMounted } from 'vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Controller, Navigation } from 'swiper';

// Props
const props = defineProps({
  attachments: {
    type: Array,
    required: true,
    default: () => []
  }
});

// Swiper instances and state
const mainSwiper = ref(null);
const thumbsSwiper = ref(null);
const currentSlide = ref(0);
const isInitialized = ref(false);

// Auto scroll timer
let autoScrollTimer = null;
const isHovered = ref(false);

// Start auto scroll
const startAutoScroll = () => {
  if (autoScrollTimer) clearInterval(autoScrollTimer);
  
  autoScrollTimer = setInterval(() => {
    if (!isHovered.value && mainSwiper.value && props.attachments.length > 1) {
      const nextIndex = (currentSlide.value + 1) % props.attachments.length;
      goToSlide(nextIndex);
    }
  }, 2000); // Scroll every 2 seconds
};

// Stop auto scroll
const stopAutoScroll = () => {
  if (autoScrollTimer) {
    clearInterval(autoScrollTimer);
    autoScrollTimer = null;
  }
};

// Mouse enter/leave handlers
const handleMouseEnter = () => {
  isHovered.value = true;
};

const handleMouseLeave = () => {
  isHovered.value = false;
};

// Swiper setters
const setMainSwiper = async (swiper) => {
  try {
    mainSwiper.value = swiper;
    await nextTick();
    checkInitialization();
  } catch (error) {
    console.error('Error setting main swiper:', error);
  }
};

const setThumbsSwiper = async (swiper) => {
  thumbsSwiper.value = swiper;
  await nextTick();
  checkInitialization();
};

// Check if both swipers are ready
const checkInitialization = () => {
  if (mainSwiper.value && thumbsSwiper.value && !isInitialized.value) {
    isInitialized.value = true;
    currentSlide.value = 0;
    startAutoScroll(); // Start auto-scrolling after initialization
  }
};

// Handle main swiper slide change
const onSlideChange = (swiper) => {
  // Get the real index (important for loop mode)
  const realIndex = swiper.realIndex !== undefined ? swiper.realIndex : swiper.activeIndex;
  currentSlide.value = realIndex;
};

// Handle thumbnail swiper slide change (if needed)
const onThumbSlideChange = (swiper) => {
  // This handles thumbnail navigation if using slide-to-clicked-slide
  const realIndex = swiper.realIndex !== undefined ? swiper.realIndex : swiper.activeIndex;
  if (mainSwiper.value) {
    goToSlide(realIndex);
  }
};

// FIXED: Go to specific slide function
const goToSlide = (index) => {
  if (!mainSwiper.value) {
    console.warn('Main swiper not initialized');
    return;
  }

  // Validate index
  if (index < 0 || index >= props.attachments.length) {
    console.warn('Invalid slide index:', index);
    return;
  }

  // Update current slide immediately for visual feedback
  currentSlide.value = index;

  try {
    // For loop mode, use slideToLoop instead of slideTo
    if (mainSwiper.value.loopedSlides !== undefined && props.attachments.length > 1) {
      // Loop mode - use slideToLoop
      mainSwiper.value.slideToLoop(index, 300);
    } else {
      // Normal mode - use slideTo
      mainSwiper.value.slideTo(index, 300);
    }
  } catch (error) {
    console.error('Error navigating to slide:', error);
    // Fallback: try both methods
    try {
      mainSwiper.value.slideTo(index, 300);
    } catch (fallbackError) {
      console.error('Fallback navigation failed:', fallbackError);
    }
  }
};

// Alternative method if the above doesn't work
const forceGoToSlide = (index) => {
  if (!mainSwiper.value) return;
  
  currentSlide.value = index;
  
  // Multiple attempts to ensure slide change
  const attempts = [
    () => mainSwiper.value.slideToLoop && mainSwiper.value.slideToLoop(index, 300),
    () => mainSwiper.value.slideTo(index, 300),
    () => {
      // Force immediate change
      mainSwiper.value.slideTo(index, 0);
      mainSwiper.value.update();
    }
  ];
  
  for (const attempt of attempts) {
    try {
      attempt();
      break; // If successful, break out of loop
    } catch (error) {
      console.warn('Slide attempt failed:', error);
    }
  }
};

// Component lifecycle
onMounted(() => {
  startAutoScroll();
});

onBeforeUnmount(() => {
  stopAutoScroll();
  try {
    if (mainSwiper.value && mainSwiper.value.destroy) {
      mainSwiper.value.destroy(true, true);
    }
    if (thumbsSwiper.value && thumbsSwiper.value.destroy) {
      thumbsSwiper.value.destroy(true, true);
    }
  } catch (error) {
    console.error('Error during cleanup:', error);
  }
});
</script>

