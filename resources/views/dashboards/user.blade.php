@extends('layouts.app')

@section('title', 'User Dashboard')

@section('content')
    <header class="d-flex justify-content-between align-items-center py-3">
        <h3>User Dashboard</h3>
        <button id="logout" class="btn btn-outline-danger">Logout</button>
    </header>
    <div id="alert" class="alert d-none"></div>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Welcome, <span id="user-name"></span>!</h5>
            <p><strong>Email:</strong> <span id="user-email"></span></p>
            <p><strong>Account Created:</strong> <span id="created-at"></span></p>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        async function loadUserDashboard() {
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
                    document.getElementById('user-name').textContent = result.data.user.name;
                    document.getElementById('user-email').textContent = result.data.user.email;
                    document.getElementById('created-at').textContent = result.data.user_details.created_at;
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

        loadUserDashboard();
    </script>
@endsection