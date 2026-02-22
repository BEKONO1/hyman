<template>
    <section ref="blogSection">
        <div class="row">
            <div class="col-md-12">
                <div class="float-end">
                    <div class="search-form input-group flex-nowrap align-items-center">
                        <input type="search" class="form-control rounded-3" name="search" v-model="search" placeholder="Search...">
                        <span v-if="search" class="input-group-text search-icon position-absolute text-body" @click="clearSearch" style="cursor: pointer;">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <line x1="6" y1="18" x2="18" y2="6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></line>
                                <line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></line>
                            </svg>
                        </span>
                        <span v-else class="input-group-text search-icon position-absolute text-body">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="11.7669" cy="11.7666" r="8.98856" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></circle><path d="M18.0186 18.4851L21.5426 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive rounded py-4">
            <table id="datatable" ref="tableRef" class="table custom-card-table"></table>
        </div>
    </section>

</template>
<script setup>
import $ from 'jquery';
import { computed,ref,watch} from 'vue';
import BlogCard from '../components/BlogCard.vue';
import BlogShimmer  from '../shimmer/BlogShimmer.vue'

import {useSection} from '../store/index'
import {useObserveSection} from '../hooks/Observer'
import useDataTable from '../hooks/Datatable'

const props = defineProps(['link']);

const search = ref('')
watch(() => search.value, () => ajaxReload())

const tableRef = ref(null);
  const ajaxReload = () => {
  if ($.fn.DataTable.isDataTable(tableRef.value)) {
    console.log('DataTable instance:', $(tableRef.value).DataTable());
    $(tableRef.value).DataTable().ajax.reload(null, false);
  } else {
    console.error('DataTable instance not found or not initialized yet.');
  }
};
const columns = ref([
  { data: 'name', title: '', orderable: false, }
]);


useDataTable({
  tableRef: tableRef,
  columns: columns.value,
  url: props.link,
  dom: '<"row align-items-center"><"table-responsive my-3" rt><"row align-items-center" <"col-md-6" l><"col-md-6 mt-md-0 mt-3" p>><"clear">',
  advanceFilter: () => {
    return {
        search: search.value,
    }
  }
});

const store = useSection()
const blog_data = computed(() => store.blog_list_data)

const [blogSection] = useObserveSection(() => store.get_blog_list({per_page: "all"}))

const clearSearch = () =>{
  search.value = '';
}
</script>
