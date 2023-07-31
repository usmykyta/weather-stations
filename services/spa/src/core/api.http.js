import axios from 'axios';

export default {
    api: axios.create({
        baseURL: process.env.VUE_APP_API_URL
    })
}