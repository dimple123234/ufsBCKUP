<template>
  <div class="profile-page">
    <!-- Navigation -->
    <nav class="profile-nav">
      <router-link to="/order" class="nav-link">
        <i class="fas fa-home"></i> Home
      </router-link>
      <router-link to="/profile-settings" class="nav-link">
        <i class="fas fa-cog"></i> Settings
      </router-link>
      <template v-if="user && user.role">
        <router-link v-if="user.role === 'customer'" to="/order-history" class="nav-link">
          <i class="fas fa-history"></i> Order History
        </router-link>
        <router-link v-if="user.role === 'seller'" to="/my-products" class="nav-link">
          <i class="fas fa-box"></i> My Products
        </router-link>
      </template>
    </nav>

    <div class="profile-container">
      <!-- Profile Header -->
      <div class="profile-header">
        <div class="profile-picture-container">
          <img 
            :src="profilePictureUrl" 
            alt="Profile Picture" 
            class="profile-image"
            @error="handleImageError"
          />
          <div class="upload-overlay" @click="$refs.fileInput.click()">
            <i class="fas fa-camera"></i>
          </div>
        </div>
        <input 
          type="file" 
          ref="fileInput" 
          @change="handleFileSelect" 
          accept="image/*"
          style="display: none"
        />
        <div v-if="selectedFile" class="upload-actions">
          <button @click="uploadPicture" class="upload-btn">
            <i class="fas fa-upload"></i> Upload Picture
          </button>
          <button @click="cancelUpload" class="cancel-btn">
            <i class="fas fa-times"></i> Cancel
          </button>
        </div>
      </div>

      <!-- Profile Info -->
      <div class="profile-info">
        <h2>{{ user.name }}</h2>
        <p class="email">{{ user.email }}</p>
        <p class="contact">{{ user.number }}</p>
        <p class="address">{{ user.address }}</p>
      </div>

      <!-- Review Section -->
      <div class="review-section" v-if="activeTab === 'experience'">
        <h2>Share Your Experience</h2>
        <div class="review-form">
          <div class="rating">
            <span 
              v-for="star in 5" 
              :key="star" 
              class="star"
              :class="{ active: star <= review.rating }"
              @click="review.rating = star"
            >
              ★
            </span>
          </div>
          <textarea 
            v-model="review.message" 
            placeholder="Share your experience with us..."
            rows="4"
          ></textarea>
          <button @click="submitReview" class="submit-review">Submit Review</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'MyProfile',
  data() {
    return {
      user: {
        name: '',
        email: '',
        number: '',
        address: '',
        profile_picture: null,
        profile_picture_url: null
      },
      selectedFile: null,
      previewUrl: null,
      rating: 0,
      reviewMessage: '',
      review: {
        rating: 0,
        message: ''
      },
      activeTab: 'experience',
      isSeller: false,
      loading: true,
      error: null
    }
  },
  computed: {
    profilePictureUrl() {
      if (this.previewUrl) return this.previewUrl;
      if (this.user.profile_picture_url) return this.user.profile_picture_url;
      return '/images/admin.png';
    }
  },
  methods: {
    handleImageError(e) {
      console.error('Image failed to load:', e.target.src);
      e.target.src = '/images/admin.png';
    },
    async uploadPicture() {
      if (!this.selectedFile) {
        return;
      }

      const formData = new FormData();
      formData.append('profile_picture', this.selectedFile);
      formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
      
      try {
        const response = await axios.post('/profile/upload-picture', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });
        
        if (response.data.status === 'success') {
          this.user.profile_picture = response.data.path;
          this.user.profile_picture_url = response.data.url;
          
          this.selectedFile = null;
          this.previewUrl = null;
          
          alert('Profile picture updated successfully');
        }
      } catch (error) {
        console.error('Error uploading profile picture:', error);
        alert(error.response?.data?.message || 'Failed to upload profile picture');
      }
    },
    async fetchUserData() {
      try {
        const response = await axios.get('/api/profile');
        if (response.data && response.data.user) {
          const currentProfilePictureUrl = this.user?.profile_picture_url;
          this.user = response.data.user;
          
          // Set isSeller based on user role
          this.isSeller = this.user.role === 'seller';
          console.log('User role:', this.user.role, 'isSeller:', this.isSeller);
          
          if (currentProfilePictureUrl && !this.user.profile_picture_url) {
            this.user.profile_picture_url = currentProfilePictureUrl;
          }
        }
      } catch (error) {
        console.error('Error fetching user data:', error);
        this.error = error.message;
      } finally {
        this.loading = false;
      }
    },
    handleFileSelect(event) {
      const file = event.target.files[0];
      if (file) {
        this.selectedFile = file;
        this.previewUrl = URL.createObjectURL(file);
      }
    },
    cancelUpload() {
      this.selectedFile = null;
      this.previewUrl = null;
    },
    async submitReview() {
      if (this.review.rating === 0) {
        alert('Please select a rating');
        return;
      }
      if (!this.review.message.trim()) {
        alert('Please write a review message');
        return;
      }

      try {
        await axios.post('/api/reviews', this.review);
        alert('Thank you for your review!');
        this.review.rating = 0;
        this.review.message = '';
      } catch (error) {
        console.error('Error submitting review:', error);
        alert('Failed to submit review. Please try again.');
      }
    }
  },
  mounted() {
    this.fetchUserData();
  },
  beforeUnmount() {
    if (this.previewUrl) {
      URL.revokeObjectURL(this.previewUrl);
    }
  }
}
</script>

