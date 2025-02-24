<template>
  <div>
    <!-- Header -->
    <header class="header">
      <a href="#" id="logo">
        <img src="images/logo1.png" alt="" />
      </a>
      <div id="menu-bars" class="fas fa-bars"></div>
      <nav class="navbar">
        <a href="#home">home</a>
        <a href="#speciality">Specialty</a>
        <a href="#popular">Popular</a>
        <a href="#gallery">Gallery</a>
        <a href="#review">Review</a>
        <!-- Vue Router link for About Us -->
        <router-link to="/about-us">About Us</router-link>
        <a href="#order">Order</a>
      </nav>
      <!-- Login and Register Buttons -->
      <div class="auth-buttons">
        <a href="/login" class="btn login-btn">Login</a>
        <a href="/register" class="btn register-btn">Register</a>
      </div>
    </header>

    <!-- Home Section -->
    <section id="home" class="home">
      <div class="image myimage">
        <img src="images/homeimg.png" alt="" />
      </div>
      <div class="content">
        <h3>universal fast food</h3>
        <p>Flavors for Every Palate, Speed for Every Craving!</p>
        <a href="#order" class="btn">order now</a>
      </div>
    </section>

    <!-- Speciality Section -->
    <section class="speciality" id="speciality">
      <h1 class="speciality-heading">
        most <span>ordered foods</span>
      </h1>

      <div class="main-box-speciality">
        <div v-for="(food, index) in foods" :key="index" class="box">
          <img :class="food.imgClass" :src="food.image" alt="" />
          <div class="content">
            <img :src="food.logo" alt="" />
            <h3>{{ food.title }}</h3>
            <p>{{ food.description }}</p>
          </div>
        </div>
      </div>
    </section>
    <section class="popular" id="popular">
      <h1 class="heading">all available<span> foods</span></h1>

      <div class="box-container">
        <!-- Repeat this block for each food item -->
        <div class="box" v-for="(item, index) in foodItems" :key="index">
          <span class="price">{{ item.price }}</span>
          <div class="icons">
            <i class="fas fa-share"></i>
            <i class="fas fa-heart"></i>
            <i class="fas fa-eye-slash"></i>
          </div>
          <img :src="item.image" :alt="item.name" />
          <h3>{{ item.name }}</h3>
          <div class="stars">
            <i v-for="star in item.stars" :key="star" class="fas fa-star"></i>
            <i v-for="emptyStar in item.emptyStars" :key="emptyStar" class="far fa-star"></i>
          </div>
          <!-- Update the Order link to use router-link -->
          <!-- <a href="#order" class="btn">order now</a> -->
        </div>
      </div>
    </section>

    <!-- Gallery Section -->
    <section class="gallery" id="gallery">
      <h1 class="heading"><span>Our</span> Gallery </h1>
      <div class="main-gallery-box">
        <div v-for="(item, index) in galleryItems" :key="index" class="box">
          <img :src="item.image" class="image" :alt="item.alt">
          <div class="content">
            <img :src="item.logo" :alt="item.logoAlt">
            <h3>{{ item.title }}</h3>
            <p>{{ item.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Review Section -->
    <section class="review" id="review">
      <h1 class="heading">Our Customers <span>Reviews</span></h1>
      <div class="box-container">
        <div v-for="review in reviews" :key="review.id" class="box">
          <div class="user-profile">
            <img 
              :src="review.user.profile_picture_url" 
              :alt="review.user.name"
              @error="handleImageError($event)"
            >
          </div>
          <h3>{{ review.user.name }}</h3>
          <div class="stars">
            <i v-for="n in review.rating" :key="n" class="fas fa-star"></i>
          </div>
          <p>{{ review.message }}</p>
        </div>
      </div>
    </section>

    <section class="footer">
      <div class="share">
        <a href="https://web.facebook.com/nateriver4" class="btn">facebook</a>
        <a href="#" class="btn">twitter</a>
        <a href="#" class="btn">instagram</a>
        <a href="#" class="btn">pinterest</a>
        <a href="#" class="btn">linkedin</a>
      </div>
    </section>

    <!-- Scroll top button -->
    <a href="#home" class="fas fa-angle-up" id="scroll-top"></a>
  </div>
</template>

<script>
import '/resources/css/style.css';
import axios from 'axios';

export default {
  data() {
    return {
      foods: [
        {
          image: "images/specialty/mcdo1.png",
          logo: "images/logos/mcdo2.png",
          title: "McDonald Crispy Chicken Fillet",
          description: "One of McDonald's best!",
          imgClass: "image",
        },
        {
          image: "images/specialty/jollibee1.png",
          logo: "images/logos/jollibee1.png",
          title: "Jollibee Yummy Burger Steak",
          description: "Jollibee's best easy meal",
          imgClass: "image",
        },
        {
          image: "images/specialty/ck1.png",
          logo: "images/logos/ck1.png",
          title: "Chowking Pork Chow Fan",
          description: "Chowking's best ever meal",
          imgClass: "image",
        },
        {
          image: "images/specialty/gw1.png",
          logo: "images/logos/gw1.png",
          title: "Greenwich Hawaiian Pizza",
          description: "One of Greenwich's best pizza",
          imgClass: "image",
        },
        {
          image: "images/specialty/mang1.png",
          logo: "images/logos/mang1.png",
          title: "Mang Inasal Paa",
          description: "Mang Inasal's best meal",
          imgClass: "image",
        },
        {
          image: "images/specialty/crispy1.png",
          logo: "images/logos/crispy1.png",
          title: "Crispy King Chicken",
          description: "People's favorite",
          imgClass: "image",
        },
        {
          image: "images/specialty/mcdo2.png",
          logo: "images/logos/mcdo2.png",
          title: "McSpaghetti with Burger",
          description: "Diet meal by McDonald's",
          imgClass: "image",
        },
        {
          image: "images/specialty/jollibee2.png",
          logo: "images/logos/jollibee1.png",
          title: "Jolly Spaghetti with Burger",
          description: "Easy meal by Jollibee",
          imgClass: "image",
        },
        {
          image: "images/specialty/ck2.png",
          logo: "images/logos/ck1.png",
          title: "Chowking Lauriat",
          description: "Easy meal by Chowking",
          imgClass: "image",
        },
        {
          image: "images/new products/manginasalpalabok.png",
          logo: "images/logos/ck1.png",
          title: "Chowking Lauriat",
          description: "Easy meal by Chowking",
          imgClass: "image",
        },
      ],

      foodItems: [
        {
          price: '78.00',
          image: 'images/specialty/mcdo1.png',
          name: 'McCrispy Chicken Fillet',
          stars: [1, 2, 3, 4, 5],
          emptyStars: []
        },
        {
          price: '₱88.00',
          image: 'images/specialty/mcdo3.png',
          name: 'McCrispy Chicken Fillet Ala king',
          stars: [1, 2, 3, 4],
          emptyStars: [1]
        },
        {
          price: '₱50.00',
          image: 'images/specialty/jollibee1.png',
          name: 'Jollibee Burger Steak',
          stars: [1, 2, 3, 4, 5],
          emptyStars: []
        },
        {
          price: '₱90.00',
          image: 'images/specialty/ck1.png',
          name: 'Chowking Chow Fan',
          stars: [1, 2, 3, 4],
          emptyStars: [1]
        },
        {
          price: '₱178.00',
          image: 'images/specialty/ck2.png',
          name: 'Chowking Lauriat',
          stars: [1, 2, 3],
          emptyStars: [1, 2]
        },
        {
          price: '₱40.00',
          image: 'images/specialty/crispy1.png',
          name: 'crispy king chicken',
          stars: [1, 2, 3, 4, 5],
          emptyStars: []
        },
        {
          price: '₱116.00',
          image: 'images/specialty/mang1.png',
          name: 'Mang Inasal Paa',
          stars: [1, 2, 3, 4, 5],
          emptyStars: []
        },
        {
          price: '₱99.00-₱500.00',
          image: 'images/specialty/gw1.png',
          name: 'Greenwich hawaiian pizza',
          stars: [1, 2, 3, 4, 5],
          emptyStars: []
        },
        {
          price: '₱95.00',
          image: 'images/specialty/jollibee2.png',
          name: 'Jolly Spagghetti with Burger',
          stars: [1, 2, 3, 4, 5],
          emptyStars: []
        },
        {
          price: '₱114.00',
          image: 'images/specialty/mcdo2.png',
          name: 'McSpagghetti with Burger',
          stars: [1, 2, 3, 4, 5],
          emptyStars: []
        },
        {
          price: '₱172.00',
          image: 'images/new products/manginasalpalabok.png',
          name: 'Mang Inasal Palabok',
          stars: [1, 2, 3, 4, 5],
          emptyStars: []
        },
        {
          price: '₱87.00',
          image: 'images/new products/chillichicken.png',
          name: 'Jollibee Spicy Chicken',
          stars: [1, 2, 3, 4, 5],
          emptyStars: []
        },
        {
          price: '₱435.00',
          image: 'images/new products/jollibeebucketmeals.png',
          name: 'Jollibee Bucket Meals Promo',
          stars: [1, 2, 3, 4, 5],
          emptyStars: []
        },
        {
          price: '₱54.00',
          image: 'images/specialty/mcdo4.png',
          name: 'Coke McFloat',
          stars: [1, 2, 3, 4, 5],
          emptyStars: []
        },
        {
          price: '₱58.00',
          image: 'images/specialty/jollibee3.png',
          name: 'Jollibee Sundae',
          stars: [1, 2, 3, 4, 5],
          emptyStars: []
        }
      ],
      galleryItems: [
        {
          image: "images/gallery/3jollibee.jpg",
          alt: "Jollibee",
          logo: "images/logos/jollibee1.png",
          logoAlt: "Jollibee Logo",
          title: "Customers",
          description: "Random pictures taken from a customers eating using our website"
        },
        {
          image: "images/gallery/jollibeenew.jpg",
          alt: "Jollibee New",
          logo: "images/logos/jollibee1.png",
          logoAlt: "Jollibee Logo",
          title: "Customers",
          description: "Random pictures taken from a customers eating using our website"
        },
        {
          image: "images/gallery/mcdonew.jpg",
          alt: "Mcdo New",
          logo: "images/logos/mcdo2.png",
          logoAlt: "Mcdo Logo",
          title: "Customers",
          description: "Random pictures taken from a customers eating using our website"
        },
        {
          image: "images/gallery/fillet.jpg",
          alt: "Fillet",
          logo: "images/logos/mcdo2.png",
          logoAlt: "Mcdo Logo",
          title: "Customers",
          description: "Random pictures taken from a customers eating using our website"
        },
        {
          image: "images/gallery/pizza.jpg",
          alt: "Pizza",
          logo: "images/logos/gw1.png",
          logoAlt: "GW Logo",
          title: "Customers",
          description: "Random pictures taken from a customers eating using our website"
        },
        {
          image: "images/gallery/gwnew.jpg",
          alt: "GW New",
          logo: "images/logos/gw1.png",
          logoAlt: "GW Logo",
          title: "Customers",
          description: "Random pictures taken from a customers eating using our website"
        },
        {
          image: "images/gallery/2jollibee.jpg",
          alt: "Jollibee 2",
          logo: "images/logos/jollibee1.png",
          logoAlt: "Jollibee Logo",
          title: "Customers",
          description: "Random pictures taken from a customers eating using our website"
        },
        {
          image: "images/gallery/1mcdo.jpg",
          alt: "Mcdo 1",
          logo: "images/logos/mcdo2.png",
          logoAlt: "Mcdo Logo",
          title: "Customers",
          description: "Random pictures taken from a customers eating using our website"
        },
        {
          image: "images/gallery/2mcdo.jpg",
          alt: "Mcdo 2",
          logo: "images/logos/mcdo2.png",
          logoAlt: "Mcdo Logo",
          title: "Customers",
          description: "Random pictures taken from a customers eating using our website"
        },
        {
          image: "images/gallery/4jollibee.jpg",
          alt: "Jollibee 4",
          logo: "images/logos/jollibee1.png",
          logoAlt: "Jollibee Logo",
          title: "Customers",
          description: "Random pictures taken from a customers eating using our website"
        }
      ],
      reviews: [],
      rules: [
        "Click the button below",
        "Sign up all the needed forms",
        "Agree to our terms and conditions",
        "Then log in your account",
        "Pick and place your order",
        "Fill in information to process your order",
        "Checkout",
        "Wait for your order to be delivered to your location",
        "If our delivery guy calls you, pick it up and receive your order",
        "Enjoy your meal"
      ]

    };
  },
  methods: {
    redirectToLogin() {
      // Add your login redirect logic here (e.g., to the login page)
      window.location.href = "/login";  // Example redirect
    },
    handleImageError(event) {
      event.target.src = '/images/admin.png'; // Fallback image
    },
    async fetchReviews() {
      try {
        const response = await axios.get('/api/reviews');
        this.reviews = response.data;
        console.log('Reviews:', this.reviews); // For debugging
      } catch (error) {
        console.error('Error fetching reviews:', error);
      }
    }
  },
  mounted() {
    this.fetchReviews();
  }
};
</script>
