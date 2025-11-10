<?php
session_start();
require_once '../../config/config.php';

// Redirect if already logged in
if (isAdmin()) {
    header('Location: dashboard.php');
    exit;
}

$pageTitle = 'Admin Login';
include '../../includes/header.php';
?>

<div class="container">
    <div style="max-width: 500px; margin: 3rem auto;">
        <div style="background: white; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--shadow);">
            <h2 style="text-align: center; color: var(--primary-color); margin-bottom: 2rem;">
                <i class="fas fa-user-shield"></i> Admin Login
            </h2>
            
            <form id="loginForm" onsubmit="handleLogin(event)">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color); color: var(--text-light); font-size: 0.9rem;">
                <p><strong>Demo Credentials:</strong></p>
                <p>Username: admin</p>
                <p>Password: admin123</p>
            </div>
        </div>
    </div>
</div>

<script>
async function handleLogin(event) {
    event.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    try {
        const response = await fetch(API_ENDPOINTS.auth, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'login',
                username: username,
                password: password
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Login successful! Redirecting...', 'success');
            setTimeout(() => {
                window.location.href = 'dashboard.php';
            }, 1000);
        } else {
            showToast(data.error || 'Login failed', 'error');
        }
    } catch (error) {
        console.error('Login error:', error);
        showToast('Login failed', 'error');
    }
}
</script>

<?php
include '../../includes/footer.php';
?>
