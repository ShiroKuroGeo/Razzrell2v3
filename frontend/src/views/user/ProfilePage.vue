<template>
  <MainLayout>
    <div class="profile-page">
      <div class="page-header">
        <h1>Profile Settings</h1>
        <div class="input-group">
          <i class="fas fa-lock"></i>
          <button type="button" class="save-btn" @click="saveChanges">
            <i class="fas fa-save"></i>
            Save Changes
          </button>
        </div>
      </div>

      <div class="profile-grid">
        <!-- Personal Information -->
        <div class="profile-card">
          <div class="card-header">
            <h2>Personal Information</h2>
          </div>

            <div class="form-row">
              <div class="form-group">
                <label for="firstName">First Name</label>
                <div class="input-group">
                  <i class="fas fa-user"></i>
                  <input
                    type="text"
                    id="firstName"
                    v-model="values.firstName"
                    @blur="handleBlur('firstName')"
                    :class="{ 'error': getFieldError('firstName') }"
                    placeholder="Enter first name"
                  />
                </div>
                <span class="error-message" v-if="getFieldError('firstName')">
                  {{ getFieldError('firstName') }}
                </span>
              </div>

              <div class="form-group">
                <label for="lastName">Last Name</label>
                <div class="input-group">
                  <i class="fas fa-user"></i>
                  <input
                    type="text"
                    id="lastName"
                    v-model="values.lastName"
                    @blur="handleBlur('lastName')"
                    :class="{ 'error': getFieldError('lastName') }"
                    placeholder="Enter last name"
                  />
                </div>
                <span class="error-message" v-if="getFieldError('lastName')">
                  {{ getFieldError('lastName') }}
                </span>
              </div>
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input
                  type="email"
                  id="email"
                  v-model="values.email"
                  @blur="handleBlur('email')"
                  :class="{ 'error': getFieldError('email') }"
                  placeholder="Enter email"
                />
              </div>
              <span class="error-message" v-if="getFieldError('email')">
                {{ getFieldError('email') }}
              </span>
            </div>

            <div class="form-group">
              <label for="phone">Phone Number</label>
              <div class="input-group">
                <i class="fas fa-phone"></i>
                <input
                  type="tel"
                  id="phone"
                  v-model="values.phone"
                  @blur="handleBlur('phone')"
                  :class="{ 'error': getFieldError('phone') }"
                  placeholder="Enter phone number"
                />
              </div>
              <span class="error-message" v-if="getFieldError('phone')">
                {{ getFieldError('phone') }}
              </span>
            </div>
        </div>

        <!-- Preferences -->
        <div class="profile-card">
          <div class="card-header">
            <h2>Preferences</h2>
          </div>

          <div class="preferences-form">
            <div class="form-group">
              <label>Theme</label>
              <div class="theme-toggle">
                <button
                  class="theme-btn"
                  :class="{ active: !isDarkMode }"
                  @click="setTheme('light')"
                >
                  <i class="fas fa-sun"></i>
                  Light
                </button>
                <button
                  class="theme-btn"
                  :class="{ active: isDarkMode }"
                  @click="setTheme('dark')"
                >
                  <i class="fas fa-moon"></i>
                  Dark
                </button>
              </div>
            </div>

            <div class="form-group">
              <label>Email Notifications</label>
              <div class="notification-settings">
                <label class="checkbox-label">
                  <input
                    type="checkbox"
                    v-model="values.notifications.bookingUpdates"
                  />
                  Booking updates
                </label>
                <label class="checkbox-label">
                  <input
                    type="checkbox"
                    v-model="values.notifications.reminders"
                  />
                  Event reminders
                </label>
                <label class="checkbox-label">
                  <input
                    type="checkbox"
                    v-model="values.notifications.promotions"
                  />
                  Promotions and news
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Security -->
        <div class="profile-card">
          <div class="card-header">
            <h2>Security</h2>
          </div>

          <div class="security-settings">
            <div class="security-item">
              <div class="security-info">
                <h3>{{ showChangePassword ? 'Security' : 'Password' }}</h3>
                <p>{{ showChangePassword ? '' : 'Last Changed Password ' + lastChangePassword }} </p>
              </div>
              <button class="action-btn" @click="showChangePassword ? showChangePassword = false : showChangePassword = true">
                {{ showChangePassword ? 'Go to Change Password' : 'Go to Security' }}
              </button>
            </div>

            <div class="security-item" v-if="showChangePassword == false">
              <div class="security-info">
                <h3>New Password</h3>
              </div><br>
              <div class="input-group">
                <i class="fas fa-lock"></i>
                <input
                  type="password"
                  id="password"
                  v-model="values.password"
                  @blur="handleBlur('password')"
                  :class="{ 'error': getFieldError('password') }"
                  placeholder="Enter new password"
                />
              </div>
              <span class="error-message" v-if="getFieldError('password')">
                {{ getFieldError('password') }}
              </span>
            </div>

            <div class="security-item" v-if="showChangePassword == false">
              <div class="security-info">
                <h3>Confirm Password</h3>
              </div><br>
              <div class="input-group">
                <i class="fas fa-lock"></i>
                <input
                  type="password"
                  id="cpassword"
                  v-model="values.cpassword"
                  @blur="handleBlur('cpassword')"
                  :class="{ 'error': getFieldError('cpassword') }"
                  placeholder="Enter new password"
                />
              </div>
              <span class="error-message" v-if="getFieldError('password')">
                {{ getFieldError('password') }}
              </span>
            </div>

            <div class="security-item" v-if="showChangePassword == true">
              <div class="security-info">
                <h3>Two-Factor Authentication</h3>
                <p>{{ values.twoFactorEnabled ? 'Enabled' : 'Disabled' }}</p>
              </div>
              <button
                class="action-btn"
                :class="{ 'btn-danger': values.twoFactorEnabled }"
                @click="toggleTwoFactor"
              >
                {{ values.twoFactorEnabled ? 'Disable' : 'Enable' }}
              </button>
            </div>

            <div class="security-item" v-if="showChangePassword == true">
              <div class="security-info">
                <h3>Active Sessions</h3>
                <p>{{ activeSessions.length }} devices</p>
              </div>
              <button class="action-btn btn-danger" @click="showActiveSessions">
                Manage Sessionss
              </button>
            </div>
          </div>
        </div>

        <!-- Account -->
        <div class="profile-card danger-zone">
          <div class="card-header">
            <h2>Account</h2>
          </div>

          <div class="account-actions">
            <div class="action-item">
              <div class="action-info">
                <h3>Export Data</h3>
                <p>Download a copy of your personal data</p>
              </div>
              <button class="action-btn" @click="exportData">
                Export Data
              </button>
            </div>

            <div class="action-item">
              <div class="action-info">
                <h3>Delete Account</h3>
                <p>Permanently delete your account and all data</p>
              </div>
              <button class="action-btn btn-danger" @click="confirmDeleteAccount">
                Delete Account
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Change Password Modal -->
    <!-- <ChangePassword
      v-if="showChangePassword"
      @close="showChangePassword = false"
      @success="handlePasswordChanged"
    /> -->

    <!-- Active Sessions Modal -->
    <ActiveSessionsModal
      v-if="showSessionsModal"
      :sessions="activeSessions"
      @close="showSessionsModal = false"
      @revoke="revokeSession"
    />
  </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import MainLayout from '@/components/ui/MainLayout.vue';
