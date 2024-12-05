<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Add New Package</h2>
        <button class="close-btn" @click="$emit('close')">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <form @submit.prevent="handleSubmit" class="package-form">
        <div class="form-group">
          <label for="packageName">Package Name</label>
          <input
            type="text"
            id="packageName"
            v-model="formData.name"
            required
            placeholder="Enter package name"
          />
        </div>
        
        <div class="form-group">
          <label>Packs</label>
          <select v-model="formData.pack" class="form-select">
            <option value="" disabled selected>Select number of packs</option>
            <option value="50">50 pax</option>
            <option value="100">100 pax</option>
            <option value="150">150 pax</option>
            <option value="200">200 pax</option>
            <option value="250">250 pax</option>
            <option value="300">300 pax</option>
            <option value="350">350 pax</option>
            <option value="400">400 pax</option>
            <option value="450">450 pax</option>
            <option value="500">500 pax</option>
          </select>
        </div>

        <div class="form-group">
          <label for="eventType">Event Type</label>
          <select id="eventType" v-model="formData.eventType" required>
            <option value="">Select event type</option>
            <option value="Wedding">Wedding</option>
            <option value="Debut">Debut</option>
            <option value="Christening">Christening</option>
            <option value="Party">Party</option>
          </select>
        </div>

        <div class="form-group">
          <label for="price">Price (₱)</label>
          <input
            type="number"
            id="price"
            v-model="formData.price"
            required
            min="0"
            step="0.01"
            placeholder="Enter price"
          />
        </div>

        <div class="form-group">
          <label for="description">Description</label>
          <textarea
            id="description"
            v-model="formData.description"
            required
            rows="4"
            placeholder="Enter package description"
          ></textarea>
        </div>

        <div class="form-group">
          <label>Inclusions</label>
          <div class="inclusions-list">
            <div v-for="(inclusion, index) in formData.inclusions" :key="index" class="inclusion-item">
              <input
                type="text"
                v-model="formData.inclusions[index]"
                placeholder="Enter inclusion"
                class="w-full"
                style="width: 100%"
              />
              <button type="button" @click="removeInclusion(index)" class="remove-btn">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <button type="button" @click="addInclusion" class="add-btn">
            <i class="fas fa-plus"></i> Add Inclusion
          </button>
        </div>

        <div class="form-group">
          <label for="image">Package Image</label>
          <div class="image-upload">
            <div class="upload-placeholder" v-if="!imagePreview">
              <input type="file" id="image" @change="handleImageUpload" accept="image/*" class="file-input" />
              <i class="fas fa-cloud-upload-alt"></i>
              <span>Click to upload image</span>
            </div>
            <img v-else :src="imagePreview" alt="Preview" class="image-preview" />
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="cancel-btn" @click="$emit('close')">Cancel</button>
          <button type="submit" class="submit-btn" :disabled="isSubmitting">
            <i class="fas fa-spinner fa-spin" v-if="isSubmitting"></i>
            {{ isSubmitting ? 'Adding...' : 'Add Package' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useAuth } from '@/composables/useAuth';
import axios from 'axios';
import Swal from 'sweetalert2';

const { token } = useAuth();

const emit = defineEmits(['close', 'success']);

const formData = reactive({
  name: '',
  eventType: '',
  price: '',
  description: '',
  pack: '',
  inclusions: [''],
  image: null
});

const imagePreview = ref(null);
const allPackages = ref([]);
const allGuests = ref([]);
const isSubmitting = ref(false);

const addInclusion = () => {
  formData.inclusions.push('');
};

const removeInclusion = (index) => {
  formData.inclusions = formData.inclusions.filter((_, i) => i !== index);
};

const handleImageUpload = (event) => {
  const file = event.target.files?.[0];
  if (!file) return;

  const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
  if (!allowedTypes.includes(file.type)) {
    alert('Please upload a valid image file (JPEG, PNG, or WebP)');
    event.target.value = '';
    return;
  }

  formData.image = file;
  
  const reader = new FileReader();
  reader.onload = (e) => {
    imagePreview.value = e.target.result;
  };
  reader.readAsDataURL(file);
};

const getAllGuessst = async () => {
  const response = await axios.get('http://127.0.0.1:8000/api/get-all-guest');
  allGuests.value = response.data;
};


const handleSubmit = async () => {
  try {
    isSubmitting.value = true;
    
    if (!formData.name || !formData.eventType || !formData.price || !formData.description || !formData.pack) {
      throw new Error('Please fill in all required fields');
    }

    const formDataToSend = {
      name: formData.name,
      eventType: formData.eventType,
      price: formData.price,
      description: formData.description,
      inclusions: JSON.stringify(formData.inclusions.filter(inc => inc.trim())),
      packs: formData.pack.toString(),
      image: formData.image
    }

    const response = await axios.post('http://127.0.0.1:8000/api/add-package', formDataToSend, {
      headers: {
        'Content-Type': 'multipart/form-data',
        'Accept': 'application/json'
      }
    });

    if (response.data) {
      Swal.fire({
        title: 'Success',
        text: 'Package added successfully',
        icon: 'success'
      }).then(() => {
        window.location.reload();
      });

    }
  } catch (error) {
    console.error('Error adding package:', error);
    alert(error.message || 'Failed to add package. Please try again.');
  } finally {
    isSubmitting.value = false;
  }
};

onMounted(() => {
  getAllGuessst();
}); 
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: var(--modal-background, #fff);
  border-radius: 12px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  padding: 2rem;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.modal-header h2 {
  font-size: 1.5rem;
  color: var(--text-color);
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: var(--text-color);
  cursor: pointer;
  padding: 0.5rem;
}

.package-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 500;
  color: var(--text-color);
}

input, select, textarea {
  padding: 0.75rem;
  border: 1px solid var(--border-color, #ddd);
  border-radius: 6px;
  font-size: 1rem;
  background: var(--input-background, #fff);
  color: var(--text-color);
}

textarea {
  resize: vertical;
}

.inclusions-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.inclusion-item {
  display: flex;
  gap: 0.5rem;
}

.remove-btn {
  padding: 0.5rem;
  background: var(--danger-color, #dc3545);
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.add-btn {
  padding: 0.75rem;
  background: var(--primary-color);
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.image-upload {
  position: relative;
  height: 200px;
  border: 2px dashed var(--border-color, #ddd);
  border-radius: 6px;
  overflow: hidden;
}

.file-input {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
}

.upload-placeholder {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  color: var(--text-muted, #666);
}

.upload-placeholder i {
  font-size: 2rem;
}

.image-preview {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
}

.cancel-btn {
  padding: 0.75rem 1.5rem;
  background: var(--secondary-color, #6c757d);
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.submit-btn {
  padding: 0.75rem 1.5rem;
  background: var(--primary-color);
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .modal-content {
    padding: 1rem;
  }

  .form-actions {
    flex-direction: column;
  }

  .cancel-btn, .submit-btn {
    width: 100%;
  }
}

.form-select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1rem;
  background: white;
  color: #333;
  cursor: pointer;
}

.form-select:focus {
  border-color: var(--primary-color);
  outline: none;
  box-shadow: 0 0 0 2px rgba(var(--primary-rgb), 0.1);
}
</style> 