@extends('layouts.app')

@section('title', 'Peta Gangguan - Road Incident Map')

@section('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
<style>
    #map {
        height: 70vh;
        width: 100%;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .map-controls {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1000;
        background: white;
        padding: 10px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .disturbance-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .disturbance-item {
        border-left: 4px solid #007bff;
        margin-bottom: 10px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
    }

    .disturbance-item.resolved {
        border-left-color: #28a745;
        opacity: 0.7;
    }

    .status-badge {
        font-size: 0.8em;
        padding: 2px 8px;
    }

    .type-badge {
        font-size: 0.8em;
        padding: 2px 8px;
        margin-right: 5px;
    }

    .modal-header {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
    }

    .btn-close {
        filter: invert(1);
    }

</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2">
                                <i class="fas fa-map-marked-alt text-primary me-3"></i>
                                Peta Gangguan Jalan
                            </h2>
                            <p class="text-muted mb-0">
                                Tambah dan kelola gangguan di jalan raya
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDisturbanceModal">
                                <i class="fas fa-plus me-2"></i>Tambah Gangguan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-body p-0">
                    <div id="map"></div>
                    <div class="map-controls">
                        <button class="btn btn-sm btn-outline-primary" onclick="getCurrentLocation()">
                            <i class="fas fa-crosshairs"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Daftar Gangguan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="disturbance-list" id="disturbanceList">
                        <!-- Disturbances will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Disturbance Modal -->
<div class="modal fade" id="addDisturbanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Tambah Gangguan Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addDisturbanceForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Judul Gangguan</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Jenis Gangguan</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="">Pilih jenis gangguan</option>
                            <option value="road_construction">Konstruksi Jalan</option>
                            <option value="traffic_jam">Macet</option>
                            <option value="accident">Kecelakaan</option>
                            <option value="flood">Banjir</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>

                    <div class="row" id="latlng-row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="number" class="form-control" id="latitude" name="latitude" step="any">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="number" class="form-control" id="longitude" name="longitude" step="any">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipe Penandaan Lokasi</label>
                        <select class="form-control" id="location_mode" name="location_mode" required>
                            <option value="marker">Titik (Marker)</option>
                            <option value="area">Area (Polygon)</option>
                        </select>
                    </div>

                    <input type="hidden" id="area" name="area">

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Klik pada peta untuk mengisi koordinat secara otomatis, atau masukkan manual.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Status Modal -->
<div class="modal fade" id="editStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Ubah Status Gangguan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editStatusForm">
                <div class="modal-body">
                    <input type="hidden" id="editDisturbanceId">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="ongoing">Berlangsung</option>
                            <option value="resolved">Selesai</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
<script>
    let map;
    let markers = [];
    let currentMarker;
    let drawnItems;
    let drawControl;
    let currentPolygon;

    function setDrawControl(mode) {
        if (drawControl) {
            map.removeControl(drawControl);
        }
        if (mode === 'area') {
            drawControl = new L.Control.Draw({
                draw: {
                    polygon: true
                    , marker: false
                    , polyline: false
                    , rectangle: false
                    , circle: false
                    , circlemarker: false
                }
                , edit: {
                    featureGroup: drawnItems
                    , edit: false
                    , remove: true
                }
            });
            map.addControl(drawControl);
        } else {
            // Tidak ada tombol draw
            drawControl = null;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        map = L.map('map').setView([-2.5489, 118.0149], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);
        setDrawControl('marker');
        loadDisturbances();
        document.getElementById('location_mode').addEventListener('change', function() {
            setDrawControl(this.value);
            if (currentMarker) {
                map.removeLayer(currentMarker);
                currentMarker = null;
            }
            if (currentPolygon) {
                map.removeLayer(currentPolygon);
                currentPolygon = null;
            }
            drawnItems.clearLayers();
            document.getElementById('area').value = '';
            document.getElementById('latitude').value = '';
            document.getElementById('longitude').value = '';
        });
        map.on(L.Draw.Event.CREATED, function(e) {
            if (e.layerType === 'polygon') {
                if (currentPolygon) {
                    map.removeLayer(currentPolygon);
                }
                currentPolygon = e.layer;
                drawnItems.addLayer(currentPolygon);
                const latlngs = currentPolygon.getLatLngs()[0].map(pt => pt.lat.toFixed(6) + ',' + pt.lng.toFixed(6));
                document.getElementById('area').value = latlngs.join(';');
                if (currentMarker) {
                    map.removeLayer(currentMarker);
                    currentMarker = null;
                }
                document.getElementById('latitude').value = '';
                document.getElementById('longitude').value = '';
            }
        });
        map.on('click', function(e) {
            if (document.getElementById('location_mode').value === 'marker') {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                document.getElementById('latitude').value = lat.toFixed(6);
                document.getElementById('longitude').value = lng.toFixed(6);
                if (currentMarker) {
                    map.removeLayer(currentMarker);
                }
                currentMarker = L.marker([lat, lng], {
                    icon: L.divIcon({
                        className: 'custom-div-icon'
                        , html: '<div style="background-color: #007bff; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>'
                        , iconSize: [20, 20]
                        , iconAnchor: [10, 10]
                    })
                }).addTo(map);
                if (currentPolygon) {
                    map.removeLayer(currentPolygon);
                    currentPolygon = null;
                }
                drawnItems.clearLayers();
                document.getElementById('area').value = '';
            }
        });
    });

    function loadDisturbances() {
        fetch('/disturbances')
            .then(response => response.json())
            .then(data => {
                markers.forEach(marker => map.removeLayer(marker));
                markers = [];
                if (window.polygons) {
                    window.polygons.forEach(p => map.removeLayer(p));
                }
                window.polygons = [];
                data.forEach(disturbance => {
                    if (disturbance.area) {
                        const latlngs = disturbance.area.split(';').map(pair => {
                            const [lat, lng] = pair.split(',');
                            return [parseFloat(lat), parseFloat(lng)];
                        });
                        const polygon = L.polygon(latlngs, {
                            color: disturbance.status === 'ongoing' ? '#dc3545' : '#28a745'
                            , fillOpacity: 0.3
                        });
                        polygon.addTo(map);
                        polygon.bindPopup(`<div style="min-width: 200px;"><h6>${disturbance.title}</h6><p class="mb-2">${disturbance.description}</p><div class="mb-2"><span class="badge bg-${disturbance.status === 'ongoing' ? 'danger' : 'success'}">${disturbance.status === 'ongoing' ? 'Berlangsung' : 'Selesai'}</span><span class="badge bg-info">${getTypeText(disturbance.type)}</span></div><small class="text-muted">Dilaporkan oleh: ${disturbance.user.name}</small><br><small class="text-muted">${new Date(disturbance.created_at).toLocaleDateString('id-ID')}</small></div>`);
                        window.polygons.push(polygon);
                    } else {
                        const marker = L.marker([disturbance.latitude, disturbance.longitude], {
                            icon: L.divIcon({
                                className: 'custom-div-icon'
                                , html: `<div style="background-color: ${disturbance.status === 'ongoing' ? '#dc3545' : '#28a745'}; width: 25px; height: 25px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white; font-size: 12px;"><i class="fas fa-${getIconForType(disturbance.type)}"></i></div>`
                                , iconSize: [25, 25]
                                , iconAnchor: [12, 12]
                            })
                        }).addTo(map);
                        marker.bindPopup(`<div style="min-width: 200px;"><h6>${disturbance.title}</h6><p class="mb-2">${disturbance.description}</p><div class="mb-2"><span class="badge bg-${disturbance.status === 'ongoing' ? 'danger' : 'success'}">${disturbance.status === 'ongoing' ? 'Berlangsung' : 'Selesai'}</span><span class="badge bg-info">${getTypeText(disturbance.type)}</span></div><small class="text-muted">Dilaporkan oleh: ${disturbance.user.name}</small><br><small class="text-muted">${new Date(disturbance.created_at).toLocaleDateString('id-ID')}</small></div>`);
                        markers.push(marker);
                    }
                });
                updateDisturbanceList(data);
            })
            .catch(error => console.error('Error loading disturbances:', error));
    }

    function updateDisturbanceList(disturbances) {
        const list = document.getElementById('disturbanceList');
        list.innerHTML = '';

        if (disturbances.length === 0) {
            list.innerHTML = '<p class="text-muted text-center">Belum ada gangguan yang dilaporkan.</p>';
            return;
        }

        disturbances.forEach(disturbance => {
            const item = document.createElement('div');
            item.className = `disturbance-item ${disturbance.status === 'resolved' ? 'resolved' : ''}`;
            item.innerHTML = `
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <h6 class="mb-1">${disturbance.title}</h6>
                    <p class="mb-2 small">${disturbance.description}</p>
                    <div class="mb-2">
                        <span class="badge bg-${disturbance.status === 'ongoing' ? 'danger' : 'success'} status-badge">${disturbance.status === 'ongoing' ? 'Berlangsung' : 'Selesai'}</span>
                        <span class="badge bg-info type-badge">${getTypeText(disturbance.type)}</span>
                    </div>
                    <small class="text-muted">Oleh: ${disturbance.user.name}</small>
                </div>
                <div class="ms-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="editStatus(${disturbance.id}, '${disturbance.status}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteDisturbance(${disturbance.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
            list.appendChild(item);
        });
    }

    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                map.setView([lat, lng], 15);

                document.getElementById('latitude').value = lat.toFixed(6);
                document.getElementById('longitude').value = lng.toFixed(6);

                if (currentMarker) {
                    map.removeLayer(currentMarker);
                }
                currentMarker = L.marker([lat, lng], {
                    icon: L.divIcon({
                        className: 'custom-div-icon'
                        , html: '<div style="background-color: #007bff; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>'
                        , iconSize: [20, 20]
                        , iconAnchor: [10, 10]
                    })
                }).addTo(map);
            });
        } else {
            alert('Geolocation tidak didukung oleh browser ini.');
        }
    }

    document.getElementById('addDisturbanceForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = Object.fromEntries(formData);

        fetch('/disturbances', {
                method: 'POST'
                , headers: {
                    'Content-Type': 'application/json'
                    , 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
                , body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addDisturbanceModal')).hide();

                    document.getElementById('addDisturbanceForm').reset();

                    if (currentMarker) {
                        map.removeLayer(currentMarker);
                        currentMarker = null;
                    }

                    loadDisturbances();

                    alert(data.message);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan gangguan.');
            });
    });

    function editStatus(id, currentStatus) {
        document.getElementById('editDisturbanceId').value = id;
        document.getElementById('status').value = currentStatus;

        new bootstrap.Modal(document.getElementById('editStatusModal')).show();
    }

    document.getElementById('editStatusForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const id = document.getElementById('editDisturbanceId').value;
        const status = document.getElementById('status').value;

        fetch(`/disturbances/${id}`, {
                method: 'PUT'
                , headers: {
                    'Content-Type': 'application/json'
                    , 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
                , body: JSON.stringify({
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('editStatusModal')).hide();

                    loadDisturbances();

                    alert(data.message);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengupdate status.');
            });
    });

    function deleteDisturbance(id) {
        if (confirm('Apakah Anda yakin ingin menghapus gangguan ini?')) {
            fetch(`/disturbances/${id}`, {
                    method: 'DELETE'
                    , headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadDisturbances();

                        alert(data.message);
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus gangguan.');
                });
        }
    }

    function getIconForType(type) {
        const icons = {
            'road_construction': 'tools'
            , 'traffic_jam': 'car'
            , 'accident': 'exclamation-triangle'
            , 'flood': 'water'
            , 'other': 'exclamation-circle'
        };
        return icons[type] || 'exclamation-circle';
    }

    function getTypeText(type) {
        const types = {
            'road_construction': 'Konstruksi Jalan'
            , 'traffic_jam': 'Macet'
            , 'accident': 'Kecelakaan'
            , 'flood': 'Banjir'
            , 'other': 'Lainnya'
        };
        return types[type] || 'Lainnya';
    }

    function updateLatLngVisibility() {
        const mode = document.getElementById('location_mode').value;
        const latlngRow = document.getElementById('latlng-row');
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        if (mode === 'marker') {
            latlngRow.style.display = '';
            latInput.required = true;
            lngInput.required = true;
        } else {
            latlngRow.style.display = 'none';
            latInput.required = false;
            lngInput.required = false;
            latInput.value = '';
            lngInput.value = '';
        }
    }
    document.getElementById('location_mode').addEventListener('change', updateLatLngVisibility);
    updateLatLngVisibility();

</script>
@endsection
