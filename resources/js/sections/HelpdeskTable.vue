<template>
    <section ref="bookingSection">
        <div class="row">
            <div class="col-md-6 col-sm-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="datatable-filter">
                        <!-- <select v-model="filterStatus" class="form-select" @change="handleFilterChange" >
                            <option value="">{{$t('messages.all')}}</option>
                        </select> -->
                    </div>
                    <a class="btn-tab-desk" :class="{ active: filterStatus === '' }" href="javascript:void(0);"
                        @click="setFilterStatus('')">
                        {{ $t('messages.all') }}
                    </a>

                    <a class="btn-tab-desk" :class="{ active: status === 0 }" href="javascript:void(0);"
                        @click="setStatus(0)">{{ $t('messages.open') }}</a>
                    <a class="btn-tab-desk" :class="{ active: status === 1 }" href="javascript:void(0);"
                        @click="setStatus(1)">{{ $t('messages.closed') }}</a>
                </div>
            </div>
            <div class="col-md-6 col-sm-4 mt-sm-0 mt-3">
                <div class="float-sm-end">
                    <div class="d-flex justify-content-end">
                        <a v-if="canhelpdesklist" href="javascript:void(0);" class="btn btn-primary"
                            data-bs-toggle="modal" data-bs-target="#helpdeskModal">{{ $t('messages.add_new') }}</a>
                    </div>
                </div>
            </div>

        </div>

        <div class="table-responsive rounded py-4">
            <table id="helpdesk-datatable" ref="tableRef" class="table custom-card-table"></table>
        </div>
    </section>

    <!-- ===================
    Review Modal
    ========================== -->
    <div class="modal fade" id="helpdeskModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="helpdeskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-capitalize" id="helpdeskModalLabel">{{ $t('messages.add_new') }}</h5>
                    <span class="text-primary custom-btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="closeModal()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="41" viewBox="0 0 40 41" fill="none">
                            <rect x="12" y="11.8381" width="17" height="17" fill="white"></rect>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M12.783 4.17017H27.233C32.883 4.17017 36.6663 8.13683 36.6663 14.0368V27.6552C36.6663 33.5385 32.883 37.5035 27.233 37.5035H12.783C7.13301 37.5035 3.33301 33.5385 3.33301 27.6552V14.0368C3.33301 8.13683 7.13301 4.17017 12.783 4.17017ZM25.0163 25.8368C25.583 25.2718 25.583 24.3552 25.0163 23.7885L22.0497 20.8218L25.0163 17.8535C25.583 17.2885 25.583 16.3552 25.0163 15.7885C24.4497 15.2202 23.533 15.2202 22.9497 15.7885L19.9997 18.7535L17.033 15.7885C16.4497 15.2202 15.533 15.2202 14.9663 15.7885C14.3997 16.3552 14.3997 17.2885 14.9663 17.8535L17.933 20.8218L14.9663 23.7718C14.3997 24.3552 14.3997 25.2718 14.9663 25.8368C15.2497 26.1202 15.633 26.2718 15.9997 26.2718C16.383 26.2718 16.7497 26.1202 17.033 25.8368L19.9997 22.8885L22.9663 25.8368C23.2497 26.1385 23.6163 26.2718 23.983 26.2718C24.3663 26.2718 24.733 26.1202 25.0163 25.8368Z"
                                fill="currentColor">
                            </path>
                        </svg>
                    </span>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="formSubmit">
                        <input type="hidden" name="_token" :value="csrfToken">
                        <div class="mb-4">
                            <label class="form-label text-capitalize">{{ $t('messages.subject') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="subject" placeholder="subject" id="subject"
                                name="subject" @input="handleSubjectInput" maxlength="100">
                            <div class="d-flex justify-content-end mt-1">
                                <small style="color: #6c757d;">{{ subjectCount }}/100</small>
                            </div>
                            <span v-if="errorMessages['subject']">
                                <ul class="text-danger">
                                    <li v-for="err in errorMessages['subject']" :key="err">{{ err }}</li>
                                </ul>
                            </span>
                            <span class="text-danger">{{ errors.subject }}</span>
                            <div class="error-message" style="color: red;margin-top: 5px;">{{ subjectError }}</div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-capitalize">{{ $t('messages.description') }} <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" v-model="description" id="description" name="description"
                                rows="4" placeholder="Write Here..." @input="clearError('description')"></textarea>
                            <span v-if="errorMessages['description']">
                                <ul class="text-danger">
                                    <li v-for="err in errorMessages['description']" :key="err">{{ err }}</li>
                                </ul>
                            </span>
                            <span class="text-danger">{{ errors.description }}</span>
                            <div class="error-message" style="color: red;margin-top: 5px;">{{ descriptionError }}</div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-capitalize" for="helpdesk_attachment">{{ $t('messages.image')
                                }}</label>
                            <div class="custom-file">
                                <input type="file" class="form-control" id="helpdesk_attachment"
                                    name="helpdesk_attachment" ref="fileInput" accept=".jpeg, .jpg, .png, .gif, .pdf"
                                    @change="fileUpload" />
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 flex-wrap justify-content-end">
                            <button type="submit" class="btn btn-primary"><span v-if="IsLoading == 1"
                                    class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"></span><span v-else>{{ $t('messages.submit') }}</span></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useField, useForm } from 'vee-validate';
