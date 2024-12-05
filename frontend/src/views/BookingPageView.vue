<template>
  <div class="booking-page" style="margin-top: 5rem;">
    <div class="booking-container">
      <h1 style="font-weight: bold; font-size: 2rem;">Book Your Event</h1>
      
      <!-- Package Details -->
      <div class="package-details" v-if="selectedPackage">
        <img :src="selectedPackage.image" :alt="selectedPackage.name" class="package-image">
        <div class="package-info">
          <h2>{{ selectedPackage.name }}</h2>
          <p class="price">₱{{ selectedPackage.price.toLocaleString() }}</p>
          <p class="description">{{ selectedPackage.description }}</p>
        </div>
      </div>

      <!-- Booking Form -->
      <form class="booking-form">

        <div class="form-section">
          <h3>Contact Information</h3>
          <div class="form-grid">
            <div class="form-group">
              <label>Full Name*</label>
              <input 
                type="text" 
                v-model="formData.fullName"
                required
                placeholder="Enter your full name"
              />
            </div>
            
            <div class="form-group">
              <label>Email*</label>
              <input 
                type="email" 
                v-model="formData.email"
                required
                placeholder="Enter your email"
              />
            </div>
            <div class="form-group">
              <label>Phone Number*</label>
              <input 
                type="tel" 
                v-model="formData.phone"
                required
                placeholder="Enter your phone number"
                pattern="[0-9]{11}"
              />
            </div>
          </div>
        </div>

        <!-- Event Details -->
        <div class="form-section">
          <h3>Event Details</h3>
          <div class="form-grid">
            <div class="form-group">
              <label>Event Date*</label>
              <input 
                type="date" 
                v-model="formData.event_date"
                required
                :min="minDate"
              />
            </div>
            <div class="form-group">
              <label>Event Time*</label>
              <input 
                type="time" 
                v-model="formData.event_time"
                required
              />
            </div>
            <div class="form-group">
              <label for="guest">Guest Packs <small style="color: red;">( Only for 100 Guests )</small></label>
              <input type="number" v-model="formData.packs" required disabled min="1" :max="formData.packs" />
            </div>
            <div class="form-group">
              <label for="package">Package <small style="color: red;">( Selected Package )</small></label>
              <input type="text" v-model="formData.package_name" disabled />
            </div>
            <div class="form-group">
              <label>Venue Name*</label>
              <input 
                type="text" 
                v-model="formData.venue_name"
                required
                placeholder="Enter venue name"
              />
            </div>
          </div>
        </div>

        <!-- Special Requests -->
        <div class="form-section">
          <h3>Additional Information</h3>
          <div class="form-group">
            <label>Special Requests <small style="color: red;">( Optional )</small></label>
            <textarea 
              v-model="formData.special_requests"
              placeholder="Any special requests or notes for your event?"
              rows="4"
            ></textarea>
          </div>
        </div>

        <!-- Payment Method -->
        <div class="form-section">
          <h3>Payment Method</h3>
          <div class="payment-options">
            <label class="payment-option">
              <input type="radio" v-model="formData.payment_method" value="cash" required />
              <span>Cash Payment <small style="color: blue;">( 50% Down Payment Required )</small></span>
            </label>
          </div>
        </div>

        <div class="form-section">
          <div class="terms-agreement">
            <label class="terms-checkbox">
              <input type="checkbox" v-model="formData.termsAccepted" required />
              <span class="checkbox-text">
                I agree to the <a href="#" @click.prevent="showTerms">Terms and Conditions</a>
              </span>
            </label>
          </div>
        </div>


        <!-- Submit Button -->
        <div class="form-actions">
          <button class="submit-btn" :disabled="isSubmitting" @click="handleSubmit">
            <LoadingAnimation v-if="isSubmitting" />
            <span v-else>Confirm Booking</span>
          </button>
        </div>
      </form>
    </div>

    <!-- Terms Modal -->
    <ConfirmationModal
      v-if="showTermsModal"
      title="Terms and Conditions"
      :message="termsAndConditions"
      confirmText="I Agree"
      @confirm="acceptTerms"
      @cancel="closeTerms"
    />

    <!-- Success Modal -->
    <AlertModal
      v-if="showSuccessModal"
      type="success"
      title="Booking Successful!"
      message="Your booking has been confirmed. You will receive a confirmation email shortly."
      confirmText="View Booking"
      @confirm="viewBooking"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';
