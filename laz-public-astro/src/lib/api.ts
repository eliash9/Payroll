import axios from 'axios';
import { config } from './config';

export const apiClient = axios.create({
    baseURL: config.apiUrl,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

export const fetcher = (url: string) => apiClient.get(url).then((res) => res.data);
