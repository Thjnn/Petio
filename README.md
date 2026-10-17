# Petio — Website bán đồ thú cưng + spa (Laravel 13)

Đồ án cuối kỳ — Cao Phúc Thịnh (023101057).

## Chạy trên Laragon

```bash
composer install
cp .env.example .env        # DB mặc định: MySQL `petio`, user root, không mật khẩu
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve           # hoặc mở http://petio.test (Laragon trỏ vào thư mục public/)
```

Tài khoản admin mẫu: `admin` / `Admin@12345` — **đổi ngay trước khi đưa lên mạng**.

Mã giảm giá mẫu: `FIRST35` (đơn đầu tiên, cần đăng nhập), `NEWS10`, `BIG25` (đơn từ 1.000.000đ).

Chạy test: `php artisan test`

## Cấu trúc

| Thư mục | Nội dung |
|---|---|
| `routes/web.php` | Toàn bộ route |
| `app/Http/Controllers` | Trang chủ, cửa hàng, tin tức, giỏ hàng, thanh toán, liên hệ, đăng nhập |
| `app/Support/Cart.php` | Giỏ hàng lưu trong session (giá luôn đọc lại từ DB) |
| `app/Models` | Eloquent model, một model cho mỗi bảng |
| `database/migrations` | Lược đồ CSDL (chuyển từ `petio_schema.sql`) |
| `database/seeders` | Dữ liệu mẫu (chuyển từ `petio_seed.sql`) |
| `resources/views` | Blade: `layouts/app` + `partials/` dùng chung, mỗi trang một view |
| `public/assets` | CSS, JS, ảnh của giao diện cũ (giữ nguyên, không cần build) |
