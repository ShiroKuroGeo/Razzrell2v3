<template>
  <div class="admin-dashboard">
    
    <main class="dashboard-content">
      <div class="overview-cards">
        <div class="card">
          <div class="card-info">
            <h3>Total Bookings</h3>
            <div class="card-value">{{ stats.totalBookings }}</div>
            <div class="card-trend" :class="{ up: stats.bookingTrend > 0 }">
              <i class="fas" :class="stats.bookingTrend > 0 ? 'fa-arrow-up' : 'fa-arrow-down'"></i>
              {{ Math.abs(stats.bookingTrend) }}% from last month
            </div>
          </div>
          <div class="card-icon">
            <i class="fas fa-calendar-check"></i>
          </div>
        </div>

        <div class="card">
          <div class="card-info">
            <h3>Revenue ({{ currentYear }})</h3>
            <div class="card-value">₱{{ formatNumber(stats.totalRevenue) }}</div>
            <div class="card-trend" :class="{ up: stats.revenueTrend > 0 }">
              <i class="fas" :class="stats.revenueTrend > 0 ? 'fa-arrow-up' : 'fa-arrow-down'"></i>
              {{ Math.abs(stats.revenueTrend) }}% from last month
            </div>
          </div>
          <div class="card-icon">
            <i class="fas fa-coins"></i>
          </div>
        </div>

        <div class="card">
          <div class="card-info">
            <h3>Active Users</h3>
            <div class="card-value">{{ stats.activeUsers }}</div>
            <div class="card-trend" :class="{ up: stats.userTrend > 0 }">
              <i class="fas" :class="stats.userTrend > 0 ? 'fa-arrow-up' : 'fa-arrow-down'"></i>
              {{ Math.abs(stats.userTrend) }}% from last month
            </div>
          </div>
          <div class="card-icon">
            <i class="fas fa-users"></i>
          </div>
        </div>

        <div class="card">
          <div class="card-info">
            <h3>Satisfaction Rate</h3>
            <div class="card-value">{{ satisfactionRate }}%</div>
            <div class="card-trend" :class="{ up: satisfactionTrend > 0 }">
              <i class="fas" :class="satisfactionTrend > 0 ? 'fa-arrow-up' : 'fa-arrow-down'"></i>
              {{ Math.abs(satisfactionTrend) }}% from last month
            </div>
          </div>
          <div class="card-icon">
            <i class="fas fa-smile"></i>
          </div>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="charts-section">
        <div class="chart-container">
          <h3>Revenue Overview ({{ currentYear }})</h3>
          <LineChart :data="revenueData" :options="chartOptions" />
        </div>
        <div class="chart-container">
          <h3>Bookings by Event Type </h3>
          <DoughnutChart :data="eventTypeData" :options="chartOptions" />
        </div>
      </div>

      <!-- Recent Bookings -->
      <div class="recent-bookings">
        <div class="section-header">
          <h3>Recent Bookings</h3>
          <a href="/admin/bookings" class="btn-view-all">View All</a>
        </div>
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Client</th>
                <th>Event Type</th>
                <th>Date</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="booking in recentBookings" :key="booking.id">
                <td>#{{ booking.id }}</td>
                <td>{{ booking.full_name }}</td>
                <td>{{ booking.package.package_type }}</td>
                <td>{{ formatDate(booking.event_date) }}</td>
                <td>
                  <span style="text-transform: capitalize;" :class="['status', booking.status.toLowerCase()]">
                    {{ booking.status }}
                  </span>
                </td>
                <td>₱{{ formatNumber(booking.package.package_price) }}</td>
                <td>
                  <button @click="viewBooking(booking)" class="btn-action">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button @click="updateStatus(booking)" class="btn-action">
                    <i class="fas fa-edit"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="quick-actions">
        <button @click="openAddGuest" v-if="user?.role === 'admin'" class="action-btn">
          <i class="fas fa-user"></i>
          <i class="fas fa-plus"></i>
          Add Pack
        </button>
        <button @click="openAddPackage" v-if="user?.role === 'admin'" class="action-btn">
          <i class="fas fa-plus"></i>
          Add Package
        </button>
        <button @click="openReports" class="action-btn">
          <i class="fas fa-file-alt"></i>
          Generate Report
        </button>
        <button @click="openSettings" class="action-btn">
          <i class="fas fa-cog"></i>
          Settings
        </button>
      </div>
    </main>

    <!-- Modals -->
    <BookingDetailsModal
      v-if="showBookingModal"
      :booking="selectedBooking"
      @close="showBookingModal = false"
    />

    <AddGuestModal
      v-if="showAddGuestModal"
      @close="showAddGuestModal = false"
      @success="handleGuestAdded"
    />

    <AddPackageModal
      v-if="showAddPackageModal"
      @close="showAddPackageModal = false"
      @success="handlePackageAdded"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Line as LineChart, Doughnut as DoughnutChart } from 'vue-chartjs';
import BookingDetailsModal from '@/components/bookings/BookingDetailsModal.vue';
import AddPackageModal from '@/components/admin/AddPackageModal.vue';
import AddGuestModal from '@/components/admin/AddGuestModal.vue';
import { useAuth } from '@/composables/useAuth';
import axios from 'axios';

