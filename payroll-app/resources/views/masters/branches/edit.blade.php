<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Cabang</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto">
        <div class="bg-white shadow-sm rounded p-4">
            <form method="post" action="{{ route('branches.update', $branch->id) }}" class="space-y-4">
                @csrf
                @method('put')
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm">Perusahaan</label>
                        <select name="company_id" class="w-full border rounded px-3 py-2" required>
                            @foreach($companies as $id => $name)
                                <option value="{{ $id }}" @selected(old('company_id', $branch->company_id) == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center space-x-2 mt-6">
                            <input type="checkbox" name="is_headquarters" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" @checked(old('is_headquarters', $branch->is_headquarters))>
                            <span class="text-sm text-gray-700">Kantor Pusat / Induk</span>
                        </label>
                    </div>
                    <div>
                        <label class="text-sm">Nama</label>
                        <input name="name" class="w-full border rounded px-3 py-2" required value="{{ old('name', $branch->name) }}">
                    </div>
                    <div>
                        <label class="text-sm">Kode</label>
                        <input name="code" class="w-full border rounded px-3 py-2" value="{{ old('code', $branch->code) }}">
                    </div>
                    <div>
                        <label class="text-sm">Telepon</label>
                        <input name="phone" class="w-full border rounded px-3 py-2" value="{{ old('phone', $branch->phone) }}">
                    </div>
                    <div>
                        <label class="text-sm">Latitude</label>
                        <input name="latitude" type="number" step="any" class="w-full border rounded px-3 py-2" value="{{ old('latitude', $branch->latitude) }}">
                    </div>
                    <div>
                        <label class="text-sm">Longitude</label>
                        <input name="longitude" type="number" step="any" class="w-full border rounded px-3 py-2" value="{{ old('longitude', $branch->longitude) }}">
                    </div>
                    <div>
                        <label class="text-sm">Grade</label>
                        <select name="grade" class="w-full border rounded px-3 py-2">
                            <option value="">Pilih Grade</option>
                            <option value="A" @selected(old('grade', $branch->grade) == 'A')>A</option>
                            <option value="B" @selected(old('grade', $branch->grade) == 'B')>B</option>
                            <option value="C" @selected(old('grade', $branch->grade) == 'C')>C</option>
                            <option value="D" @selected(old('grade', $branch->grade) == 'D')>D</option>
                        </select>
                    </div>
                </div>

                <!-- Region Selects -->
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm">Provinsi</label>
                        <select id="province" name="province_code" class="w-full border rounded px-3 py-2">
                            <option value="">Pilih Provinsi</option>
                        </select>
                        <input type="hidden" name="province_name" id="province_name" value="{{ old('province_name', $branch->province_name) }}">
                    </div>
                    <div>
                        <label class="text-sm">Kabupaten/Kota</label>
                        <select id="city" name="city_code" class="w-full border rounded px-3 py-2" disabled>
                            <option value="">Pilih Kota/Kabupaten</option>
                        </select>
                        <input type="hidden" name="city_name" id="city_name" value="{{ old('city_name', $branch->city_name) }}">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm">Kecamatan</label>
                        <select id="district" name="district_code" class="w-full border rounded px-3 py-2" disabled>
                            <option value="">Pilih Kecamatan</option>
                        </select>
                        <input type="hidden" name="district_name" id="district_name" value="{{ old('district_name', $branch->district_name) }}">
                    </div>
                    <div>
                        <label class="text-sm">Desa/Kelurahan</label>
                        <select id="village" name="village_code" class="w-full border rounded px-3 py-2" disabled>
                            <option value="">Pilih Desa/Kelurahan</option>
                        </select>
                        <input type="hidden" name="village_name" id="village_name" value="{{ old('village_name', $branch->village_name) }}">
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="text-sm font-semibold mb-2 block">Lokasi Cabang (Geser marker untuk koreksi)</label>
                    <div id="map" class="h-96 w-full border rounded-lg shadow-inner" style="height: 400px; z-index: 0;"></div>
                </div>

                <div class="mt-4">
                    <label class="text-sm">Alamat</label>
                    <textarea name="address" class="w-full border rounded px-3 py-2">{{ old('address', $branch->address) }}</textarea>
                </div>
                <div class="flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                    <a href="{{ route('branches.index') }}" class="text-slate-600 px-4 py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            const latInput = document.querySelector('input[name="latitude"]');
            const lngInput = document.querySelector('input[name="longitude"]');
            
            // Default to Jakarta if no coords
            let lat = latInput.value || -6.2088;
            let lng = lngInput.value || 106.8456;
            let zoom = latInput.value ? 15 : 11;

            const map = L.map('map').setView([lat, lng], zoom);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            let marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);

            marker.on('dragend', function(event) {
                const position = marker.getLatLng();
                latInput.value = position.lat.toFixed(6);
                lngInput.value = position.lng.toFixed(6);
            });

            // Click on map to move marker
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                latInput.value = e.latlng.lat.toFixed(6);
                lngInput.value = e.latlng.lng.toFixed(6);
            });
            
            // Update marker if inputs change manually
            [latInput, lngInput].forEach(input => {
                input.addEventListener('change', () => {
                    const newLat = parseFloat(latInput.value);
                    const newLng = parseFloat(lngInput.value);
                    if (!isNaN(newLat) && !isNaN(newLng)) {
                        marker.setLatLng([newLat, newLng]);
                        map.panTo([newLat, newLng]);
                    }
                });
            });
            
            // Force map invalidate size after load in case of layout shifts
            setTimeout(() => { map.invalidateSize(); }, 500);

            // Region API
            const baseUrl = 'https://www.emsifa.com/api-wilayah-indonesia/api';
            
            const provinceSelect = document.getElementById('province');
            const citySelect = document.getElementById('city');
            const districtSelect = document.getElementById('district');
            const villageSelect = document.getElementById('village');

            // Initial Values
            const initialProvince = "{{ old('province_code', $branch->province_code) }}";
            const initialCity = "{{ old('city_code', $branch->city_code) }}";
            const initialDistrict = "{{ old('district_code', $branch->district_code) }}";
            const initialVillage = "{{ old('village_code', $branch->village_code) }}";

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
            await fetchRegions(`${baseUrl}/provinces.json`, provinceSelect, 'Pilih Provinsi');
            if (initialProvince) {
                provinceSelect.value = initialProvince;
                
                // Load Cities
                await fetchRegions(`${baseUrl}/regencies/${initialProvince}.json`, citySelect, 'Pilih Kota/Kabupaten');
                if (initialCity) {
                    citySelect.value = initialCity;
                    
                    // Load Districts
                    await fetchRegions(`${baseUrl}/districts/${initialCity}.json`, districtSelect, 'Pilih Kecamatan');
                    if (initialDistrict) {
                         districtSelect.value = initialDistrict;

                         // Load Villages
                         await fetchRegions(`${baseUrl}/villages/${initialDistrict}.json`, villageSelect, 'Pilih Desa/Kelurahan');
                         if (initialVillage) {
                             villageSelect.value = initialVillage;
                         }
                    }
                }
            }

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
