<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Instansi</h2>
    </x-slot>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <div class="py-8 max-w-4xl mx-auto">
        <div class="bg-white shadow-sm rounded p-4">
            <form method="post" action="{{ route('companies.store') }}" class="space-y-4">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm">Nama Instansi</label>
                        <input name="name" class="w-full border rounded px-3 py-2" required value="{{ old('name') }}">
                    </div>
                    <div>
                        <label class="text-sm">Kode Instansi</label>
                        <input name="code" class="w-full border rounded px-3 py-2" value="{{ old('code') }}">
                    </div>
                </div>

                <!-- Region Selects -->
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm">Provinsi</label>
                        <select id="province" name="province_code" class="w-full border rounded px-3 py-2">
                            <option value="">Pilih Provinsi</option>
                        </select>
                        <input type="hidden" name="province_name" id="province_name">
                    </div>
                    <div>
                        <label class="text-sm">Kabupaten/Kota</label>
                        <select id="city" name="city_code" class="w-full border rounded px-3 py-2" disabled>
                            <option value="">Pilih Kota/Kabupaten</option>
                        </select>
                        <input type="hidden" name="city_name" id="city_name">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm">Kecamatan</label>
                        <select id="district" name="district_code" class="w-full border rounded px-3 py-2" disabled>
                            <option value="">Pilih Kecamatan</option>
                        </select>
                        <input type="hidden" name="district_name" id="district_name">
                    </div>
                    <div>
                        <label class="text-sm">Desa/Kelurahan</label>
                        <select id="village" name="village_code" class="w-full border rounded px-3 py-2" disabled>
                            <option value="">Pilih Desa/Kelurahan</option>
                        </select>
                        <input type="hidden" name="village_name" id="village_name">
                    </div>
                </div>

                <div>
                    <label class="text-sm">Alamat Lengkap</label>
                    <textarea name="address" class="w-full border rounded px-3 py-2">{{ old('address') }}</textarea>
                </div>

                <!-- Map -->
                <div>
                    <label class="text-sm mb-2 block">Lokasi Peta</label>
                    <div id="map" class="w-full h-64 border rounded z-0"></div>
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <div>
                            <label class="text-xs text-gray-500">Latitude</label>
                            <input type="text" name="latitude" id="latitude" class="w-full border rounded px-2 py-1 bg-gray-50 text-sm" readonly>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Longitude</label>
                            <input type="text" name="longitude" id="longitude" class="w-full border rounded px-2 py-1 bg-gray-50 text-sm" readonly>
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm">Telepon</label>
                        <input name="phone" class="w-full border rounded px-3 py-2" value="{{ old('phone') }}">
                    </div>
                    <div>
                        <label class="text-sm">Email</label>
                        <input name="email" type="email" class="w-full border rounded px-3 py-2" value="{{ old('email') }}">
                    </div>
                    <div>
                        <label class="text-sm">NPWP</label>
                        <input name="npwp" class="w-full border rounded px-3 py-2" value="{{ old('npwp') }}">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                    <a href="{{ route('companies.index') }}" class="text-slate-600 px-4 py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Map Initialization
            const map = L.map('map').setView([-2.5489, 118.0149], 5); // Indonesia Center
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            let marker;

            function updateMarker(lat, lng) {
                if (marker) {
                    marker.setLatLng([lat, lng]);
                } else {
                    marker = L.marker([lat, lng]).addTo(map);
                }
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
            }

            map.on('click', function(e) {
                updateMarker(e.latlng.lat, e.latlng.lng);
            });

            // Region API
            const baseUrl = 'https://www.emsifa.com/api-wilayah-indonesia/api';
            
            const provinceSelect = document.getElementById('province');
            const citySelect = document.getElementById('city');
            const districtSelect = document.getElementById('district');
            const villageSelect = document.getElementById('village');

            async function fetchRegions(url, selectElement, defaultText) {
                selectElement.innerHTML = `<option value="">Loading...</option>`;
                selectElement.disabled = true;
                
                try {
                    const response = await fetch(url);
                    const data = await response.json();
                    
                    selectElement.innerHTML = `<option value="">${defaultText}</option>`;
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.text = `${item.id} - ${item.name}`;
                        option.dataset.name = item.name;
                        selectElement.appendChild(option);
                    });
                    selectElement.disabled = false;
                } catch (error) {
                    console.error('Error fetching regions:', error);
                    selectElement.innerHTML = `<option value="">Error Loading</option>`;
                }
            }

            // Load Provinces
            fetchRegions(`${baseUrl}/provinces.json`, provinceSelect, 'Pilih Provinsi');

            // Handle Changes
            provinceSelect.addEventListener('change', function() {
                const id = this.value;
                const name = this.options[this.selectedIndex].dataset.name;
                document.getElementById('province_name').value = name || '';

                if (id) {
                    fetchRegions(`${baseUrl}/regencies/${id}.json`, citySelect, 'Pilih Kota/Kabupaten');
                    resetSelect(districtSelect, 'Pilih Kecamatan');
                    resetSelect(villageSelect, 'Pilih Desa/Kelurahan');
                } else {
                    resetSelect(citySelect, 'Pilih Kota/Kabupaten');
                    resetSelect(districtSelect, 'Pilih Kecamatan');
                    resetSelect(villageSelect, 'Pilih Desa/Kelurahan');
                }
            });

            citySelect.addEventListener('change', function() {
                const id = this.value;
                const name = this.options[this.selectedIndex].dataset.name;
                document.getElementById('city_name').value = name || '';

                if (id) {
                    fetchRegions(`${baseUrl}/districts/${id}.json`, districtSelect, 'Pilih Kecamatan');
                    resetSelect(villageSelect, 'Pilih Desa/Kelurahan');
                } else {
                    resetSelect(districtSelect, 'Pilih Kecamatan');
                    resetSelect(villageSelect, 'Pilih Desa/Kelurahan');
                }
            });

            districtSelect.addEventListener('change', function() {
                const id = this.value;
                const name = this.options[this.selectedIndex].dataset.name;
                document.getElementById('district_name').value = name || '';

                if (id) {
                    fetchRegions(`${baseUrl}/villages/${id}.json`, villageSelect, 'Pilih Desa/Kelurahan');
                } else {
                    resetSelect(villageSelect, 'Pilih Desa/Kelurahan');
                }
            });
            
            villageSelect.addEventListener('change', function() {
                const name = this.options[this.selectedIndex].dataset.name;
                document.getElementById('village_name').value = name || '';
            });

            function resetSelect(element, defaultText) {
                element.innerHTML = `<option value="">${defaultText}</option>`;
                element.disabled = true;
            }
        });
    </script>
</x-app-layout>
