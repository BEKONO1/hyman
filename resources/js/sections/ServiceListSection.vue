<template>
    <!-- Tab panes -->
    <div>
        <div class="tab-pane h-100 active show" id="cleaning">
            <div class="row our-service animated fadeInUp h-100">
                <!-- Check if service array is empty -->
                <template v-if="!service || service.length === 0">
                    <div class="col-12 h-100">

                        <p class="text-center no-data-found" v-if="user_lat == null && user_lng == null">
                            {{ $t('messages.nodata') }}
                        </p>

                        <p class="text-center no-data-found" v-else >
                            {{ $t('messages.no_data_in_zone') }}
                        </p>
                    </div>
                </template>
                <!-- Render service cards if service array is not empty -->
                <template v-else>
                    <div v-for="data in getLimitedService(service)" :key="data"
                        :class="is_provider_detail == true ? 'col-md-6' : 'col-xl-4 col-sm-6'">
                        <ServiceCard :user_id="user_id" :service_id="data.id" :provider_id="data.provider_id"
                            :image="data.attchments[0]" :userImage="data.provider_image" :userName="data.provider_name"
                            :reviewNo="data.total_rating" :reviewCount="data.total_review" :title="data.name"
                            :price="data.price" :duration="data.duration" :favourite="isFavourite(data.id)"
                            :visit_type="data.visit_type" :discount="data.discount"
                           />
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script setup>

import ServiceCard from '../components/ServiceCard.vue';

const props = defineProps(['service', 'is_provider_detail', 'max_records', 'user_id', 'favourite', 'user_lat', 'user_lng']);

const getLimitedService = (service) => {
    return props.max_records !== null ? service.slice(0, props.max_records) : service;
};

const isFavourite = (serviceId) => {
    return props.favourite !== null ? props.favourite.some(item => item.service_id === serviceId) : false;
};

</script>
