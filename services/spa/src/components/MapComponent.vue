<template>
  <div class="row no-margin">
    <div v-if="!states.loading" class="col m8">
      <l-map style="height: 100vh" :zoom="parseInt(zoom)" :center="center" v-if="center" @click="onMapClickEvent"
             ref="weatherMap">
        <l-tile-layer :url="url"></l-tile-layer>

        <l-marker v-for="station in coordinates.stations" :lat-lng="station.coordinates"
                  :key="`station_marker_${station.id}`" :ref="`station_marker_${station.id}`"></l-marker>

        <l-circle
            v-if="coordinates.selectedRadius"
            :lat-lng="coordinates.selectedRadius"
            :radius="milesToMeters(defaults.circleRadius)"
            :color="defaults.circleRadiusColor"
        />
      </l-map>
    </div>
    <div v-if="!states.loading" class="col m4">
      <ul class="collection with-header">
        <li class="collection-header"><h4>Settings</h4></li>
        <li class="collection-item">
          <p v-text="`Range: ${defaults.circleRadius} mi (${(milesToMeters(defaults.circleRadius) / 1000).toFixed(2)} km)`"></p>
          <p class="range-field">
            <input type="range" v-model="defaults.circleRadius" min="1" max="1000"/>
          </p>
        </li>
        <li class="collection-item">
          <p v-text="`Zoom: ${zoom}`"></p>
          <p class="range-field">
            <input type="range" v-model="zoom" min="1" max="18"/>
          </p>
        </li>
      </ul>
      <ul class="collection with-header stations-list">
        <li class="collection-header">
          <h5>
            Stations <span v-text="': ' + filteredStations.length" v-if="filteredStations.length > 0"></span><br/>
            Average temperature <span v-text="': ' + averageTemperature + ' °C'"
                                      v-if="filteredStations.length > 0"></span>
          </h5>
        </li>
        <li class="collection-item" v-if="!filteredStations.length">
          No available stations within the radius
        </li>
        <li class="collection-item" v-else v-for="station in filteredStations" :key="`station_in_radius_${station.id}`">
          <div class="row">
            <div class="col m">
              <img :src="station.weather.icon" alt="">
            </div>
            <div class="col m5">
              <ul>
                <li><span class="station-name" v-text="station.name"></span></li>
                <br />
                <li>Temp: <span v-text="station.weather['temperatures']['temp'] + ' °C'"></span></li>
                <li>Min Temp: <span v-text="station.weather['temperatures']['temp_min']  + ' °C'"></span></li>
                <li>Max Temp: <span v-text="station.weather['temperatures']['temp_max']  + ' °C'"></span></li>

              </ul>
            </div>
          </div>
        </li>
      </ul>
    </div>

    <div class="loading--block" v-if="states.loading">
      <div class="preloader-wrapper big active">
        <div class="spinner-layer spinner-blue-only">
          <div class="circle-clipper left">
            <div class="circle"></div>
          </div>
          <div class="gap-patch">
            <div class="circle"></div>
          </div>
          <div class="circle-clipper right">
            <div class="circle"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import WeatherService from "@/services/weather.service";
import TrackingService from "@/services/tracking.service";
import {distanceBetweenCoordinates} from "@/core/helpers";

export default {
  name: 'MapComponent',
  data: function () {
    return {
      center: null,
      states: {
        loading: true
      },
      zoom: 12,
      url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
      services: {
        weatherService: new WeatherService,
        trackingService: new TrackingService
      },
      coordinates: {
        stations: [],
        selectedRadius: null
      },
      defaults: {
        circleRadius: 1, // in miles
        circleRadiusColor: 'orange',
        stationsInRadius: []
      },
      refs: {
        weatherMap: this.$refs.weatherMap
      }
    };
  },
  beforeCreate() {
    navigator.geolocation.watchPosition(({coords}) => {
      if (!this.center) {
        this.center = [
          coords.latitude,
          coords.longitude
        ]
      }
    });
  },
  mounted() {
    this.setCoords();
    this.setDefaults();
    this.getStations();
  },
  methods: {
    setCoords() {
      let coordinates = JSON.parse(localStorage.getItem('coordinates.selectedRadius') || 'null');

      if (coordinates) {
        this.coordinates.selectedRadius = coordinates;

        this.center = [
          this.coordinates.selectedRadius.lat,
          this.coordinates.selectedRadius.lng,
        ];
      }
    },
    setDefaults() {
      this.defaults.circleRadius = parseInt(localStorage.getItem('defaults.circleRadius') || 1)
      this.zoom = parseInt(localStorage.getItem('zoom') || 12);
    },
    getStations() {
      this.services.weatherService.getStations()
          .then(({data}) => {
            this.coordinates.stations = [];

            data.stations.forEach(station => {
              this.coordinates.stations.push({
                id: station.id,
                coordinates: [
                  station.lat,
                  station.lng,
                ],
                weather: station.weather,
                name: station.name
              });
            });

            if (!this.center && this.coordinates.stations.length) {
              this.center = this.coordinates.stations[0].coordinates;
            }
          }).finally(() => this.states.loading = false);
    },
    onMapClickEvent(e) {
      this.services.trackingService.track(e.latlng);
      this.coordinates.selectedRadius = e.latlng;
    },
    metersToMiles(meters = 0) {
      return meters / 1609.34;
    },
    milesToMeters(miles = 0) {
      return miles * 1609.34;
    },
  },
  watch: {
    defaults: {
      deep: true,
      handler: function (val) {
        localStorage.setItem('defaults.circleRadius', val.circleRadius);
      }
    },
    coordinates: {
      deep: true,
      handler: function (val) {
        localStorage.setItem('coordinates.selectedRadius', JSON.stringify(val.selectedRadius));
      }
    },
    zoom: function (val) {
      localStorage.setItem('zoom', val.toString());
    }
  },

  computed: {
    filteredStations() {
      if (!this.coordinates.selectedRadius) {
        return [];
      }

      return this.coordinates.stations.filter(station => {
        return distanceBetweenCoordinates(...station.coordinates, this.coordinates.selectedRadius.lat, this.coordinates.selectedRadius.lng) <= this.defaults.circleRadius
      });
    },

    averageTemperature() {
      if (this.filteredStations.length === 0) {
        return null;
      }

      const sum = this.filteredStations.reduce((total, station) => {
        return total + station.weather.temperatures.temp;
      }, 0);

      const average = sum / this.filteredStations.length;

      return average.toFixed(2);
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.no-margin {
  margin: 0;
}

.stations-list {
  max-height: 50vh;
  overflow-y: visible;
}

.station-name {
  font-size: 20px;
}

div.loading--block {
  display: flex;
  height: 100vh;
  width: 100vw;
  margin: 0;
  align-content: center;
  align-items: center;
  justify-content: center;
}
</style>
