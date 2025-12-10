<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Lokasi Kerja Custom</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto">
        <div class="bg-white shadow-sm rounded p-4">
            <form method="post" action="{{ route('work-locations.store') }}" class="space-y-4">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="text-sm">Nama Lokasi</label>
                        <input name="name" class="w-full border rounded px-3 py-2" required value="{{ old('name') }}">
                    </div>
                    <div>
                        <label class="text-sm">Radius (meter)</label>
                        <input name="radius" type="number" class="w-full border rounded px-3 py-2" required value="{{ old('radius', 100) }}">
                    </div>
                    <div>
                        <label class="text-sm">Latitude</label>
                        <input name="latitude" type="number" step="any" class="w-full border rounded px-3 py-2" required value="{{ old('latitude') }}">
                    </div>
                    <div>
                        <label class="text-sm">Longitude</label>
                        <input name="longitude" type="number" step="any" class="w-full border rounded px-3 py-2" required value="{{ old('longitude') }}">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="text-sm font-semibold mb-2 block">Pilih Titik Lokasi (Geser marker)</label>
                    <div id="map" class="h-96 w-full border rounded-lg shadow-inner" style="height: 400px; z-index: 0;"></div>
                </div>

                <div class="flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                    <a href="{{ route('work-locations.index') }}" class="text-slate-600 px-4 py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const latInput = document.querySelector('input[name="latitude"]');
            const lngInput = document.querySelector('input[name="longitude"]');
            
            // Default to Jakarta
            let lat = -6.2088;
            let lng = 106.8456;
            let zoom = 11;
            
            if (navigator.geolocation) {
                 navigator.geolocation.getCurrentPosition((position) => {
                     const userLat = position.coords.latitude;
                     const userLng = position.coords.longitude;
                     if (!latInput.value) { 
                        map.setView([userLat, userLng], 15);
                        marker.setLatLng([userLat, userLng]);
                        latInput.value = userLat.toFixed(6);
                        lngInput.value = userLng.toFixed(6);
                     }
                 });
            }

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
            
            setTimeout(() => { map.invalidateSize(); }, 500);
        });
    </script>
</x-app-layout>
