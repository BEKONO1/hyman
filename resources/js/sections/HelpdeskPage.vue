<template>
  <section>
    <div class="text-center mb-4">
      <h2>{{ $t('messages.helpdesk') }}</h2>
      <p>{{ $t('messages.helpdesk_add_modal_title') }}</p>
    </div>

    <form @submit.prevent="onSubmit" enctype="multipart/form-data" novalidate>
      <div class="mb-3">
        <label for="subject" class="form-label">
          {{ $t('messages.subject') }} <span class="text-danger">*</span>
        </label>
        <input
          id="subject"
          v-model="subject"
          type="text"
          class="form-control"
          :class="{ 'is-invalid': errors.subject }"
          placeholder="Enter subject"
        />
        <div v-if="errors.subject" class="invalid-feedback">
          {{ errors.subject }}
        </div>
      </div>

      <div class="mb-3">
        <label for="description" class="form-label">
          {{ $t('messages.description') }} <span class="text-danger">*</span>
        </label>
        <textarea
          id="description"
          v-model="description"
          class="form-control"
          rows="4"
          :class="{ 'is-invalid': errors.description }"
          placeholder="Enter description"
        ></textarea>
        <div v-if="errors.description" class="invalid-feedback">
          {{ errors.description }}
        </div>
      </div>

      <div class="mb-3">
        <label for="file" class="form-label">{{ $t('messages.file_upload') }}</label>
        <input
          id="file"
          type="file"
          @change="handleFileChange"
          class="form-control"
          accept="image/*,.pdf,.doc,.docx"
        />
        <div v-if="errors.file" class="text-danger mt-1">
          {{ errors.file }}
        </div>
      </div>

      <div class="text-end">
        <button type="submit" class="btn btn-primary me-2">{{ $t('messages.submit') }}</button>
        <button type="button" class="btn btn-secondary" @click="resetForm">{{ $t('messages.close') }}</button>
      </div>
    </form>
  </section>
</template>

<script setup>
import { ref, reactive } from 'vue'

const subject = ref('')
const description = ref('')
const file = ref(null)

const errors = reactive({
  subject: '',
  description: '',
  file: '',
})

function validate() {
  let valid = true
  errors.subject = ''
  errors.description = ''
  errors.file = ''

  if (!subject.value.trim()) {
    errors.subject = 'Subject is required.'
    valid = false
  }
  if (!description.value.trim()) {
    errors.description = 'Description is required.'
    valid = false
  }
  if (file.value) {
    const maxSize = 5 * 1024 * 1024 // 5MB
    if (file.value.size > maxSize) {
      errors.file = 'File size must be less than 5MB.'
      valid = false
    }
  }
  return valid
}

function handleFileChange(event) {
  errors.file = ''
  const selected = event.target.files[0]
  if (selected) {
    file.value = selected
  } else {
    file.value = null
  }
}

function resetForm() {
  subject.value = ''
  description.value = ''
  file.value = null
  errors.subject = ''
  errors.description = ''
  errors.file = ''

  // Reset file input value manually
  const fileInput = document.getElementById('file')
  if (fileInput) fileInput.value = ''
}

async function onSubmit() {
  if (!validate()) return

  try {
    const formData = new FormData()
    formData.append('subject', subject.value)
    formData.append('description', description.value)
    if (file.value) formData.append('attachment', file.value)

    // Example: send formData via fetch or axios
    // await axios.post('/api/helpdesk', formData)

    alert('Helpdesk ticket submitted successfully!')

    resetForm()
  } catch (err) {
    alert('Failed to submit. Please try again.')
  }
}
</script>

<style scoped>
.is-invalid {
  border-color: #dc3545;
}
.invalid-feedback {
  display: block;
}
</style>