import ChangePassword from '@/components/user/ChangePassword.vue';
import ActiveSessionsModal from '@/components/user/ActiveSessionsModal.vue';
import { useAuth } from '@/composables/useAuth';
import { useValidation } from '@/composables/useValidation';
import { useNotifications } from '@/composables/useNotifications';
import { useLoading } from '@/composables/useLoading';
import { useConfirmation } from '@/composables/useConfirmation';
import { useTheme } from '@/composables/useTheme';
import { useApi } from '@/composables/useApi';
import axios from 'axios';
import Swal from 'sweetalert2'; // Import Swal if not already imported

const router = useRouter();
// const userInfo = localStorage.getItem('user_info');
const { user, updateProfile, logout } = useAuth();
const { success, error: showError } = useNotifications();
const { isLoading, withLoading } = useLoading();
const { confirm } = useConfirmation();
const { currentTheme, setTheme, isDarkMode } = useTheme();
const { token } = useAuth();
const { users } = useApi();

const showChangePassword = ref(false);
const showSessionsModal = ref(false);
const activeSessions = ref([]);
const lastChangePassword = ref(null);

const {
  values,
  validateAll,
  handleBlur,
  isValid,
  getFieldError,
} = useValidation({
  id: "",
  firstName: "",
  lastName: "",
  email: "",
  phone: "",
  email_verified_at: "",
  role: "",
  status: "",
  flag: "",
  password: '',
  confirmPassword: '',
  cpassword: '',
  notifications: {
    bookingUpdates: true,
    reminders: true,
    promotions: false,
  },
  twoFactorEnabled: false,
});

