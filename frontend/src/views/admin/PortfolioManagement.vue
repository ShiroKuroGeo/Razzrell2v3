<template>
  <div class="portfolios-management">
    <main class="management-content">
      
      <div class="page-header">
        <h1>Portfolios Management</h1>
        <div class="header-actions">
          <div class="search-bar">
            <i class="fas fa-search"></i>
            <input 
              type="text" 
              v-model="searchQuery" 
              placeholder="Search portfolios..."
              @input="handleSearch"
            />
          </div>
          <div class="filters">
            <button class="add-portfolio-btn" @click="openAddportfolioModal">
              <i class="fas fa-plus"></i>
              Add Portfolio
            </button>
          </div>
        </div>
      </div>

      <div class="portfolios-grid">
        <div v-for="portfolioItem in filteredportfolios" :key="portfolioItem.id" class="portfolio-card">
          <div class="portfolio-image">
            <img :src="getImageUrl(portfolioItem.image)" :alt="portfolioItem.portfolio_name" />
            <div class="portfolio-actions">
              <button @click="editportfolio(portfolioItem)" class="action-btn edit">
                <i class="fas fa-edit"></i>
              </button>
              <button @click="deleteportfolio(portfolioItem.id)" class="action-btn delete">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
          
          <div class="portfolio-content">
            <div class="portfolio-header">
              <h3>{{ portfolioItem.title }}</h3>
            </div>
            <p class="portfolio-description">{{ portfolioItem.description }}</p>
          </div>
        </div>
      </div>

      <div v-if="filteredportfolios.length === 0" class="no-results">
        <i class="fas fa-box-open"></i>
        <p>No portfolios founds in {{ portfolio_typeFilter }}</p>
      </div>
    </main>

    <!-- Modals -->
    <AddportfolioModal
      v-if="showAddModal"
      @close="showAddModal = false"
      @success="handleportfolioAdded"
    />

    <!-- <EditportfolioModal
      v-if="showEditModal"
      :portfolio="selectedportfolio"
      @close="showEditModal = false"
      @update="handleportfolioUpdated"
    /> -->
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useAuth } from '@/composables/useAuth';
import AdminSidebar from '@/components/admin/AdminSidebar.vue';
import AddportfolioModal from '@/components/admin/AddportfolioModal.vue';
// import EditportfolioModal from '@/components/admin/EditportfolioModal.vue';
import ConfirmationModal from '@/components/ui/ConfirmationModal.vue';
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  name: 'portfoliosManagement',
  components: {
    AdminSidebar,
    AddportfolioModal,
    // EditportfolioModal,
    ConfirmationModal
  },
  setup() {
    const { token } = useAuth();

    const portfolios = ref([]);
    const searchQuery = ref('');
    const portfolio_typeFilter = ref('');
    const showAddModal = ref(false);
    const showEditModal = ref(false);
    const showDeleteModal = ref(false);
    const selectedportfolio = ref(null); 

    const filteredportfolios = computed(() => {
      return portfolios.value.filter(portfolioItem => {
        const matchesSearch = !searchQuery.value || 
          portfolioItem.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          portfolioItem.description.toLowerCase().includes(searchQuery.value.toLowerCase());
        
        const matchesportfolio_type = !portfolio_typeFilter.value || 
          portfolioItem.type.toLowerCase() === portfolio_typeFilter.value.toLowerCase();
        
        return matchesSearch && matchesportfolio_type;
      });
    });

    const fetchportfolios = async () => {
      const response = await axios.get(`${import.meta.env.VITE_API_URL}/api/get-all-portfolios`);
      portfolios.value = response.data;
    };

    const handleSearch = () => {
      portfolio_typeFilter.value = '';
    };

    const formatNumber = (num) => {
      return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    };

    const openAddportfolioModal = () => {
      showAddModal.value = true;
    };

    const editportfolio = (pkg) => {
      selectedportfolio.value = pkg;
      showEditModal.value = true;
    };

    const deleteportfolio = async (pkg) => {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Okay',
        cancelButtonText: 'Close'
      }).then(async (result) => {
        if (result.isConfirmed) {
          const response = await axios.post(`${import.meta.env.VITE_API_URL}/api/delete-portfolio/${pkg}`);
          if(response.status === 200){
            Swal.fire('Deleted!', 'Portfolio has been deleted.', 'success').then(() => {
              window.location.reload();
            });
          } else {
            Swal.fire('Error!', 'Something went wrong.', 'error');
          }
        }
      });
    };

    const confirmDeleteportfolio = async () => {
      try {
        const response = await axios.post(`${import.meta.env.VITE_API_URL}/api/delete-portfolio/${selectedportfolio.value.id}`);
        if(response.status === 200){
          Swal.fire({
            title: 'Success',
            text: response.data.message,
            icon: 'success'
          });
          await fetchportfolios();
          showDeleteModal.value = false;
        }else{
          Swal.fire({
            title: 'Error',
            text: response.data.message,
            icon: 'error'
          });
          await fetchportfolios();
          showDeleteModal.value = false;
        }
      } catch (error) {
        console.error('Error deleting portfolio:', error);
      }
    };

    const handleportfolioAdded = async () => {
      await fetchportfolios();
      showAddModal.value = false;
    };

    const handleportfolioUpdated = async () => {
      await fetchportfolios();
      showEditModal.value = false;
    };

    const defaultImageUrl = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDIwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjIwMCIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNFNUU3RUIiLz48cGF0aCBkPSJNODAgOTBIMTIwVjExMEg4MFY5MFoiIGZpbGw9IiM5Q0EzQUYiLz48cGF0aCBkPSJNNjUgNjBIMTM1VjgwSDY1VjYwWiIgZmlsbD0iIzlDQTNBRiIvPjwvc3ZnPg==';

    const getImageUrl = (imagePath) => {
      if (imagePath) {
        return `${import.meta.env.VITE_API_URL}/storage/${imagePath}`;
      } else {
        return defaultImageUrl;
      }
    };

    onMounted(async () => {
      await fetchportfolios();
    });

    return {
      portfolios,
      searchQuery,
      portfolio_typeFilter,
      showDeleteModal,
      selectedportfolio,
      filteredportfolios,
      handleSearch,
      deleteportfolio,
      formatNumber,
      openAddportfolioModal,
      confirmDeleteportfolio,
      handleportfolioAdded,
      handleportfolioUpdated,
      showAddModal,
      getImageUrl
    };
  }
};
</script>

