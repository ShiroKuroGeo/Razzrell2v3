<template>
  <div class="users-management">
    
    <main class="management-content">
      <div class="page-header">
        <h1>Contact Us Management</h1>
        <div class="header-actions">
          <div class="search-bar">
            <i class="fas fa-search"></i>
            <input 
              type="text" 
              v-model="searchQuery" 
              placeholder="Search users..."
              @input="handleSearch"
            />
          </div>
        </div>
      </div>

      <div class="users-table-container">
        <table class="users-table">
          <thead>
            <tr>
              <th>
                <div class="th-content">
                  ID
                  <i class="fas"></i>
                </div>
              </th>
              <th>User Information</th>
              <th>Message</th>
              <th>
                <div class="th-content" >
                  Reasonable Type
                  <i class="fas fa-sort"></i>
                </div>
              </th>
              <th>
                <div class="th-content" >
                  Date Created
                  <i class="fas fa-sort"></i>
                </div>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in filteredUsers" :key="user.id">
              <td>#{{ user.id }}</td>
              <td>
                <div class="user-info">
                  <div style="text-transform: capitalize;">
                    <span class="user-name">{{ user.first_name }} {{ user.last_name }}</span>
                    <span class="user-email">{{ user.email }}</span>
                    <span class="user-email">{{ user.phone }}</span>
                  </div>
                </div>
              </td>
              <td>
                <span class="status-badge" style="text-transform: capitalize;" :class="user.flag === '1' ? 'banned' : user.event_type">
                  {{ showFullMessage[user.id] ? user.message : (user.message.length > 50 ? user.message.slice(0, 50) + '...' : user.message) }}
                  <button v-if="user.message.length > 50" 
                          @click="toggleMessage(user.id)"
                          class="view-more-btn">
                    {{ showFullMessage[user.id] ? 'View Less' : 'View More' }}
                  </button>
                </span>
              </td>
              <td>
                <span class="status-badge" style="text-transform: capitalize;" :class="user.flag === '1' ? 'banned' : user.event_type">
                  {{ user.flag === '1' ? 'Removed!' : user.event_type }}
                </span>
              </td>
              <td>
                <div class="last-login">
                  <span class="date">{{ formatDate(user.updated_at) }}</span>
                  <span class="time">{{ formatTime(user.updated_at) }}</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="pagination">
        <button 
          :disabled="currentPage === 1" 
          @click="changePage(currentPage - 1)"
          class="page-btn"
        >
          <i class="fas fa-chevron-left"></i>
        </button>
        <span class="page-info">
          Page {{ currentPage }} of {{ totalPages }}
        </span>
        <button 
          :disabled="currentPage === totalPages" 
          @click="changePage(currentPage + 1)"
          class="page-btn"
        >
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </main>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuth } from '@/composables/useAuth';
import AdminSidebar from '@/components/admin/AdminSidebar.vue';
import AddUserModal from '@/components/admin/AddUserModal.vue';
import EditUserModal from '@/components/admin/EditUserModal.vue';
import UserDetailsModal from '@/components/admin/UserDetailsModal.vue';
import ConfirmationModal from '@/components/ui/ConfirmationModal.vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const { token } = useAuth();

// State
const contactUsData = ref([]);
const searchQuery = ref('');
const currentPage = ref(1);
const showFullMessage = ref({});

// Computed
const filteredUsers = computed(() => {
  return contactUsData.value.filter(contactus => {
    const matchesSearch = searchQuery.value === '' || 
      contactus.first_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      contactus.last_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      contactus.email.toLowerCase().includes(searchQuery.value.toLowerCase());
    
    return matchesSearch;
  });
});

const totalPages = computed(() => {
  return Math.ceil(filteredUsers.value.length / 5);
});

const fetchContactUs = async () => {
  const response = await axios.get(`${import.meta.env.VITE_API_URL}/api/get-all-contact-us`);
  contactUsData.value = response.data;
  console.log(response.data);
};

const handleSearch = () => {
  currentPage.value = 1;
};

const changePage = (page) => {
  currentPage.value = page;
};

const toggleMessage = (id) => {
  showFullMessage.value[id] = !showFullMessage.value[id];
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-PH', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

const formatTime = (date) => {
  return new Date(date).toLocaleTimeString('en-PH', {
    hour: '2-digit',
    minute: '2-digit'
  });
};


onMounted(async () => {
  await fetchContactUs();
});
</script>

<style scoped>
.users-management {
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
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 1.8rem;
  color: var(--text-color);
  margin-bottom: 1rem;
}

.header-actions {
  display: flex;
  gap: 1rem;
  align-items: center;
  flex-wrap: wrap;
}

.search-bar {
  position: relative;
  flex: 1;
  min-width: 200px;
}

.search-bar i {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-muted);
}

.search-bar input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  font-size: 1rem;
  background: var(--input-background);
  color: var(--text-color);
}

.filters {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.filters select {
  padding: 0.75rem;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  background: var(--input-background);
  color: var(--text-color);
}

.add-user-btn {
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

.users-table-container {
  background: var(--card-background);
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow-x: auto;
  margin-bottom: 2rem;
}

.users-table {
  width: 100%;
  border-collapse: collapse;
}

.th-content {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

th, td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid var(--border-color);
}

th {
  font-weight: 600;
  color: var(--text-color);
  background: var(--table-header-background, rgba(0, 0, 0, 0.02));
}

.user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.user-info img {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
}

.user-info div {
  display: flex;
  flex-direction: column;
}

.user-email {
  font-size: 0.9rem;
  color: var(--text-muted);
}

.role-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: 500;
}

.role-badge.admin {
  background: #e3f2fd;
  color: #1565c0;
}

.role-badge.staff {
  background: #e8f5e9;
  color: #2e7d32;
}

.role-badge.user {
  background: #f3e5f5;
  color: #7b1fa2;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: 500;
}

.status-badge.active {
  background: #d4edda;
  color: #155724;
}

.status-badge.inactive {
  background: #fff3cd;
  color: #856404;
}

.status-badge.banned {
  background: #ff0000;
  color: white;
}

.status-badge.blocked {
  background: #f8d7da;
  color: #721c24;
}

.last-login {
  display: flex;
  flex-direction: column;
}

.last-login .time {
  font-size: 0.9rem;
  color: var(--text-muted);
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
}

.btn-action {
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 6px;
  color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-action.view {
  background: var(--primary-color);
}

.btn-action.edit {
  background: var(--warning-color, #ffc107);
}

.btn-action.block {
  background: var(--danger-color, #dc3545);
}

.btn-action.unblock {
  background: var(--success-color, #28a745);
}

.btn-action.delete {
  background: var(--danger-color, #dc3545);
}

.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
}

.page-btn {
  padding: 0.5rem 1rem;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  background: var(--card-background);
  color: var(--text-color);
  cursor: pointer;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  color: var(--text-color);
}

@media (max-width: 768px) {
  .management-content {
    margin-left: 0;
    padding: 1rem;
  }

  .header-actions {
    flex-direction: column;
  }

  .search-bar {
    width: 100%;
  }

  .filters {
    width: 100%;
    flex-direction: column;
  }

  .filters select {
    width: 100%;
  }

  .add-user-btn {
    width: 100%;
  }
}
</style> 