// Login
$(document).on('submit', '#loginForm', function (e) {
  e.preventDefault();
  $.post(base_url + '/login', $(this).serialize(), function (res) {
    if (res.status) {
      setTimeout(() => (window.location.href = res.redirect || base_url), 500);
    } else {
      showToast(res.message, 'error');
    }
  });
});

// Register
$(document).on('submit', '#registerForm', function (e) {
  e.preventDefault();
  $.post(base_url + '/register', $(this).serialize(), function (res) {
    if (res.status) {
      showToast(res.message, 'success');
      setTimeout(() => (window.location.href = res.redirect || '/login'), 1500);
    } else {
      showToast(res.message, 'error');
    }
  });
});

// Forgot Password
$(document).on('submit', '#forgotForm', function (e) {
  e.preventDefault();
  $.post(base_url + '/forgot-password', $(this).serialize(), function (res) {
    showToast(res.message, res.status ? 'success' : 'error');
  });
});

// Reset Password
$(document).on('submit', '#resetForm', function (e) {
  e.preventDefault();
  $.post(base_url + '/reset-password', $(this).serialize(), function (res) {
    if (res.status) {
      showToast(res.message, 'success');
      setTimeout(() => (window.location.href = res.redirect || '/login'), 1500);
    } else {
      showToast(res.message, 'error');
    }
  });
});