<style scoped>
.portfolios-management {
  display: flex;
  min-height: 100vh;
  background: var(--background);
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
  color: var(--dark);
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

.search-bar input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border: 1px solid var(--light);
  border-radius: 0.5rem;
  background: var(--white);
}

.search-bar i {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--info-dark);
}

.filters {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.filters select {
  padding: 0.75rem;
  border: 1px solid var(--light);
  border-radius: 0.5rem;
  background: var(--white);
  min-width: 150px;
}

.add-portfolio-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: var(--primary);
  color: var(--white);
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.add-portfolio-btn:hover {
  background: var(--primary-dark);
}

.portfolios-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
  margin-top: 2rem;
}

.portfolio-card {
  background: var(--white);
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: var(--box-shadow);
  transition: transform 0.2s;
}

.portfolio-card:hover {
  transform: translateY(-5px);
}

.portfolio-image {
  position: relative;
  height: 200px;
}

.portfolio-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.portfolio-actions {
  position: absolute;
  top: 1rem;
  right: 1rem;
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 50%;
  border: none;
  background: var(--white);
  color: var(--dark);
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.action-btn.edit:hover {
  background: var(--primary);
  color: var(--white);
}

.action-btn.delete:hover {
  background: var(--danger);
  color: var(--white);
}

.portfolio-content {
  padding: 1.5rem;
}

.portfolio-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.portfolio-header h3 {
  font-size: 1.2rem;
  color: var(--dark);
}

.event-type {
  padding: 0.25rem 0.75rem;
  border-radius: 1rem;
  font-size: 0.875rem;
  font-weight: 500;
}

.event-type.wedding {
  background: #FFE2EC;
  color: #FF4081;
}

.event-type.debut {
  background: #E3F2FD;
  color: #2196F3;
}

.event-type.christening {
  background: #E8F5E9;
  color: #4CAF50;
}

.event-type.party {
  background: #FFF3E0;
  color: #FF9800;
}

.portfolio-price {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--primary);
  margin-bottom: 1rem;
}

.portfolio-description {
  color: var(--info-dark);
  margin-bottom: 1rem;
  line-height: 1.5;
}

.portfolio-inclusions {
  margin-bottom: 1rem;
}

.portfolio-inclusions h4 {
  font-size: 1rem;
  color: var(--dark);
  margin-bottom: 0.5rem;
}

.portfolio-inclusions ul {
  list-style: none;
  padding-left: 0;
}

.portfolio-inclusions li {
  color: var(--info-dark);
  padding: 0.25rem 0;
  display: flex;
  align-items: center;
}

.portfolio-inclusions li::before {
  content: "•";
  color: var(--primary);
  font-weight: bold;
  margin-right: 0.5rem;
}

.portfolio-stats {
  display: flex;
  gap: 1rem;
  padding-top: 1rem;
  border-top: 1px solid var(--light);
}

.stat {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--info-dark);
}

.stat i {
  color: var(--primary);
}

.no-results {
  text-align: center;
  padding: 3rem;
  color: var(--info-dark);
}

.no-results i {
  font-size: 3rem;
  margin-bottom: 1rem;
}

@media (max-width: 768px) {
  .management-content {
    margin-left: 0;
    padding: 1rem;
  }

  .header-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .search-bar {
    width: 100%;
  }

  .filters {
    flex-direction: column;
    width: 100%;
  }

  .filters select {
    width: 100%;
  }

  .add-portfolio-btn {
    width: 100%;
    justify-content: center;
  }
}
</style> 