@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <h2 class="text-center mb-4">Login</h2>
            <div id="alert" class="alert d-none"></div>
            <form id="login-form">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <p class="text-center mt-3">Don't have an account? <a href="{{ route('register') }}">Register</a></p>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('login-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
            };

            try {
                const response = await fetch(`${API_BASE}/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(data),
                });
                const result = await response.json();
                if (result.status) {
                    localStorage.setItem('token', result.token);
                    showAlert('alert', result.message, 'success');
                    // Fetch profile to get dashboard URL
                    const profileResponse = await fetch(`${API_BASE}/profile`, {
                        headers: {
                            'Authorization': `Bearer ${result.token}`,
                            'Accept': 'application/json',
                        },
                    });
                    const profileResult = await profileResponse.json();
                    if (profileResult.status) {
                        window.location.href = profileResult.data.dashboard_url;
                    } else {
                        showAlert('alert', 'Failed to load profile', 'danger');
                    }
                } else {
                    showAlert('alert', result.message || 'Login failed', 'danger');
                }
            } catch (error) {
                showAlert('alert', 'Error connecting to server', 'danger');
            }
        });
    </script>
@endsection