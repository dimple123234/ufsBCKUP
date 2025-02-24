<template>
  <div class="page-wrapper">
    <!-- Main Content Container -->
    <div class="content">
      <div class="header1">
        <h1><span style="color: rgb(202, 6, 6);">Our</span> Team</h1>
      </div>
     <!-- Use router-link to navigate back to HomePage.vue -->
     <router-link to="/" class="hire">Go Back</router-link>
      <div class="sub-container">
        <div class="teams" v-for="(member, index) in teamMembers" :key="index">
          <img :src="member.image" :alt="member.name" />
          <div class="name">{{ member.name }}</div>
          <div class="desig">{{ member.position }}</div>
          <div class="about">{{ member.description }}</div>
          <div class="social-links">
            <a :href="member.facebook" target="_blank" v-if="member.facebook"><i class="fa fa-facebook"></i></a>
            <a :href="member.instagram" target="_blank" v-if="member.instagram"><i class="fa fa-instagram"></i></a>
            <a :href="member.twitter" target="_blank" v-if="member.twitter"><i class="fa fa-twitter"></i></a>
            <a :href="member.github" target="_blank" v-if="member.github"><i class="fa fa-github"></i></a>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="footer">
      <p>Copyright &copy;2023; Designed by <span class="designer">Kenny Fazbear</span></p>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      teamMembers: [
        {
          name: "Ren Laureanne G. Perandos",
          position: "Developer",
          description: "The Creator of this website. Due to his curiosity he was able to make and brainstorm tons of ideas and knowledge that he watched on several platforms, until he made this one.",
          image: "images/aboutUs/ken.jpg",
          facebook: "https://web.facebook.com/nateriver4",
          instagram: "#",
          twitter: "#",
          github: "#"
        },
        {
          name: "Dimple Kay L. Eguna",
          position: "Co-Developer",
          description: "The assistant, suggesting and telling the developer some ideas about what should be the next move and what changes should he make.",
          image: "images/aboutUs/dimplax.jpg",
          facebook: "https://web.facebook.com/marc.salamanca.3",
          instagram: "#",
          twitter: "#",
          github: "#"
        },
        {
          name: "Kenny Fazbear",
          position: "Manager",
          description: "The developer's alter ego, this is his inner self when he is in the zone, and is too focused on what he is doing. He is the reason why the developer is so unique and inspired.",
          image: "images/aboutUs/freddy.jpg",
          facebook: "#",
          instagram: "#",
          twitter: "#",
          github: "#"
        }
      ]
    };
  }
};
</script>
  
<style scoped>
@import url('https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
}

.page-wrapper {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.content {
  flex-grow: 1;
  text-align: center;
  background: #f5f5f5;
}

.header1 {
  padding-top: 30px;
  color: #444;
  font-size: 20px;
  margin: auto;
  line-height: 50px;
}

.sub-container {
  max-width: 1200px;
  margin: auto;
  padding: 30px 0;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
}

.teams {
  margin: 10px;
  padding: 22px;
  max-width: 30%;
  cursor: pointer;
  transition: 0.4s;
  box-sizing: border-box;
}

.teams:hover {
  background: #ddd;
  border-radius: 12px;
}

.teams img {
  width: 200px;
  height: 200px;
}

.name {
  padding: 12px;
  font-weight: bold;
  font-size: 16px;
  text-transform: uppercase;
}

.desig {
  font-style: italic;
  color: #888;
  font-size: 16px;
}

.about {
  margin: 20px 0;
  font-weight: lighter;
  color: #4e4e4e;
  font-size: 16px;
  text-transform: none; /* Ensure text displays as-is */
}


.social-links {
  margin: 14px;
}

.social-links a {
  display: inline-block;
  height: 30px;
  width: 40px;
  transition: .4s;
}

.social-links a:hover {
  transform: scale(1.5);
}

.social-links a i {
  color: #444;
  font-size: 17px;
}

.footer {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  background-color: rgb(202, 6, 6);
  padding: 35px;
  text-align: center;
}

.footer p {
  color: white;
  margin: 0;
  font-size: 15px;
}

.designer {
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 600;
  color: black;
}

.hire {
  font-size: 18px;
  background: #ff3838;
  color: #fff;
  text-decoration: none;
  border: none;
  padding: 8px 25px;
  border-radius: 6px;
  transition: 0.5s;
  position: absolute;
  left: 20px;
  top: 20px;
}

.hire:hover {
  background: yellow;
  color: black;
}
</style>