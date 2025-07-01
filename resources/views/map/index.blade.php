@extends('layouts.app')

@section('title', 'Peta Gangguan - Road Incident Map')

@section('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="number" class="form-control" id="latitude" name="latitude" step="any" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="number" class="form-control" id="longitude" name="longitude" step="any" required>
                            </div>
                        </div>
                    </div>

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
<script>
    let map;
    let markers = [];
    let currentMarker;

    // Initialize map
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize map centered on Indonesia
        map = L.map('map').setView([-2.5489, 118.0149], 5);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Load disturbances
        loadDisturbances();

        // Add click event to map
        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;

            // Update form fields
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);

            // Show marker on map
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
    });

    // Load disturbances from server
    function loadDisturbances() {
        fetch('/disturbances')
            .then(response => response.json())
            .then(data => {
                // Clear existing markers
                markers.forEach(marker => map.removeLayer(marker));
                markers = [];

                // Add markers to map
                data.forEach(disturbance => {
                    const marker = L.marker([disturbance.latitude, disturbance.longitude], {
                        icon: L.divIcon({
                            className: 'custom-div-icon'
                            , html: `<div style="background-color: ${disturbance.status === 'ongoing' ? '#dc3545' : '#28a745'}; width: 25px; height: 25px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white; font-size: 12px;">
                            <i class="fas fa-${getIconForType(disturbance.type)}"></i>
                        </div>`
                            , iconSize: [25, 25]
                            , iconAnchor: [12, 12]
                        })
                    }).addTo(map);

                    // Add popup
                    marker.bindPopup(`
                    <div style="min-width: 200px;">
                        <h6>${disturbance.title}</h6>
                        <p class="mb-2">${disturbance.description}</p>
                        <div class="mb-2">
                            <span class="badge bg-${disturbance.status === 'ongoing' ? 'danger' : 'success'}">${disturbance.status === 'ongoing' ? 'Berlangsung' : 'Selesai'}</span>
                            <span class="badge bg-info">${getTypeText(disturbance.type)}</span>
                        </div>
                        <small class="text-muted">Dilaporkan oleh: ${disturbance.user.name}</small>
                        <br>
                        <small class="text-muted">${new Date(disturbance.created_at).toLocaleDateString('id-ID')}</small>
                    </div>
                `);

                    markers.push(marker);
                });

                // Update list
                updateDisturbanceList(data);
            })
            .catch(error => console.error('Error loading disturbances:', error));
    }

    // Update disturbance list
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

    // Get current location
    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                map.setView([lat, lng], 15);

                // Update form fields
                document.getElementById('latitude').value = lat.toFixed(6);
                document.getElementById('longitude').value = lng.toFixed(6);

                // Show marker
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

    // Add disturbance form submission
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
                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById('addDisturbanceModal')).hide();

                    // Reset form
                    document.getElementById('addDisturbanceForm').reset();

                    // Remove current marker
                    if (currentMarker) {
                        map.removeLayer(currentMarker);
                        currentMarker = null;
                    }

                    // Reload disturbances
                    loadDisturbances();

                    // Show success message
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

    // Edit status
    function editStatus(id, currentStatus) {
        document.getElementById('editDisturbanceId').value = id;
        document.getElementById('status').value = currentStatus;

        new bootstrap.Modal(document.getElementById('editStatusModal')).show();
    }

    // Edit status form submission
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
                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById('editStatusModal')).hide();

                    // Reload disturbances
                    loadDisturbances();

                    // Show success message
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

    // Delete disturbance
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
                        // Reload disturbances
                        loadDisturbances();

                        // Show success message
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

    // Helper functions
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

</script>
@endsection