import LoadingAnimation from '@/components/ui/LoadingAnimation.vue';
import ConfirmationModal from '@/components/modals/ConfirmationModal.vue';
import AlertModal from '@/components/modals/AlertModal.vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const route = useRoute();
const router = useRouter();
const { user, token } = useAuth();

const selectedPackage = ref(null);
const allGuests = ref([]);
const allPackages = ref([]);
const isSubmitting = ref(false);
const showTermsModal = ref(false);
const showSuccessModal = ref(false);
const bookingId = ref(null);

// Initialize formData with empty values
const formData = ref({
  user_id: '',
  package_id: '',
  packs: '',
  fullName: '',
  event_date: '',
  event_time: '',
  venue_name: '',
  email: '',
  phone: '',
  special_requests: '',
  payment_method: '',
});

watch(() => user.value, (newUser) => {
  if (newUser) {
    formData.value.fullName = newUser.first_name || '';
    formData.value.email = newUser.email || '';
    formData.value.phone = newUser.phone_number || '';
  }
}, { immediate: true });

const minDate = computed(() => {
  const today = new Date();
  today.setDate(today.getDate() + 7);
  return today.toISOString().split('T')[0];
});

const getBookingById = async () => {
  const response = await axios.get(`http://127.0.0.1:8000/api/get-package-by-id/${route.params.id}`);
  
  formData.value.packs = response.data.packs;
  formData.value.package_name = response.data.package_name;
  formData.value.package_id = response.data.id;
  
  const userInfo = JSON.parse(localStorage.getItem('user_info'));
  
  formData.value.fullName = userInfo.first_name + ' ' + userInfo.last_name;
  formData.value.email = userInfo.email;
  formData.value.phone = userInfo.phone_number;
  formData.value.user_id = userInfo.id;
}

const getAllGuessst = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/get-all-guest');
    allGuests.value = response.data;
  } catch (error) {
    console.error('Error fetching gsuests:', error);
  }
};

const getAllPackages = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/get-all-packages');
    allPackages.value = response.data;
  } catch (error) {
    console.error('Error fetching packages:', error);
  }
};

const termsAndConditions = `
1. Booking and Payment
- A 50% down payment is required to confirm your booking
- Full payment must be made at least 3 days before the event
- Cancellations made less than 7 days before the event are non-refundable

2. Event Details
- Final guest count must be confirmed 7 days before the event
- Changes to event details must be made at least 3 days in advance
- Additional charges may apply for extended hours

3. Venue and Services
- Client is responsible for venue booking and permissions
- Package inclusions cannot be exchanged or refunded
- Additional services may be requested subject to availability
`;

onMounted(async () => {
  await getAllPackages();
  await getAllGuessst();
  await getBookingById();
  
  if (route.params.packageId) {
    const pkg = allPackages.value.find(p => p.id === parseInt(route.params.packageId));
    if (pkg) {
      selectedPackage.value = pkg;
      formData.value.package_id = pkg.id;
    }
  }
});

const showTerms = () => {
  showTermsModal.value = true;
};

const closeTerms = () => {
  showTermsModal.value = false;
};

const acceptTerms = () => {
  formData.value.termsAccepted = true;
  showTermsModal.value = false;
};

