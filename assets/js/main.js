// main.js — JavaScript dùng chung (rất nhẹ, không cần thư viện ngoài)
document.addEventListener('DOMContentLoaded', function () {
  // Tự ẩn thông báo sau 5 giây
  var alerts = document.querySelectorAll('.alert-success');
  alerts.forEach(function (el) {
    setTimeout(function () { el.style.display = 'none'; }, 5000);
  });

  // Xác nhận trước khi xóa (cho các link có data-confirm)
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      if (!confirm(el.getAttribute('data-confirm'))) {
        e.preventDefault();
      }
    });
  });
});
