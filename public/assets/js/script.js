/* ==========================================================================
   script.js — Kiểm tra dữ liệu các form (đăng nhập, đăng ký, liên hệ)
   Các hàm ở đây được gọi từ onsubmit của form: trả về true thì form được gửi
   lên server (Laravel kiểm tra lại lần nữa), false thì chặn lại và báo lỗi.

   Giỏ hàng, menu, animation nằm ở assets/js/cart.js và assets/js/main.js.
   ========================================================================== */

/* --------------------------------------------------------------------------
   Hộp thông báo dùng chung
   -------------------------------------------------------------------------- */
function showAlert(message) {
  var box = document.getElementById("custom-alert");
  var text = document.getElementById("alert-message");

  if (!box || !text) {
    window.alert(message);
    return;
  }

  text.textContent = message;
  box.classList.add("is-open");
}

function closeAlert() {
  var box = document.getElementById("custom-alert");
  if (box) box.classList.remove("is-open");
}

/* Đưa con trỏ về đúng ô bị thiếu và báo lỗi */
function baoLoi(idInput, thongBao) {
  showAlert(thongBao);

  var input = document.getElementById(idInput);
  if (input) input.focus();

  return false;
}

/* --------------------------------------------------------------------------
   Đăng nhập
   -------------------------------------------------------------------------- */
function kiemtraDN() {
  var tendangnhap = document.getElementById("username").value.trim();
  var matkhau = document.getElementById("password").value.trim();

  if (tendangnhap === "") {
    return baoLoi("username", "Hãy nhập tên đăng nhập");
  }

  if (matkhau === "") {
    return baoLoi("password", "Hãy nhập mật khẩu");
  }

  if (matkhau.length < 6) {
    return baoLoi("password", "Mật khẩu phải có ít nhất 6 ký tự");
  }

  return true;
}

/* --------------------------------------------------------------------------
   Đăng ký
   -------------------------------------------------------------------------- */
function kiemtraDK() {
  var tendangnhap = document.getElementById("username").value.trim();
  var matkhau = document.getElementById("password").value.trim();
  var rematkhau = document.getElementById("re_password").value.trim();

  if (tendangnhap === "") {
    return baoLoi("username", "Hãy nhập tên đăng nhập");
  }

  if (tendangnhap.length < 3) {
    return baoLoi("username", "Tên đăng nhập phải có ít nhất 3 ký tự");
  }

  if (matkhau === "") {
    return baoLoi("password", "Hãy nhập mật khẩu");
  }

  if (matkhau.length < 6) {
    return baoLoi("password", "Mật khẩu phải có ít nhất 6 ký tự");
  }

  if (rematkhau === "") {
    return baoLoi("re_password", "Hãy nhập lại mật khẩu");
  }

  if (matkhau !== rematkhau) {
    return baoLoi("re_password", "Mật khẩu nhập lại không khớp");
  }

  return true;
}

/* --------------------------------------------------------------------------
   Liên hệ
   -------------------------------------------------------------------------- */
function kiemtralienhe() {
  var tenkhachhang = document.getElementById("TenKhachHang").value.trim();
  var email = document.getElementById("Email").value.trim();
  var sdt = document.getElementById("SDT").value.trim();
  var noidung = document.getElementById("NoiDung").value.trim();

  var regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  var regexPhone = /^0[0-9]{9}$/;

  if (tenkhachhang === "") {
    return baoLoi("TenKhachHang", "Hãy cho chúng tôi biết tên bạn");
  }

  if (email === "") {
    return baoLoi("Email", "Bạn chưa nhập Email kìa!");
  }

  if (!regexEmail.test(email)) {
    return baoLoi("Email", "Email không hợp lệ rồi");
  }

  if (sdt === "") {
    return baoLoi("SDT", "Nhập thêm số điện thoại nha");
  }

  if (!regexPhone.test(sdt)) {
    return baoLoi("SDT", "Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0");
  }

  if (noidung === "") {
    return baoLoi("NoiDung", "Bạn cần giúp gì nhỉ?");
  }

  return true;
}

/* Bấm ra ngoài hộp thông báo để đóng */
document.addEventListener("click", function (e) {
  var box = document.getElementById("custom-alert");
  if (box && e.target === box) closeAlert();
});
