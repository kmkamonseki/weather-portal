<template>
  <div id="app" class="col-md-12">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">Dashboard</div>
        <div class="card-body">
          <div class="form-row">
            <div class="col">
              <input v-model="city" type="city" class="form-control" id="city" placeholder="Enter a city" @keydown.enter="create">
              <div v-if="error_message" class="alert alert-danger mt-3" role="alert">{{ error_message }}</div>
              <div v-if="success_message" class="alert alert-success mt-3" role="alert">{{ success_message }}</div>
            </div>
            <div class="col">
              <button @click="create()" class="btn btn-success btn">Add</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <location-component
      v-for="location in locations"
      v-bind="location"
      :key="location.id"
      @delete="del"></location-component>
  </div>
</template>
<script>
  function Location({ id, city }) {
    this.id = id;
    this.city = city;
  }

  import LocationComponent from './LocationComponent.vue';

  export default {
    data() {
      return {
        city: '',
        locations: this.read(),
        error_message: '',
        success_message: '',
      }
    },
    methods: {
      create() {
        window.axios.post('/api/locations', {
          city: city.value
        }).then(() => {
          this.error_message = '';
          //this.success_message = `"${city.value}" included in your weather forecast!`;
          this.read();
        }).catch(() => {
          this.error_message = `Sorry, we couldn\'t find "${city.value}" in our weather services.`;
          //this.success_message = '';
        });
      },
      read() {
        window.axios.get('/api/locations').then(({ data }) => {
          this.locations = [];
          data.forEach(location => {
            this.locations.push(new Location(location));
          });
        });
      },
      del(id) {
        window.axios.delete(`/api/locations/${id}`).then(() => {
          let index = this.locations.findIndex(location => location.id === id);
          this.locations.splice(index, 1);
        });
      },
    },
    components: {
      LocationComponent
    }
  }
</script>
