import axios from "axios";

// Create an instance of axios with default properties
const axiosClient = axios.create({
    baseURL: "https://api.aboutcsgo.com/",

    // baseURL: "http://127.0.0.1:8000/",
});

export default axiosClient;
