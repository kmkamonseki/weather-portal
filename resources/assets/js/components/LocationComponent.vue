<template>
  <div class="col-md-6 float-left">
    <div class="card mt-3">
      <div class="card-body">
      <h4>{{ city }} Weather</h4>
          <h2 v-if="forecast.length > 0">{{ Math.round(forecast[0].now) }}</h2>
          <button @click="del" class="btn btn-danger">Delete Forecast</button>
          <table class="table table-striped table-dark mt-3">
              <thead>
              <tr>
                  <th scope="col">Day</th>
                  <th scope="col">Min</th>
                  <th scope="col">Max</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="weather in forecast">
                  <th scope="row">{{ weather.date }}</th>
                  <td> {{ Math.round(weather.min) }}</td>
                  <td> {{ Math.round(weather.max) }}</td>
              </tr>
              </tbody>
          </table>
      </div>
    </div>
  </div>
</template>
<script>
  function Weather({ city, date, min, max, now }) {
    this.city = city;
    this.date = date;
    this.min = min;
    this.max = max;
    this.now = now;
  }
  export default {
  data() {
    return {
        forecast: [],
        forecast_averages: []
      }
    },
    methods: {
      del() {
        this.$emit('delete', this.id);
      },
      get_forecast() {
        window.axios.get(`/api/locations/${this.id}/forecast`).then(({ data }) => {
          this.forecast = [];
          data.forEach(weather => {
            this.forecast.push(new Weather(weather));
          });
        });
      },
    },
    props: ['id', 'city'],
    mounted() {
        this.get_forecast();
    }
  }

</script>