const validationRules = {
  firstName: ['required', ['minLength', 2]],
  lastName: ['required', ['minLength', 2]],
  email: ['required', 'email'],
  phone: ['required', 'phone'],
};

const lastPasswordChange = computed(() => {
  if (!user.value?.lastPasswordChange) return 'Never';
  return new Date(user.value.lastPasswordChange).toLocaleDateString();
});

const saveChanges = async () => {
  try {
    if (!token.value) {
      throw new Error('No authentication token found');
    }
    const formData = new FormData();
    formData.append('firstName', values.value.firstName);
    formData.append('lastName', values.value.lastName);
    formData.append('email', values.value.email);
    formData.append('phone', values.value.phone);
    formData.append('role', 'user');
    formData.append('status', 'active');
    formData.append('flag', '0');
    formData.append('id', values.value.id);
    if (values.value.password) {
      formData.append('password', values.value.password);
    }

    const response = await axios.post(`http://127.0.0.1:8000/api/update-user-info`, formData, {
      headers: {
        'Authorization': `Bearer ${token.value}`,
        'Content-Type': 'multipart/form-data'
      }
    });

    if(response.status === 200) {
      Swal.fire({
        title: 'Success', 
        text: response.data.message + '. You are about to logout.',
        icon: 'success',
        showConfirmButton: false,
        timer: 2000
      }).then(async () => {
        await logout();
        router.push('/login');
      });
    } else {
      throw new Error(response.data.message || 'Failed to update user');
    }

  } catch (error) {
    console.error('Error updating user:', error);
    Swal.fire({
      title: 'Error',
      text: error.response?.data?.message || 'Failed to update user information',
      icon: 'error'
    });
  }
};

const toggleTwoFactor = async () => {
  try {
    const confirmed = await confirm({
      title: values.value.twoFactorEnabled ? 'Disable 2FA' : 'Enable 2FA',
      message: values.value.twoFactorEnabled
        ? 'Are you sure you want to disable two-factor authentication? This will make your account less secure.'
        : 'Enable two-factor authentication to add an extra layer of security to your account.',
      confirmText: values.value.twoFactorEnabled ? 'Disable' : 'Enable',
      type: values.value.twoFactorEnabled ? 'danger' : 'warning',
    });

    if (confirmed) {
      await withLoading('toggle-2fa', () =>
        users.toggleTwoFactor(!values.value.twoFactorEnabled)
      );

      values.value.twoFactorEnabled = !values.value.twoFactorEnabled;
      success(
        `Two-factor authentication has been ${
          values.value.twoFactorEnabled ? 'enabled' : 'disabled'
        }`
      );
    }
  } catch (err) {
    showError('Failed to update two-factor authentication');
  }
};

const showActiveSessions = async () => {
  try {
    // const sessions = await withLoading('fetch-sessions', () =>
    //   users.getSessions()
    // );
    // activeSessions.value = sessions;
    // showSessionsModal.value = true;
  } catch (err) {
    showError('Failed to fetch active sessions');
  }
};

const revokeSession = async (sessionId) => {
  try {
    await withLoading(`revoke-session-${sessionId}`, () =>
      users.revokeSession(sessionId)
    );
    activeSessions.value = activeSessions.value.filter(
      (session) => session.id !== sessionId
    );
    success('Session revoked successfully');
  } catch (err) {
    showError('Failed to revoke session');
  }
};

const exportData = async () => {
  try {
    const data = await withLoading('export-data', () => users.exportData());
    const blob = new Blob([JSON.stringify(data, null, 2)], {
      type: 'application/json',
    });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'my-data.json';
    a.click();
    window.URL.revokeObjectURL(url);
    success('Data exported successfully');
  } catch (err) {
    showError('Failed to export data');
  }
};