const handleSubmit = async () => {
  try {
    isSubmitting.value = true;
    const bookingData = {
      user_id: formData.value.user_id,
      package_id: formData.value.package_id,
      fullName: formData.value.fullName,
      event_date: formData.value.event_date,
      event_time: formData.value.event_time,
      venue_name: formData.value.venue_name,
      email: formData.value.email,
      phone: formData.value.phone,
      special_requests: formData.value.special_requests,
      payment_method: formData.value.payment_method,
    }


    const response = await axios.post('http://127.0.0.1:8000/api/add-booking', bookingData);

    if(response.status === 200){
      Swal.fire({
        icon: 'success',
        title: 'Booking Successful!',
        text: 'Your booking has been confirmed. You will receive a confirmation email shortly.',
      });
      formData.value.event_date = "";
      formData.value.event_time = "";
      formData.value.venue_name = "";
    }else{
      alert('Failed to create booking. Please try again.');
    }
  } catch (error) {
    console.error('Booking error:', error);
    alert('Failed to create booking. Please try again.');
  } finally {
    isSubmitting.value = false;
  }
};

const viewBooking = () => {
  router.push('/dashboard');
};
</script>

<style scoped>
.booking-page {
  padding: 2rem;
  background-color: var(--background-color);
  min-height: 100vh;
}

.booking-container {
  max-width: 800px;
  margin: 0 auto;
  background: var(--card-background);
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  padding: 2rem;
}

h1 {
  text-align: center;
  color: var(--text-color);
  margin-bottom: 2rem;
}

.package-details {
  display: flex;
  gap: 2rem;
  margin-bottom: 2rem;
  padding: 1rem;
  background: var(--background-color);
  border-radius: 8px;
}

.package-image {
  width: 200px;
  height: 150px;
  object-fit: cover;
  border-radius: 8px;
}

.package-info {
  flex: 1;
}

.price {
  font-size: 1.5rem;
  color: #4CAF50;
  font-weight: bold;
  margin: 0.5rem 0;
}

.form-section {
  margin-bottom: 2rem;
}

.form-section h3 {
  color: var(--text-color);
  margin-bottom: 1rem;
  font-size: 1.2rem;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.form-group {
  margin-bottom: 1rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  color: var(--text-color);
  font-weight: 500;
}

input, select, textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 1rem;
  background: var(--background-color);
  color: var(--text-color);
}

.terms-agreement {
  padding: 1rem;
  border-radius: 6px;
  background-color: var(--background-color);
}

.terms-checkbox {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.terms-checkbox input[type="checkbox"] {
  width: 18px;
  height: 18px;
  margin: 0;
}

.checkbox-text {
  font-size: 1rem;
  color: var(--text-color);
}

.checkbox-text a {
  color: #4CAF50;
  text-decoration: none;
}

.checkbox-text a:hover {
  text-decoration: underline;
}

.payment-options {
  display: flex;
  gap: 2rem;
  margin-top: 1rem;
}

.payment-option {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.radio-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.terms {
  margin: 2rem 0;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.payment-options {
  border: 1px solid #ddd;
  border-radius: 6px;
  padding: 1rem;
}

.payment-option {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1rem;
  color: var(--text-color);
}

.payment-option input[type="radio"] {
  width: auto;
  margin: 0;
}

input[type="date"],
input[type="time"] {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 1rem;
  background: var(--background-color);
  color: var(--text-color);
  /* Add these lines to control the calendar/clock icon colors */
  color-scheme: var(--color-scheme);
}

.payment-option span {
  font-weight: normal;
}

.submit-btn {
  width: 100%;
  padding: 1rem;
  background: #4CAF50;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 1.1rem;
  cursor: pointer;
  transition: background-color 0.3s;
}

.submit-btn:hover {
  background: #45a049;
}

.submit-btn:disabled {
  background: #ccc;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .booking-container {
    padding: 1rem;
  }

  .package-details {
    flex-direction: column;
  }

  .package-image {
    width: 100%;
    height: 200px;
  }

  .payment-options {
    flex-direction: column;
    gap: 1rem;
  }
}
</style>