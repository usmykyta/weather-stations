import clients from '../core/api.http';

class WeatherService {
    constructor() {
        this.api = clients.api;
    }
    getStations() {
       return this.api.get('weather/stations')
    }

    getWeather() {

    }

    getStationsWeather(stationIdentifiers = []) {
        return this.api.post('weather/stations', {stations: stationIdentifiers});
    }
}

export default WeatherService;