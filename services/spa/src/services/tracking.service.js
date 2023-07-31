import clients from '../core/api.http';

class TrackingService {
    constructor() {
        this.api = clients.api;
    }

    track(latLng) {
        console.log(latLng);
        return this.api.post('track', latLng);
    }
}

export default TrackingService;