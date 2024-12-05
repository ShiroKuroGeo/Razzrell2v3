<template>
    <div class="modal-overlay" @click.self="$emit('close')">
      <div class="modal-content">
        <div class="modal-header">
          <h2>Booking Details</h2>
          <button class="close-btn" @click="$emit('close')">
            <i class="fas fa-times"></i>
          </button>
        </div>
  
        <form class="edit-form">
          <div class="form-section">
            <div class="section">
                <img :src="getImageUrl(packages.package_image)" class="details-image" :alt="packages.package_name" />
              <h3>Customer Details</h3>
              <div class="details">
                <h4>Name: {{ bookings.full_name }}</h4>
                <h4>Email: {{ bookings.email }}</h4>
                <h4>Phone: {{ bookings.phone }}</h4>
              </div>
            </div>

            <div class="section">
              <h3>Event Details</h3>
              <div class="details">
                <h4>Event Type: {{ packages.package_type }}</h4>
                <h4>Event Date: {{ bookings.event_date }}</h4>
                <h4>Event Time: {{ bookings.event_time }}</h4>
                <h4>Venue: {{ bookings.venue_name }}</h4>
              </div>
            </div>

            <div class="section">
              <h3>Package Details</h3>
              <div class="details">
                <h4>Package Price: ₱{{ formatNumber(packages.package_price) }}</h4>
                <h4>Package Inclusions:</h4>
                <ul>
                  <li v-for="(inclusion, index) in packages.package_inclusion ? (Array.isArray(packages.package_inclusion) ? packages.package_inclusion : JSON.parse(packages.package_inclusion)) : []">
                    - {{ inclusion }}
                  </li>
                </ul>
              </div>
            </div>
          </div>
  
          <div class="form-actions">
            <button type="button" class="cancel-btn" @click="$emit('close')">Okay</button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, reactive, computed, onMounted } from 'vue';
  import { useAuth } from '@/composables/useAuth';
  import axios from 'axios';
  import Swal from 'sweetalert2';
  
  const props = defineProps({
    booking: {
      type: Object,
      required: true
    }
  });
  
  const emit = defineEmits(['close', 'update']);
  const { token } = useAuth();
  
  const bookings = ref([]);
  const packages = ref([]);
  const isSubmitting = ref(false);
  
  const selectedPackage = computed(() => {
    return bookings.value.find(pkg => pkg.id === props.booking.id);
  });
  

    const formatNumber = (num) => {
        if (num === null || num === undefined) return '';
        return Number(num).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    };

    const defaultImageUrl = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDIwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjIwMCIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNFNUU3RUIiLz48cGF0aCBkPSJNODAgOTBIMTIwVjExMEg4MFY5MFoiIGZpbGw9IiM5Q0EzQUYiLz48cGF0aCBkPSJNNjUgNjBIMTM1VjgwSDY1VjYwWiIgZmlsbD0iIzlDQTNBRiIvPjwvc3ZnPg==';

    const getImageUrl = (imagePath) => {
        if (imagePath) {
            return `${import.meta.env.VITE_API_URL}/storage/${imagePath}`;
        } else {
            return defaultImageUrl;
        }
    };

  const fetchBooking = async () => {
    const response = await axios.get(`http://127.0.0.1:8000/api/get-booking-by-id/${props.booking.id}`);
    bookings.value = response.data;
    packages.value = response.data.package;

  };

  onMounted(async () => {
    await fetchBooking();
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
    max-width: 800px;
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
  
  
  .form-section {
    background: var(--card-background);
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  }
  
  .form-section h3 {
    font-size: 1.2rem;
    color: var(--text-color);
    margin-bottom: 1.5rem;
  }
  
  .form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
  }
  
  .form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1rem;
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
  
    .form-row {
      grid-template-columns: 1fr;
    }
  
    .form-actions {
      flex-direction: column;
    }
  
    .cancel-btn, .submit-btn {
      width: 100%;
    }
  }
  .details{
    margin-left: 1rem;
    margin-bottom: 1rem;
  }
  .details-image{
    width: 100px;
    height: 100px;
    border-radius: 10px;
    margin-bottom: 1rem;
    margin-left: 1rem;
    margin-top: 1rem;
    object-fit: cover;
    border: 1px solid var(--border-color, #ddd);

  }
  </style> 