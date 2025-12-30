<<<<<<< HEAD
# Project Management System

Sebuah sistem manajemen proyek berbasis web yang dibangun dengan Laravel untuk mengelola tugas, proyek, dan anggota tim.

## 📋 Fitur Utama

- **Manajemen Pengguna**: 3 role (Admin, Project Manager, Member)
- **Manajemen Proyek**: CRUD proyek, penugasan anggota, tracking progress
- **Manajemen Tugas**: CRUD tugas, update status, tracking deadline
- **Sistem Komentar**: Diskusi pada setiap tugas
- **Link Submission**: Pengumpulan hasil kerja melalui link
- **Kalender**: Visualisasi deadline tugas
- **Dashboard**: Statistik dan overview untuk setiap role

## 🚀 Cara Install & Run

### Prasyarat
- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js & NPM

### Langkah Installasi

1. **Clone Repository**
```bash
git clone https://github.com/username/project-management-system.git
cd project-management-system
```

2. **Install Dependencies**
```bash
composer install
npm install
```

3. **Konfigurasi Environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Konfigurasi Database**
- Buat database di MySQL
- Update file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=username
DB_PASSWORD=password
```

5. **Run Migration & Seeder**
```bash
php artisan migrate --seed
```

6. **Build Assets**
```bash
npm run build
```

7. **Jalankan Server**
```bash
php artisan serve
```

8. **Akses Aplikasi**
- Buka browser: `http://localhost:8000`
- Login dengan user default:
  - Admin: `admin@example.com` / `password`
  - Project Manager: `pm@example.com` / `password`
  - Member: `member@example.com` / `password`

## 🗄️ Struktur Database

### Tabel Utama

#### 1. users
```sql
CREATE TABLE `users` (
  `id` bigint unsigned PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) UNIQUE NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','project_manager','member') NOT NULL DEFAULT 'member',
  `phone` varchar(20) NULL,
  `position` varchar(100) NULL,
  `department` varchar(100) NULL,
  `avatar` varchar(255) NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `last_login_at` timestamp NULL,
  `email_verified_at` timestamp NULL,
  `remember_token` varchar(100) NULL,
  `deleted_at` timestamp NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### 2. projects
```sql
CREATE TABLE `projects` (
  `id` bigint unsigned PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NULL,
  `status` enum('planning','in_progress','on_hold','completed','cancelled') DEFAULT 'planning',
  `progress` int DEFAULT 0,
  `deadline` timestamp NULL,
  `project_manager_id` bigint unsigned NULL,
  `start_date` date NULL,
  `end_date` date NULL,
  `budget` decimal(15,2) NULL,
  `deleted_at` timestamp NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`project_manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
);
```

#### 3. project_members
```sql
CREATE TABLE `project_members` (
  `id` bigint unsigned PRIMARY KEY AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `role` enum('member','lead','observer') DEFAULT 'member',
  `joined_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `project_user_unique` (`project_id`, `user_id`),
  FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);
```

#### 4. tasks
```sql
CREATE TABLE `tasks` (
  `id` bigint unsigned PRIMARY KEY AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NULL,
  `status` enum('todo','in_progress','done') DEFAULT 'todo',
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `progress` int DEFAULT 0,
  `deadline` timestamp NULL,
  `link` varchar(500) NULL,
  `project_id` bigint unsigned NULL,
  `assigned_to` bigint unsigned NULL,
  `completed_at` timestamp NULL,
  `deleted_at` timestamp NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL
);
```

#### 5. task_comments
```sql
CREATE TABLE `task_comments` (
  `id` bigint unsigned PRIMARY KEY AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `content` text NOT NULL,
  `deleted_at` timestamp NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);
```

#### 6. task_link_histories
```sql
CREATE TABLE `task_link_histories` (
  `id` bigint unsigned PRIMARY KEY AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `old_link` varchar(500) NULL,
  `new_link` varchar(500) NOT NULL,
  `notes` text NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);
```

### Diagram Relasi
```
users
├── projects (project_manager_id)
├── tasks (assigned_to)
├── task_comments
├── task_link_histories
└── project_members

projects
├── users (manager)
├── tasks
└── project_members

