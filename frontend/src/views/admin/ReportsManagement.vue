<template>
  <div class="reports-management">
    <main class="management-content">
      <div class="page-header">
        <h1>Reports Management</h1>
        <div class="header-actions">
          <button class="generate-btn" @click="generateReport">
            <i class="fas fa-file-export"></i>
            Generate Report
          </button>
        </div>
      </div>

      <div class="reports-grid">
        <!-- Revenue Reports -->
        <div class="report-card">
          <div class="report-header">
            <h3>Revenue Overview</h3>
            <div class="period-selector">
              <button 
                v-for="period in periods" 
                :key="period"
                :class="['period-btn', { active: selectedPeriod === period }]"
                @click="selectedPeriod = period"
              >
                {{ period }}
              </button>
            </div>
          </div>
          
          <div class="chart-container">
            <canvas ref="revenueChart"></canvas>
          </div>

          <div class="stats-grid">
            <div class="stat-item">
              <span class="label">Total Revenue</span>
              <span class="value">₱150,000</span>
              <span class="trend up">
                <i class="fas fa-arrow-up"></i>
                12%
              </span>
            </div>
            <div class="stat-item">
              <span class="label">Average Order Value</span>
              <span class="value">₱25,000</span>
              <span class="trend up">
                <i class="fas fa-arrow-up"></i>
                8%
              </span>
            </div>
          </div>
        </div>

        <!-- Bookings Reports -->
        <div class="report-card">
          <div class="report-header">
            <h3>Bookings by Event Type</h3>
            <button class="export-btn">
              <i class="fas fa-download"></i>
              Export
            </button>
          </div>
          
          <div class="chart-container">
            <canvas ref="bookingsChart"></canvas>
          </div>

          <div class="stats-grid">
            <div class="stat-item">
              <span class="label">Total Bookings</span>
              <span class="value">245</span>
              <span class="trend up">
                <i class="fas fa-arrow-up"></i>
                15%
              </span>
            </div>
            <div class="stat-item">
              <span class="label">Completion Rate</span>
              <span class="value">92%</span>
              <span class="trend up">
                <i class="fas fa-arrow-up"></i>
                5%
              </span>
            </div>
          </div>
        </div>

        <!-- Customer Reports -->
        <div class="report-card">
          <div class="report-header">
            <h3>Customer Analytics</h3>
            <button class="export-btn">
              <i class="fas fa-download"></i>
              Export
            </button>
          </div>
          
          <div class="chart-container">
            <canvas ref="customerChart"></canvas>
          </div>

          <div class="stats-grid">
            <div class="stat-item">
              <span class="label">Active Users</span>
              <span class="value">1,250</span>
              <span class="trend up">
                <i class="fas fa-arrow-up"></i>
                18%
              </span>
            </div>
            <div class="stat-item">
              <span class="label">Customer Satisfaction</span>
              <span class="value">95%</span>
              <span class="trend up">
                <i class="fas fa-arrow-up"></i>
                3%
              </span>
            </div>
          </div>
        </div>

        <!-- Popular Packages -->
        <div class="report-card">
          <div class="report-header">
            <h3>Popular Packages</h3>
            <button class="export-btn">
              <i class="fas fa-download"></i>
              Export
            </button>
          </div>

          <div class="packages-list">
            <div class="package-item">
              <div class="package-info">
                <span class="package-name">Premium Wedding Package</span>
                <span class="event-type wedding">Wedding</span>
              </div>
              <div class="package-stats">
                <div class="stat">
                  <span class="label">Bookings</span>
                  <span class="value">45</span>
                </div>
                <div class="stat">
                  <span class="label">Revenue</span>
                  <span class="value">₱1.2M</span>
                </div>
                <div class="stat">
                  <span class="label">Rating</span>
                  <span class="value">4.8</span>
                </div>
              </div>
            </div>

            <div class="package-item">
              <div class="package-info">
                <span class="package-name">Deluxe Debut Package</span>
                <span class="event-type debut">Debut</span>
              </div>
              <div class="package-stats">
                <div class="stat">
                  <span class="label">Bookings</span>
                  <span class="value">38</span>
                </div>
                <div class="stat">
                  <span class="label">Revenue</span>
                  <span class="value">₱950K</span>
                </div>
                <div class="stat">
                  <span class="label">Rating</span>
                  <span class="value">4.7</span>
                </div>
              </div>
            </div>

            <div class="package-item">
              <div class="package-info">
                <span class="package-name">Basic Christening Package</span>
                <span class="event-type christening">Christening</span>
              </div>
              <div class="package-stats">
                <div class="stat">
                  <span class="label">Bookings</span>
                  <span class="value">52</span>
                </div>
                <div class="stat">
                  <span class="label">Revenue</span>
                  <span class="value">₱780K</span>
                </div>
                <div class="stat">
                  <span class="label">Rating</span>
                  <span class="value">4.6</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Report Generator Modal -->
      <div v-if="showGenerateModal" class="modal-overlay" @click.self="showGenerateModal = false">
        <div class="modal-content">
          <div class="modal-header">
            <h2>Generate Report</h2>
            <button class="close-btn" @click="showGenerateModal = false">
              <i class="fas fa-times"></i>
            </button>
          </div>

          <form @submit.prevent="handleGenerateReport" class="generate-form">
            <div class="form-group">
              <label for="reportType">Report Type</label>
              <select id="reportType" v-model="reportForm.type" required>
                <option value="revenue">Revenue Report</option>
                <option value="bookings">Bookings Report</option>
                <option value="customers">Customer Analytics</option>
                <option value="packages">Package Performance</option>
              </select>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="startDate">Start Date</label>
                <input
                  type="date"
                  id="startDate"
                  v-model="reportForm.startDate"
                  required
                />
              </div>

              <div class="form-group">
                <label for="endDate">End Date</label>
                <input
                  type="date"
                  id="endDate"
                  v-model="reportForm.endDate"
                  required
                />
              </div>
            </div>

            <div class="form-group">
              <label for="format">Export Format</label>
              <select id="format" v-model="reportForm.format" required>
                <option value="pdf">PDF</option>
                <option value="excel">Excel</option>
                <option value="csv">CSV</option>
              </select>
            </div>

            <div class="form-actions">
              <button type="button" class="cancel-btn" @click="showGenerateModal = false">
                Cancel
              </button>
              <button type="submit" class="submit-btn" :disabled="isGenerating">
                <i class="fas fa-spinner fa-spin" v-if="isGenerating"></i>
                {{ isGenerating ? 'Generating...' : 'Generate Report' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Chart from 'chart.js/auto';

const showGenerateModal = ref(false);
const isGenerating = ref(false);

const revenueChart = ref(null);
const bookingsChart = ref(null);
const customerChart = ref(null);

const reportForm = ref({
  type: 'revenue',
  startDate: '',
  endDate: '',
  format: 'pdf'
});

onMounted(() => {
  // Revenue Chart
  new Chart(revenueChart.value, {
    type: 'line',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
      datasets: [{
        label: 'Revenue',
        data: [65000, 85000, 75000, 95000, 120000, 150000],
        borderColor: '#4CAF50',
        tension: 0.4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false
    }
  });

  // Bookings Chart
  new Chart(bookingsChart.value, {
    type: 'doughnut',
    data: {
      labels: ['Wedding', 'Debut', 'Christening', 'Kiddie Party'],
      datasets: [{
        data: [45, 25, 20, 10],
        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false
    }
  });

  // Customer Chart
  new Chart(customerChart.value, {
    type: 'bar',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
      datasets: [{
        label: 'New Users',
        data: [120, 150, 180, 220, 280, 350],
        backgroundColor: '#4CAF50'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false
    }
  });
});
</script>

<style scoped>
/* ... (keep existing styles) ... */
</style>

<style scoped>
.reports-management {
  display: flex;
  min-height: 100vh;
  background: var(--background-color);
}

.management-content {
  flex: 1;
  padding: 2rem;
  margin-left: 250px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 1.8rem;
  color: var(--text-color);
}

.generate-btn {
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

.reports-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.report-card {
  background: var(--card-background);
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.report-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.report-header h3 {
  font-size: 1.2rem;
  color: var(--text-color);
}

.period-selector {
  display: flex;
  gap: 0.5rem;
}

.period-btn {
  padding: 0.5rem 1rem;
  background: none;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  color: var(--text-color);
  cursor: pointer;
}

.period-btn.active {
  background: var(--primary-color);
  color: white;
  border-color: var(--primary-color);
}

.export-btn {
  padding: 0.5rem 1rem;
  background: none;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  color: var(--text-color);
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.chart-container {
  height: 300px;
  margin-bottom: 1.5rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.stat-item {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.stat-item .label {
  font-size: 0.9rem;
  color: var(--text-muted);
}

.stat-item .value {
  font-size: 1.5rem;
  font-weight: bold;
  color: var(--text-color);
}

.trend {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.9rem;
  color: #f44336;
}

.trend.up {
  color: #4CAF50;
}

.packages-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.package-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background: var(--background-color);
  border-radius: 6px;
}

.package-info {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.package-name {
  font-weight: 500;
  color: var(--text-color);
}

.event-type {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: 500;
}

.event-type.wedding {
  background: #e8f5e9;
  color: #2e7d32;
}

.event-type.debut {
  background: #fff3e0;
  color: #ef6c00;
}

.event-type.christening {
  background: #e3f2fd;
  color: #1565c0;
}

.event-type.kiddie {
  background: #f3e5f5;
  color: #7b1fa2;
}

.package-stats {
  display: flex;
  gap: 2rem;
}

.package-stats .stat {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.25rem;
}

.package-stats .label {
  font-size: 0.8rem;
  color: var(--text-muted);
}

.package-stats .value {
  font-weight: 500;
  color: var(--text-color);
}

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
  background: var(--modal-background);
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
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
}

.generate-form {
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

.form-row {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

input, select {
  padding: 0.75rem;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  background: var(--input-background);
  color: var(--text-color);
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
  .management-content {
    margin-left: 0;
    padding: 1rem;
  }

  .reports-grid {
    grid-template-columns: 1fr;
  }

  .package-item {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }

  .package-stats {
    width: 100%;
    justify-content: space-around;
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
</style> 