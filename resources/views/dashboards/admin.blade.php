@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <header class="d-flex justify-content-between align-items-center py-3">
        <h3>Admin Dashboard</h3>
        <button id="logout" class="btn btn-outline-danger">Logout</button>
    </header>
    <div id="alert" class="alert d-none"></div>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Welcome, <span id="admin-name"></span>!</h5>
            <p><strong>Email:</strong> <span id="admin-email"></span></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text fs-3" id="total-users">0</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Products</h5>
                    <p class="card-text fs-3" id="total-products">0</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        async function loadAdminDashboard() {
            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = '{{ route('login') }}';
                return;
            }

            try {
                const response = await fetch(`${API_BASE}/profile`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                    },
                });
                const result = await response.json();
                if (result.status) {
                    document.getElementById('admin-name').textContent = result.data.user.name;
                    document.getElementById('admin-email').textContent = result.data.user.email;
                    document.getElementById('total-users').textContent = result.data.admin_stats.total_users;
                    document.getElementById('total-products').textContent = result.data.admin_stats.total_products || 0;
                } else {
                    showAlert('alert', result.message || 'Failed to load dashboard', 'danger');
                    localStorage.removeItem('token');
                    window.location.href = '{{ route('login') }}';
                }
            } catch (error) {
                showAlert('alert', 'Error connecting to server', 'danger');
            }
        }

        document.getElementById('logout').addEventListener('click', () => {
            localStorage.removeItem('token');
            window.location.href = '{{ route('login') }}';
        });

        loadAdminDashboard();
    </script>
@endsection