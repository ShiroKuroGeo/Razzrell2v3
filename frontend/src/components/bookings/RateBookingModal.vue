<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Rate Booking</h2>
        <button class="close-btn" @click="$emit('close')">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <form @submit.prevent="handleSubmit" class="portfolio-form">
        <div class="form-group">
          <label for="portfolioName">Rate</label>
          <select name="rating" id="rating" v-model="formData.rating" required>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
          </select>
        </div>

        <div class="form-group">
          <label for="description">Write Review</label>
          <textarea
            id="description"
            v-model="formData.review"
            required
            rows="4"
            placeholder="Enter Review"
          ></textarea>
        </div>

        <div class="form-actions">
          <button type="button" class="cancel-btn" @click="$emit('close')">Cancel</button>
          <button type="submit" class="submit-btn" :disabled="isSubmitting">
            <i class="fas fa-spinner fa-spin" v-if="isSubmitting"></i>
            {{ isSubmitting ? 'Adding...' : 'Add Portfolio' }}
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

const props = defineProps({
  booking: {
    type: Object,
    required: true
  }
});

const { token } = useAuth();

const emit = defineEmits(['close', 'success']);

const formData = reactive({
  package_id: props.booking.package_id,
  rating: 1,
  review: null,
  user_id: props.booking.user_id,
  booking_id: props.booking.id
});

const allGuests = ref([]);
const isSubmitting = ref(false);

const getAllGuessst = async () => {
  const response = await axios.get('http://127.0.0.1:8000/api/get-all-guest');
  allGuests.value = response.data;
};

const handleSubmit = async () => {
  try {
    const formDataToSend = new FormData();
    formDataToSend.append('package_id', formData.package_id);
    formDataToSend.append('rating', formData.rating);
    formDataToSend.append('review', formData.review);
    formDataToSend.append('user_id', formData.user_id);
    formDataToSend.append('booking_id', formData.booking_id);

    const response = await axios.post('http://127.0.0.1:8000/api/add-rating', formDataToSend, {
      headers: {
        'Content-Type': 'multipart/form-data',
        'Authorization': `Bearer ${token.value}`
      }
    });

    if(response.status === 200){
      Swal.fire({
        title: 'Success',
        text: response.data.message,
        icon: 'success'
      }).then(() => {
        window.location.reload();
      });
    }else{
      alert(response.data.message || 'Failed to add portfolio. Please try again.');
    }
    
  } catch (error) {
    console.error('Error adding portfolio:', error);
    alert(error.message || 'Failed to add portfolio. Please try again.');
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

.portfolio-form {
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