<style scoped>
.profile-page {
  padding: 20px;
  max-width: 800px;
  margin: 0 auto;
}

.profile-nav {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin-bottom: 30px;
}

.nav-link {
  text-decoration: none;
  color: #333;
  padding: 10px 20px;
  border-radius: 5px;
  transition: background-color 0.3s;
}

.nav-link:hover {
  background-color: #f0f0f0;
}

.profile-container {
  background: white;
  border-radius: 10px;
  padding: 30px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.profile-header {
  text-align: center;
  margin-bottom: 30px;
}

.profile-picture-container {
  position: relative;
  width: 150px;
  height: 150px;
  margin: 0 auto;
  border-radius: 50%;
  overflow: hidden;
}

.profile-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.upload-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(0, 0, 0, 0.5);
  padding: 8px;
  color: white;
  text-align: center;
  cursor: pointer;
  transition: background-color 0.3s;
}

.upload-overlay:hover {
  background: rgba(0, 0, 0, 0.7);
}

.profile-info {
  text-align: center;
  margin-bottom: 30px;
}

.profile-info h2 {
  margin-bottom: 10px;
  color: #333;
}

.profile-info p {
  color: #666;
  margin: 5px 0;
}

.review-section {
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 2rem;
  margin-top: 2rem;
}

.review-section h2 {
  color: #333;
  font-size: 1.5rem;
  margin-bottom: 1.5rem;
  text-align: center;
}

.review-form {
  max-width: 500px;
  margin: 0 auto;
}

.rating {
  display: flex;
  justify-content: center;
  margin-bottom: 1.5rem;
}

.star {
  font-size: 2rem;
  color: #ddd;
  cursor: pointer;
  margin: 0 0.2rem;
  transition: color 0.2s ease-in-out;
}

.star:hover,
.star.active {
  color: #FFD700;
}

textarea {
  width: 100%;
  padding: 1rem;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  font-family: inherit;
  resize: vertical;
  min-height: 120px;
  box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
  text-transform: none;
}

textarea:focus {
  outline: none;
  border-color: #4CAF50;
  box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.2);
}

.submit-review {
  display: block;
  width: 100%;
  background-color: #4CAF50;
  color: white;
  padding: 1rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 600;
  transition: all 0.3s ease;
}

.submit-review:hover {
  background-color: #45a049;
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.submit-review:active {
  transform: translateY(0);
  box-shadow: none;
}

.upload-actions {
  margin-top: 15px;
  display: flex;
  gap: 10px;
  justify-content: center;
}

.upload-btn, .cancel-btn {
  padding: 8px 16px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 5px;
  transition: background-color 0.3s;
}

.upload-btn {
  background-color: #4CAF50;
  color: white;
}

.upload-btn:hover {
  background-color: #45a049;
}

.cancel-btn {
  background-color: #666;
  color: white;
}

.cancel-btn:hover {
  background-color: #555;
}
</style>