tasks
├── projects
├── users (assignee)
├── task_comments
└── task_link_histories
```

## 🏗️ Deskripsi Singkat Arsitektur

### Tech Stack
- **Backend**: Laravel 12.x (PHP 8.2+)
- **Frontend**: Bootstrap 5, Blade Templates
- **Database**: MySQL 8.0+
- **Authentication**: Laravel Sanctum
- **Authorization**: Role-based Middleware

### Struktur Aplikasi

#### 1. **Architecture Pattern**: MVC (Model-View-Controller)

#### 2. **Direktori Penting**
```
project-management-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/        # Controller untuk admin
│   │   │   ├── ProjectManager/ # Controller untuk project manager
│   │   │   └── DashboardController.php
│   │   └── Middleware/
│   │       └── CheckRole.php # Middleware authorization
│   ├── Models/               # Eloquent Models
│   └── Providers/            # Service Providers
├── config/                   # Konfigurasi aplikasi
├── database/
│   ├── migrations/          # Database migrations
│   ├── seeders/            # Data dummy
│   └── factories/          # Model factories
├── public/                  # Asset publik
├── resources/
│   ├── views/              # Blade templates
│   │   ├── admin/          # Views untuk admin
│   │   ├── project-manager/ # Views untuk PM
│   │   ├── member/         # Views untuk member
│   │   └── layouts/        # Layout templates
│   └── lang/               # Lokalisasi
├── routes/                  # Route definitions
│   └── web.php             # Web routes
└── storage/                 # Logs, cache, uploads
```

#### 3. **Flow Authentication & Authorization**
```
1. User login → Laravel Auth → CheckRole Middleware
2. Redirect berdasarkan role → Dashboard masing-masing
3. Setiap request → Middleware validasi role
4. Access Control → Berdasarkan permissions masing-masing role
```

#### 4. **Role & Permission**
- **Admin**: Full access, manage users, view all
- **Project Manager**: Manage projects & tasks, assign members
- **Member**: View assigned tasks, update progress, submit work

#### 5. **Key Components**
- **Middleware CheckRole**: Filter akses berdasarkan role
- **Eloquent Relationships**: Complex database relationships
- **Route Groups**: Grouping berdasarkan role
- **Service Layer**: Business logic separation

#### 6. **Security Features**
- CSRF Protection
- XSS Prevention
- SQL Injection Prevention (Eloquent)
- Role-based Access Control
- Password Hashing (bcrypt)
- Session Management

### Workflow Umum
1. **Project Manager** membuat proyek
2. **Project Manager** menambahkan anggota ke proyek
3. **Project Manager** membuat tugas untuk anggota
4. **Member** mengerjakan tugas, update progress
5. **Member** submit hasil kerja via link
6. **Project Manager** memantau progress proyek
7. **Admin** memonitor seluruh sistem

## 📱 Fitur Teknis

### API Endpoints
```
GET    /member/tasks/calendar    # Data kalender tugas
GET    /member/tasks/stats       # Statistik tugas
POST   /tasks/{task}/progress    # Update progress
POST   /tasks/{task}/comments    # Tambah komentar
POST   /tasks/{task}/submit-link # Submit link kerja
```

### Event & Listeners
- Task status changes
- Project progress updates
- User activity logging

### Queue & Jobs
- Email notifications
- Report generation
- Data backups

## 🔧 Development Commands

```bash
# Generate model dengan migration dan controller
php artisan make:model Project -mcr

# Create new migration
php artisan make:migration create_projects_table

# Run migrations
php artisan migrate
php artisan migrate:fresh --seed

# Clear cache
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:clear

# Generate encryption key
php artisan key:generate

# Create storage link
php artisan storage:link
```

## 🧪 Testing

```bash
# Run PHPUnit tests
php artisan test

# Run specific test
php artisan test --filter=UserTest

# Generate test coverage
php artisan test --coverage-html coverage/
```

## 📦 Deployment

### Production Requirements
- SSL Certificate
- Environment: production
- Debug: false
- Queue worker configured
- Task scheduler configured

### Deployment Steps
1. Clone repository di server
2. Setup environment variables
3. Install dependencies (`composer install --no-dev`)
4. Build assets (`npm run production`)
5. Run migrations (`php artisan migrate --force`)
6. Setup cron job untuk scheduler
7. Configure web server (Nginx/Apache)
8. Setup queue worker (Supervisor)

## 🚨 Troubleshooting

### Common Issues

1. **Permission denied on storage**
```bash
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache
```

2. **Class not found**
```bash
composer dump-autoload
```

3. **Migration errors**
```bash
php artisan migrate:fresh
```

4. **Route not found**
```bash
php artisan route:clear
```

## 📄 License

Proprietary - All rights reserved

## 👥 Contributors

- [Nama Developer] - Initial work
- [Nama Developer] - Features & improvements

## 🤝 Support

Untuk support atau pertanyaan:
1. Check [Issues](https://github.com/username/project-management-system/issues)
2. Email: support@company.com
3. Documentation: [Wiki](https://github.com/username/project-management-system/wiki)

---

**Version**: 1.0.0  
**Last Updated**: December 2024  
**Maintained by**: IT Department
=======
# manajemen_proyek
manajemen proyek internal PT. INDONESIA GADAI OKE
>>>>>>> 40a7b7542f893ffd7cb2cb6f786080a73d309885