const { token } = useAuth();
const stats = ref({
  totalBookings: 0,
  totalRevenue: '',
  activeUsers: 0,
  bookingTrend: 0,
  revenueTrend: 0,
  userTrend: 0
});

const satisfactionRate = ref(0);
const satisfactionTrend = ref(0);

const recentBookings = ref([]);
const currentYear = ref(new Date().getFullYear());
const user = ref(JSON.parse(localStorage.getItem('user_info')));
const showBookingModal = ref(false);
const showAddPackageModal = ref(false);
const selectedBooking = ref(null);
const showAddGuestModal = ref(false);

// Chart Data
const revenueData = ref({
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
  datasets: [{
    label: 'Revenue',
    data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
    borderColor: '#4CAF50',
    tension: 0.4
  }]
});

const eventTypeData = ref({
  labels: [],
  datasets: [{
    data: [],
    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
  }]
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false
};

const formatNumber = (num) => {
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-PH', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

const fetchBookings = async () => {
  const response = await axios.get('http://127.0.0.1:8000/api/get-all-bookings');

  stats.value.totalBookings = response.data.length;
  stats.value.totalRevenue = response.data.reduce((total, booking) => {
    if (booking.status == 'completed') {
      return total + parseFloat(booking.package.package_price);
    }
    return total;
  }, 0);

  recentBookings.value = response.data;
};

const fetchUser = async () => {
  const response = await axios.get('http://127.0.0.1:8000/api/get-all-users', {
    headers: {
      Authorization: `Bearer ${token.value}`
    }
  });
  stats.value.activeUsers = response.data.filter(user => user.status === 'active').length;
};

const fetchEventTypeStats = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/get-event-type-stats');
    
    eventTypeData.value = {
      labels: response.data.data.labels,
      datasets: [{
        data: response.data.data.values,
        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
      }]
    };

  } catch (error) {
    console.error('Error fetching event type stats:', error);
  }
};

const fetchMonthlyRevenue = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/get-monthly-revenue');
    revenueData.value = {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      datasets: [{
        label: 'Revenue',
        data: response.data.data,
        borderColor: '#4CAF50',
        tension: 0.4
      }]
    };
  } catch (error) {
    console.error('Error fetching monthly revenue:', error);
  }
};

const fetchSatisfactionRate = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/get-satisfaction-rate');
    if (response.data.status === 200) {
      satisfactionRate.value = response.data.data.satisfactionRate;
      satisfactionTrend.value = response.data.data.trend;
    }``
  } catch (error) {
    console.error('Error fetching satisfaction rate:', error);
  }
};

const viewBooking = (booking) => {
  selectedBooking.value = booking;
  showBookingModal.value = true;
};

const updateStatus = async (booking) => {
  // Implementation for updating booking status
};

const openAddPackage = () => {
  showAddPackageModal.value = true;
};

const handlePackageAdded = () => {
  showAddPackageModal.value = false;
  // Refresh dashboard data
  // fetchDashboardData();
};

const openAddGuest = () => {
  showAddGuestModal.value = true;
};

const handleGuestAdded = () => {
  showAddGuestModal.value = false;
  // fetchDashboardData();
};

onMounted(async () => {
  await fetchBookings();
  await fetchUser();
  await fetchEventTypeStats();
  await fetchMonthlyRevenue();
  await fetchSatisfactionRate();
});
</script>

<style scoped>
.admin-dashboard {
  display: flex;
  background: var(--background-color);
  position: relative; /* Add this */
}

.dashboard-content {
  flex: 1;
  padding: 2rem;
  transition: margin-left 0.3s ease; /* Add smooth transition */
}

.overview-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.card {
  background: var(--card-background);
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.card-info h3 {
  color: var(--text-color);
  font-size: 1rem;
  margin-bottom: 0.5rem;
}

.card-value {
  font-size: 1.8rem;
  font-weight: bold;
  color: var(--text-color);
  margin-bottom: 0.5rem;
}

.card-trend {
  font-size: 0.9rem;
  color: #f44336;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.card-trend.up {
  color: #4CAF50;
}

.card-icon {
  font-size: 2rem;
  color: var(--primary-color);
  opacity: 0.8;
}

.charts-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.chart-container {
  background: var(--card-background);
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  height: 300px;
}

.chart-container h3 {
  color: var(--text-color);
  margin-bottom: 1rem;
}

.recent-bookings {
  background: var(--card-background);
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.btn-view-all {
  padding: 0.5rem 1rem;
  background: var(--primary-color);
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.table-container {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #eee;
}

th {
  font-weight: 600;
  color: var(--text-color);
}

.status {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: 500;
}

.status.pending {
  background: #fff3cd;
  color: #856404;
}

.status.confirmed {
  background: #d4edda;
  color: #155724;
}

.status.cancelled {
  background: #f8d7da;
  color: #721c24;
}

.btn-action {
  background: none;
  border: none;
  color: var(--primary-color);
  cursor: pointer;
  padding: 0.25rem;
  margin: 0 0.25rem;
}

.quick-actions {
  display: flex;
  gap: 1rem;
}

.action-btn {
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

@media (max-width: 768px) {
  .dashboard-content {
    margin-left: 0;
    padding: 1rem;
  }

  .charts-section {
    grid-template-columns: 1fr;
  }

  .quick-actions {
    flex-direction: column;
  }

  .action-btn {
    width: 100%;
  }
}
</style> 