<template>
    <section ref="bannerSection" class="py-5">
        <!-- Shimmer Loading -->
        <div v-if="!banners?.length" class="container">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="bg-light rounded-3 overflow-hidden" style="aspect-ratio: 16/9">
                        <div class="w-100 h-100 placeholder-glow">
                            <span class="placeholder w-100 h-100"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-light rounded-3 overflow-hidden" style="aspect-ratio: 16/9">
                        <div class="w-100 h-100 placeholder-glow">
                            <span class="placeholder w-100 h-100"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actual Content -->
        <div v-else class="container position-relative">
            <Swiper
                :modules="modules"
                :slides-per-view="2"
                :space-between="30"
                :loop="banners.length >= 3"
                :autoplay="{ delay: 3000, disableOnInteraction: false }"
                :pagination="{
                    clickable: true,
                    el: '.swiper-pagination'
                }"
                :navigation="false"
                :breakpoints="{
                    320: { slidesPerView: 1 },
                    768: { slidesPerView: 2 }
                }"
            >           
                <SwiperSlide v-for="banner in banners" :key="banner.id">
                    <a :href="banner.banner_type === 'service' 
                                ? `${baseUrl}/service-detail/${banner.service_id}` 
                                : banner.banner_redirect_url" 
                       class="d-block overflow-hidden rounded-3">
                        <img :src="banner.image" 
                             alt="banner-image"
                             class="img-fluid w-100 rounded-3" 
                             loading="lazy"
                             style="aspect-ratio: 16/9; object-fit: cover;">
                    </a>
                </SwiperSlide>
                
                <div class="swiper-pagination mt-4"></div>
            </Swiper>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, Pagination, Autoplay } from 'swiper';
import { useSection } from '../store/index';
import { useObserveSection } from '../hooks/Observer';
const baseUrl = document.querySelector('meta[name="baseUrl"]').getAttribute('content');

const modules = [Navigation, Pagination, Autoplay];
const store = useSection();
const banners = computed(() => store.promotional_banners_data);

const [bannerSection] = useObserveSection(() => 
    store.get_promotional_banner({ per_page: "all" })
);
</script>

<style scoped>
:deep(.swiper-pagination) {
    position: absolute;
    bottom: 20px;
    left: 0;
    right: 0;
    text-align: center;
}

:deep(.swiper-pagination-bullet) {
    width: 10px;
    height: 10px;
    background: var(--bs-primary);
    opacity: 0.5;
}

:deep(.swiper-pagination-bullet-active) {
    opacity: 1;
    transform: scale(1.2);
}

@media (max-width: 768px) {
    .shimmer-wrapper {
        grid-template-columns: 1fr;
    }
}
</style>