import { STORE_HELPDESK_API } from '../data/api';
import * as yup from 'yup';
import Swal from 'sweetalert2-neutral';
import useDataTable from '../hooks/Datatable'
import { useSection } from '../store/index'
import { useObserveSection } from '../hooks/Observer'

const props = defineProps(['employee_id', 'link', 'canhelpdesklist']);
console.log(props.canhelpdesklist)
const IsLoading = ref(0)
const status = ref(0);
const filterStatus = ref('');
console.log(status.value)
const setStatus = (value) => {
    status.value = value;
    filterStatus.value = '';  // Reset filter when using buttons
    ajaxReload();
};

const handleFilterChange = () => {
    if (filterStatus.value === '') {
        status.value = 0;  // Reset to default status when "All" is selected
    }
    ajaxReload();
}

// Datatable
const ajaxReload = () => window.$(tableRef.value).DataTable().ajax.reload(null, false)
const tableRef = ref(null)
const columns = ref([
    { data: 'name', title: '', orderable: false, searchable: false }
]);
useDataTable({
    tableRef: tableRef,
    columns: columns.value,
    url: props.link,
    per_page: 10,
    dom: '<"row align-items-center"><"table-responsive my-3" rt><"row align-items-center" <"col-md-6" l><"col-md-6 mt-md-0 mt-3" p>><"clear">',
    advanceFilter: () => {
        return {
            status: filterStatus.value || status.value,
        }
    }
});

const fileInput = ref(null);
const fileUpload = async (e) => {
    let file = e.target.files[0]
    await readFile(file, (fileB64) => {
        ImageViewer.value = fileB64
    })
    helpdesk_attachment.value = file
}
const errorMessages = ref({})
const defaultData = () => {
    errorMessages.value = {}
    return {
        subject: '',
        description: '',
    }
}
const subjectError = ref('');
const descriptionError = ref('');
const validationSchema = yup.object({
    subject: yup.string().required('Subject is Required').max(100, 'Subject must not exceed 100 characters'),
    description: yup.string().required('Description is Required'),
})

const { handleSubmit, errors, resetForm } = useForm({
    validationSchema,
    initialValues: {
        subject: '',
        description: ''
    }
})

const { value: subject } = useField('subject')
const { value: description } = useField('description')

const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const formSubmit = handleSubmit(async () => {
    IsLoading.value = 1
    // Trim values to remove spaces
    const trimmedSubject = subject.value.trim();
    const trimmedDescription = description.value.trim();

    // Validate subject
    if (!trimmedSubject) {
        subjectError.value = 'Subject is required.';
    } else {
        subjectError.value = '';
    }
    // Validate description
    if (!trimmedDescription) {
        descriptionError.value = 'Description is required.';
    } else {
        descriptionError.value = '';
    }

    // Check if there are any validation errors
    if (subjectError.value || descriptionError.value) {
        IsLoading.value = 0; // Reset loading state
        return;
    }
    const formData = new FormData();
    formData.append('subject', subject.value);
    formData.append('description', description.value);
    formData.append('employee_id', props.employee_id);

    // Get files from the file input
    const files = fileInput.value.files;
    for (let i = 0; i < files.length; i++) {
        formData.append(`helpdesk_attachment_${i}`, files[i]); // Dynamic key for each attachment
    }
    formData.append('attachment_count', files.length); // Attach count

    try {
        const response = await fetch(STORE_HELPDESK_API, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            body: formData,
        });

        if (response.ok) {
            IsLoading.value = 0;
            const responseData = await response.json();
            Swal.fire({
                title: 'Done',
                text: responseData.message,
                icon: 'success',
                iconColor: '#5F60B9'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.reload();
                }
            });
            resetForm();
        } else {
            IsLoading.value = 0;
            console.error('Error saving Helpdesk:', response.statusText);
        }
    } catch (error) {
        IsLoading.value = 0;
        console.error('Error saving Helpdesk:', error);
    }
});

const clearError = (fieldName) => {
    switch (fieldName) {
        case 'subject':
            subjectError.value = '';
            break;
        case 'description':
            descriptionError.value = '';
            break;

    }
};

const subjectCount = computed(() => {
    return subject.value ? subject.value.length : 0;
})

const handleSubjectInput = () => {
    if (subject.value && subject.value.length > 100) {
        subject.value = subject.value.slice(0, 100);
    }
    clearError('subject');
}

// const closeModal=()=>{
//     ratingval.value = 0
//     componentKey.value += 1
//     review.value = ''
// }

onMounted(() => {
    defaultData()
    // setStatus(0);
})

</script>
