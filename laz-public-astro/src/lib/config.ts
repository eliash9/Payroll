const isProduction = process.env.NODE_ENV === 'production';


export const config = {
  apiUrl: import.meta.env.PUBLIC_API_URL || (isProduction ? 'https://hrd.lazsidogiri.com/api/v1/laz' : 'http://localhost:8000/api/v1/laz'),
  storageUrl: import.meta.env.PUBLIC_STORAGE_URL || (isProduction ? 'https://hrd.lazsidogiri.com/storage' : 'http://localhost:8000/storage'),
};
