@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <h2 class="text-center mb-4">Register</h2>
            <div id="alert" class="alert d-none"></div>
            <form id="register-form">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" required>
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>
            <p class="text-center mt-3">Already have an account? <a href="{{ route('login') }}">Login</a></p>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('register-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('password_confirmation').value,
            };

            try {
                const response = await fetch(`${API_BASE}/register`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(data),
                });
                const result = await response.json();
                if (result.status) {
                    showAlert('alert', result.message, 'success');
                    setTimeout(() => (window.location.href = '{{ route('login') }}'), 1000);
                } else {
                    showAlert('alert', result.message || 'Registration failed', 'danger');
                }
            } catch (error) {
                showAlert('alert', 'Error connecting to server', 'danger');
            }
        });
    </script>
@endsection