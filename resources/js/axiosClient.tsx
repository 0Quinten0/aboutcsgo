import axios from "axios";

// Create an instance of axios with default properties
const axiosClient = axios.create({
    baseURL: "https://aboutcsgo.com/api/v1/",

    // baseURL: "http://127.0.0.1:8000/api/v1/",
});

export default axiosClient;
