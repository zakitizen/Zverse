# NEXUS.id — Laravel

Portal entertainment Indonesia. Dikonversi dari React + Vite ke **Laravel 11 + Blade + Tailwind CDN**.

---

## ⚡ Instalasi Cepat

```bash
# 1. Clone / extract project
cd nexus-laravel

# 2. Install dependencies
composer install

# 3. Setup .env
cp .env.example .env
php artisan key:generate

# 4. Database (SQLite default — tidak perlu setup apapun)
touch database/database.sqlite
php artisan migrate --seed

# 5. Storage link (untuk upload gambar)
php artisan storage:link

# 6. Jalankan!
php artisan serve
```

Buka: **http://localhost:8000**

---

## 🗄️ Pakai MySQL (opsional)

Di `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nexus_id
DB_USERNAME=root
DB_PASSWORD=your_password
```

Lalu:
```bash
php artisan migrate --seed
```

---

## 🔑 Akun Default (setelah seed)

| Role    | Username | Password    | URL                   |
|---------|----------|-------------|-----------------------|
| Admin   | —        | `nexus2026` | `/admin/login`        |
| Pewarta | `rizky`  | `pewarta123`| `/pewarta/login`      |
| Redaksi | `dian`   | `redaksi123`| `/redaksi/login`      |
| Reader  | Daftar sendiri | —   | `/login`              |

---

## 📁 Struktur Project

```
nexus-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── HomeController.php
│   │   │   ├── ArticleController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── SearchController.php
│   │   │   ├── AuthController.php       ← Reader login/register
│   │   │   ├── AdminController.php      ← Admin panel
│   │   │   ├── PewartaController.php    ← Reporter portal
│   │   │   └── RedaksiController.php    ← Editor portal
│   │   └── Middleware/
│   │       ├── AdminMiddleware.php
│   │       ├── PewartaMiddleware.php
│   │       └── RedaksiMiddleware.php
│   └── Models/
│       ├── Article.php
│       ├── Comment.php
│       ├── User.php
│       └── WorkflowArticle.php
├── database/
│   ├── migrations/
│   └── seeders/DatabaseSeeder.php
├── resources/views/
│   ├── layouts/
│   │   ├── app.blade.php               ← Layout publik
│   │   └── admin.blade.php             ← Layout admin
│   ├── components/
│   │   ├── navbar.blade.php
│   │   ├── footer.blade.php
│   │   └── article-card.blade.php
│   ├── pages/
│   │   ├── home.blade.php
│   │   ├── article.blade.php
│   │   ├── category.blade.php
│   │   ├── search.blade.php
│   ├── auth/login.blade.php
│   ├── admin/
│   │   ├── login.blade.php
│   │   ├── dashboard.blade.php
│   │   └── article-form.blade.php
│   ├── pewarta/
│   │   ├── login.blade.php
│   │   ├── dashboard.blade.php
│   │   └── article-form.blade.php
│   └── redaksi/
│       ├── login.blade.php
│       └── dashboard.blade.php
└── routes/web.php
```

---

## ✨ Fitur

### Publik
- ✅ Beranda dengan carousel hero + artikel per kategori
- ✅ Halaman detail artikel dengan konten Markdown-like
- ✅ Like artikel (AJAX)
- ✅ Komentar (login wajib)
- ✅ Halaman kategori (Games, Musik, Film, Entertainment)
- ✅ Search artikel
- ✅ Login / Register reader

### Admin
- ✅ Dashboard statistik
- ✅ CRUD artikel (buat, edit, hapus)
- ✅ Upload gambar
- ✅ Featured artikel

### Workflow Editorial
- ✅ **Pewarta** bisa tulis draft dan submit ke redaksi
- ✅ **Redaksi** bisa approve/reject/terbitkan artikel
- ✅ Status: draft → pending → approved/rejected → published
- ✅ Note/alasan penolakan

---

## 🛠️ Tech Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Database**: SQLite (default) atau MySQL
- **Frontend**: Blade Templates + Tailwind CSS (via CDN)
- **Auth**: Laravel Auth + Session-based role auth