const confirmDeleteAccount = async () => {
  try {
    const confirmed = await confirm({
      title: 'Delete Account',
      message:
        'Are you sure you want to delete your account? This action cannot be undone and all your data will be permanently deleted.',
      confirmText: 'Delete Account',
      type: 'danger',
    });

    if (confirmed) {
      await withLoading('delete-account', () => users.deleteAccount());
      success('Account deleted successfully');
      router.push('/login');
    }
  } catch (err) {
    showError('Failed to delete account');
  }
};

const handlePasswordChanged = () => {
  showChangePassword.value = false;
  success('Password changed successfully');
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-PH', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

const takeUserInfo = () =>{
  const user = JSON.parse(localStorage.getItem('user_info'));
  values.value.firstName = user.first_name;
  values.value.lastName = user.last_name;
  values.value.email = user.email;
  values.value.phone = user.phone_number;
  values.value.id = user.id;
  lastChangePassword.value = formatDate(user.updated_at);
};

onMounted(async () => {
  takeUserInfo();
  // try {
  //   const sessions = await users.getSessions();
  //   activeSessions.value = sessions;
  // } catch (err) {
  //   console.error('Failed to fetch initial sessions:', err);
  // }
});
</script>

<style scoped>
.profile-page {
  margin-top: 2rem;
  padding: 2rem;
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

.save-btn {
  padding: 0.75rem 1.5rem;
  background: var(--primary-color);
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: background-color 0.2s;
}

.save-btn:hover:not(:disabled) {
  background: var(--primary-hover);
}

.save-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.profile-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 1.5rem;
}

.profile-card {
  background: var(--card-background);
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: var(--shadow-sm);
}

.card-header {
  margin-bottom: 1.5rem;
}

.card-header h2 {
  font-size: 1.2rem;
  color: var(--text-color);
}

.profile-form,
.preferences-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
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
}

.form-group label {
  font-weight: 500;
  color: var(--text-color);
}

.input-group {
  position: relative;
  display: flex;
  align-items: center;
}

.input-group i {
  position: absolute;
  left: 1rem;
  color: var(--text-muted);
}

.input-group input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  font-size: 1rem;
  background: var(--input-background);
  color: var(--text-color);
  transition: border-color 0.2s;
}

.input-group input:focus {
  outline: none;
  border-color: var(--primary-color);
}

.input-group input.error {
  border-color: var(--error-color);
}

.error-message {
  font-size: 0.875rem;
  color: var(--error-color);
}

.theme-toggle {
  display: flex;
  gap: 0.5rem;
}

.theme-btn {
  flex: 1;
  padding: 0.75rem;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  background: var(--input-background);
  color: var(--text-color);
  font-size: 1rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  transition: all 0.2s;
}

.theme-btn.active {
  background: var(--primary-color);
  color: white;
  border-color: var(--primary-color);
}

.notification-settings {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--text-color);
  cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
  width: 16px;
  height: 16px;
}

.language-select,
.timezone-select {
  padding: 0.75rem;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  font-size: 1rem;
  background: var(--input-background);
  color: var(--text-color);
  width: 100%;
}

.security-settings,
.account-actions {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.security-item,
.action-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  border: 1px solid var(--border-color);
  border-radius: 6px;
}

.security-info h3,
.action-info h3 {
  font-size: 1rem;
  color: var(--text-color);
  margin-bottom: 0.25rem;
}

.security-info p,
.action-info p {
  font-size: 0.875rem;
  color: var(--text-muted);
}

.action-btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 6px;
  background: var(--primary-color);
  color: white;
  font-size: 0.875rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.action-btn:hover {
  background: var(--primary-hover);
}

.btn-danger {
  background: var(--error-color);
}

.btn-danger:hover {
  background: #dc2626;
}

.danger-zone {
  border: 1px solid var(--error-color);
}

@media (max-width: 768px) {
  .profile-page {
    padding: 1rem;
  }

  .profile-grid {
    grid-template-columns: 1fr;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .security-item,
  .action-item {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
}
</style> 