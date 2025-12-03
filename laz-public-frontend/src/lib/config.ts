const isProduction = process.env.NODE_ENV === 'production';

export const config = {
  apiUrl: process.env.NEXT_PUBLIC_API_URL || (isProduction ? 'https://hrd.lazsidogiri.com/api/v1/laz' : 'http://localhost:8000/api/v1/laz'),
  storageUrl: process.env.NEXT_PUBLIC_STORAGE_URL || (isProduction ? 'https://hrd.lazsidogiri.com/storage' : 'http://localhost:8000/storage'),
};
