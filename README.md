# E-Rapor Viewer - Laravel + Filament

Aplikasi viewer untuk database E-Rapor SMA menggunakan Laravel dan Filament dengan role-based access control.

## 🚀 Status Implementasi

✅ **SELESAI:**
- Laravel 11 project setup
- Filament v4.3 installation
- Database connection ke PostgreSQL (port 54945)
- Role-based authentication (Admin & Guru panels)
- Models untuk tabel utama (Siswa, Ptk, Kelas, dll)
- Resources untuk Admin panel
- Laravel system tables (sessions, cache, jobs)

## 🔐 Login Information

### Credentials untuk semua user:
- **Password:** `@dikdasmen123456*` (untuk semua user)

### Admin Users:
- **User ID:** `silmi` → Silmi Faris (Level: Admin)
- **User ID:** `administrator` → Administrator (Level: Admin)

### Guru Users (contoh):
- **User ID:** `199404162024212033` → Revi Indika (Level: Guru)
- **User ID:** `aamamarulloh` → Aam Amarulloh (Level: Guru)
- **User ID:** `riniidawati` → Rini Idawati (Level: Guru)
- Dan 50+ guru lainnya...

*Note: Form login menggunakan field "User ID" untuk menghindari validasi email*

## 🌐 Access URLs

- **Admin Panel:** http://127.0.0.1:8000/admin
- **Guru Panel:** http://127.0.0.1:8000/guru

## 📊 Database Info

- **Host:** localhost
- **Port:** 54945
- **Database:** db_neweraporsma
- **Username:** postgres
- **Password:** (trust authentication)
- **Total Tables:** 53 tables

## 🏗️ Struktur Project

```
erapor-viewer/
├── app/
│   ├── Models/
│   │   ├── Siswa.php
│   │   ├── Ptk.php
│   │   ├── UserLogin.php
│   │   ├── Kelas.php
│   │   ├── Mapel.php
│   │   └── ...
│   ├── Filament/
│   │   ├── Resources/        # Admin resources
│   │   └── Guru/Resources/   # Guru resources
│   └── Http/Middleware/
│       └── RoleMiddleware.php
├── app/Providers/Filament/
│   ├── AdminPanelProvider.php
│   └── GuruPanelProvider.php
└── ...
```

## 🔧 Cara Menjalankan

1. **Start PostgreSQL** (sudah otomatis jalan)
2. **Start Laravel server:**
   ```bash
   cd erapor-viewer
   php artisan serve
   ```
3. **Login ke aplikasi:**
   - **Admin Panel:** http://127.0.0.1:8000/admin/login
     - User ID: `silmi`
     - Password: `@dikdasmen123456*`
   - **Guru Panel:** http://127.0.0.1:8000/guru/login  
     - User ID: `199404162024212033` (atau userid guru lain)
     - Password: `@dikdasmen123456*`

## 📋 Fitur yang Tersedia

### Admin Panel:
- ✅ Full access ke semua data
- ✅ CRUD Siswa, PTK, Kelas
- ✅ User management
- 🔄 Resources untuk 53 tabel (dalam progress)

### Guru Panel:
- ✅ Limited access sesuai role
- 🔄 View siswa di kelas yang diajar
- 🔄 Input nilai dan assessment

## 🎯 Next Steps

1. **Complete Resources:** Buat resources untuk semua 53 tabel
2. **Guru Restrictions:** Implementasi pembatasan data guru sesuai kelas yang diajar
3. **Dashboard Widgets:** Statistik dan grafik
4. **PDF Export:** Integrasi dengan sistem cetak rapor
5. **Custom Authentication:** Implementasi password verification untuk sistem hash yang ada

## 🗂️ Tabel Database Utama

- `tabel_siswa` - Data siswa
- `tabel_ptk` - Data guru/staff
- `user_login` - User authentication
- `tabel_kelas` - Data kelas
- `tabel_mapel` - Mata pelajaran
- `tabel_pembelajaran` - Relasi guru-mapel-kelas
- `tabel_nilai*` - Berbagai jenis nilai
- `tabel_kehadiran` - Data absensi

## 💡 Catatan

- Database menggunakan existing data tanpa migration
- Authentication menggunakan tabel `user_login` yang sudah ada
- Role-based access: Admin (full access) vs Guru (limited access)
- Password system menggunakan hash kompleks dari aplikasi asli