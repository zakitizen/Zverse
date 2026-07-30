# Database Schema — Zverse.id

**DBMS:** MySQL / MariaDB  
**Database:** `zverse_id`  
**Laravel Version:** 11.x

---

## Entity Relationship Diagram (Logical)

```
┌───────────────────────┐
│        users          │
├───────────────────────┤
│ id (PK)               │──┐
│ username (UQ)          │  │
│ display_name           │  │
│ name                   │  │
│ email (UQ)             │  │
│ password               │  │
│ avatar_color           │  │
│ role                   │  │
│ remember_token         │  │
│ timestamps             │  │
└───────────────────────┘  │
        │                  │
        │ 1                │
        │                  │
        ▼                  │
┌───────────────────────┐  │
│      articles         │  │
├───────────────────────┤  │
│ id (PK)               │  │
│ slug (UQ)             │  │
│ title                  │  │
│ excerpt                │  │
│ content                │  │
│ category               │  │
│ image                  │  │
│ author                 │  │
│ read_time              │  │
│ rating                 │  │
│ featured               │  │
│ tags (JSON)            │  │
│ source                 │  │
│ status                 │  │
│ author_id (FK) ────────┼──┘
│ author_name            │
│ submitted_at           │
│ reviewed_at            │
│ reviewed_by            │
│ review_note            │
│ published_article_id   │──┐ (self-ref)
│ timestamps             │  │
└───────────────────────┘  │
        │                  │
        │ 1                │
        │                  │
        ▼                  │
┌───────────────────────┐  │
│      comments         │  │
├───────────────────────┤  │
│ id (PK)               │  │
│ article_id (FK) ───────┼──┘
│ user_id (FK) ──────────┤
│ parent_id (FK) ────────┤ (self-ref)
│ reply_to_user_id (FK) ─┤
│ author_name            │
│ avatar_color           │
│ content                │
│ body                   │
│ timestamps             │
│ deleted_at (DS)        │
└───────────────────────┘
```

### System Tables (Laravel Built-in)

```
┌───────────────────┐  ┌─────────────────────┐  ┌───────────────────────┐
│ password_reset_   │  │      sessions       │  │        cache          │
│      tokens       │  ├─────────────────────┤  ├───────────────────────┤
├───────────────────┤  │ id (PK)             │  │ key (PK)              │
│ email (PK)        │  │ user_id (FK)        │  │ value                 │
│ token             │  │ ip_address          │  │ expiration            │
│ created_at        │  │ user_agent          │  └───────────────────────┘
└───────────────────┘  │ payload             │
                        │ last_activity       │  ┌───────────────────────┐
                        └─────────────────────┘  │     cache_locks      │
                                                 ├───────────────────────┤
┌───────────────────┐  ┌─────────────────────┐  │ key (PK)              │
│       jobs        │  │    job_batches      │  │ owner                 │
├───────────────────┤  ├─────────────────────┤  │ expiration            │
│ id (PK)           │  │ id (PK)             │  └───────────────────────┘
│ queue             │  │ name                │
│ payload           │  │ total_jobs          │
│ attempts          │  │ pending_jobs        │  ┌───────────────────────┐
│ reserved_at       │  │ failed_jobs         │  │     failed_jobs      │
│ available_at      │  │ failed_job_ids      │  ├───────────────────────┤
│ created_at        │  │ options             │  │ id (PK)              │
└───────────────────┘  │ cancelled_at        │  │ uuid (UQ)            │
                        │ created_at          │  │ connection           │
                        │ finished_at         │  │ queue                │
                        └─────────────────────┘  │ payload              │
                                                  │ exception            │
                                                  │ failed_at            │
                                                  └───────────────────────┘
```

---

## Table: `users`

| Kolom | Tipe | Panjang | Constraint | Default |
|---|---|---|---|---|
| id | BIGINT UNSIGNED | - | PK, AUTO_INCREMENT | - |
| username | VARCHAR | 50 | UNIQUE, NULLABLE | NULL |
| display_name | VARCHAR | 100 | NULLABLE | NULL |
| name | VARCHAR | 255 | NULLABLE | NULL |
| email | VARCHAR | 100 | UNIQUE, NULLABLE | NULL |
| email_verified_at | TIMESTAMP | - | NULLABLE | NULL |
| password | VARCHAR | 255 | NOT NULL | - |
| remember_token | VARCHAR | 100 | NULLABLE | NULL |
| avatar_color | VARCHAR | 60 | NOT NULL | `from-orange-500 to-amber-400` |
| role | VARCHAR | 15 | NOT NULL | `reader` |
| created_at | TIMESTAMP | - | NULLABLE | NULL |
| updated_at | TIMESTAMP | - | NULLABLE | NULL |

**Values role:** `reader`, `pewarta`, `redaksi`

**Relasi:**
- `users.id` → `articles.author_id` (1:N)
- `users.id` → `comments.user_id` (1:N)

---

## Table: `articles`

| Kolom | Tipe | Panjang | Constraint | Default |
|---|---|---|---|---|
| id | BIGINT UNSIGNED | - | PK, AUTO_INCREMENT | - |
| slug | VARCHAR | 500 | UNIQUE, NOT NULL | - |
| title | TEXT | - | NOT NULL | - |
| excerpt | TEXT | 65.535 | NOT NULL | - |
| content | LONGTEXT | 4GB | NOT NULL | - |
| category | VARCHAR | 20 | NOT NULL | - |
| image | VARCHAR | 1000 | NULLABLE | NULL |
| author | VARCHAR | 100 | NOT NULL | - |
| read_time | VARCHAR | 20 | NOT NULL | `5 menit` |
| rating | DECIMAL(3,1) | - | NULLABLE | NULL |
| featured | TINYINT(1) | - | NOT NULL | `0` (false) |
| tags | JSON | - | NULLABLE | NULL |
| source | VARCHAR | 10 | NOT NULL | `seed` |
| status | VARCHAR | 15 | NOT NULL | `draft` |
| author_id | BIGINT UNSIGNED | - | FK → users.id, NULLABLE | NULL |
| author_name | VARCHAR | 100 | NULLABLE | NULL |
| submitted_at | TIMESTAMP | - | NULLABLE | NULL |
| reviewed_at | TIMESTAMP | - | NULLABLE | NULL |
| reviewed_by | VARCHAR | 100 | NULLABLE | NULL |
| review_note | TEXT | 65.535 | NULLABLE | NULL |
| published_article_id | BIGINT UNSIGNED | - | FK → articles.id, NULLABLE | NULL |
| created_at | TIMESTAMP | - | NULLABLE | NULL |
| updated_at | TIMESTAMP | - | NULLABLE | NULL |

**Status workflow:** `draft` → `pending` → `approved` → `published` | `rejected` → `draft` | `published` → `withdrawn`

**Relasi:**
- `articles.author_id` → `users.id` (N:1)
- `articles.published_article_id` → `articles.id` (self-referencing, 1:1)
- `articles.id` → `comments.article_id` (1:N)

---

## Table: `comments`

| Kolom | Tipe | Panjang | Constraint | Default |
|---|---|---|---|---|
| id | BIGINT UNSIGNED | - | PK, AUTO_INCREMENT | - |
| article_id | BIGINT UNSIGNED | - | FK → articles.id, CASCADE DELETE | - |
| user_id | BIGINT UNSIGNED | - | FK → users.id, NULLABLE, SET NULL ON DELETE | NULL |
| parent_id | BIGINT UNSIGNED | - | FK → comments.id, NULLABLE, SET NULL ON DELETE | NULL |
| reply_to_user_id | BIGINT UNSIGNED | - | FK → users.id, NULLABLE, SET NULL ON DELETE | NULL |
| author_name | VARCHAR | 100 | NULLABLE | NULL |
| avatar_color | VARCHAR | 60 | NOT NULL | `from-orange-500 to-amber-400` |
| content | TEXT | 65.535 | NULLABLE | NULL |
| body | TEXT | 65.535 | NULLABLE | NULL |
| created_at | TIMESTAMP | - | NULLABLE | NULL |
| updated_at | TIMESTAMP | - | NULLABLE | NULL |
| deleted_at | TIMESTAMP | - | NULLABLE | NULL |

**Relasi:**
- `comments.article_id` → `articles.id` (N:1)
- `comments.user_id` → `users.id` (N:1, nullable — supports guest comments)
- `comments.parent_id` → `comments.id` (self-referencing, N:1 — nested replies)
- `comments.reply_to_user_id` → `users.id` (N:1 — mention notification target)

---

## Supporting Tables (Laravel Built-in)

### `password_reset_tokens`

| Kolom | Tipe | Panjang | Constraint |
|---|---|---|---|
| email | VARCHAR | 255 | PK |
| token | VARCHAR | 255 | NOT NULL |
| created_at | TIMESTAMP | - | NULLABLE |

### `sessions`

| Kolom | Tipe | Panjang | Constraint |
|---|---|---|---|
| id | VARCHAR | 255 | PK |
| user_id | BIGINT UNSIGNED | - | FK → users.id, NULLABLE, INDEXED |
| ip_address | VARCHAR | 45 | NULLABLE |
| user_agent | TEXT | 65.535 | NULLABLE |
| payload | LONGTEXT | 4GB | NOT NULL |
| last_activity | INT | - | NOT NULL, INDEXED |

### `cache`

| Kolom | Tipe | Panjang | Constraint |
|---|---|---|---|
| key | VARCHAR | 255 | PK |
| value | MEDIUMTEXT | 16MB | NOT NULL |
| expiration | INT | - | NOT NULL, INDEXED |

### `cache_locks`

| Kolom | Tipe | Panjang | Constraint |
|---|---|---|---|
| key | VARCHAR | 255 | PK |
| owner | VARCHAR | 255 | NOT NULL |
| expiration | INT | - | NOT NULL |

### `jobs`

| Kolom | Tipe | Panjang | Constraint |
|---|---|---|---|
| id | BIGINT UNSIGNED | - | PK, AUTO_INCREMENT |
| queue | VARCHAR | 255 | NOT NULL, INDEXED |
| payload | LONGTEXT | 4GB | NOT NULL |
| attempts | TINYINT UNSIGNED | - | NOT NULL |
| reserved_at | INT UNSIGNED | - | NULLABLE |
| available_at | INT UNSIGNED | - | NOT NULL |
| created_at | INT UNSIGNED | - | NOT NULL |

### `job_batches`

| Kolom | Tipe | Panjang | Constraint |
|---|---|---|---|
| id | VARCHAR | 255 | PK |
| name | VARCHAR | 255 | NOT NULL |
| total_jobs | INT | - | NOT NULL |
| pending_jobs | INT | - | NOT NULL |
| failed_jobs | INT | - | NOT NULL |
| failed_job_ids | LONGTEXT | 4GB | NOT NULL |
| options | MEDIUMTEXT | 16MB | NULLABLE |
| cancelled_at | INT | - | NULLABLE |
| created_at | INT | - | NOT NULL |
| finished_at | INT | - | NULLABLE |

### `failed_jobs`

| Kolom | Tipe | Panjang | Constraint |
|---|---|---|---|
| id | BIGINT UNSIGNED | - | PK, AUTO_INCREMENT |
| uuid | VARCHAR | 255 | UNIQUE, NOT NULL |
| connection | TEXT | 65.535 | NOT NULL |
| queue | TEXT | 65.535 | NOT NULL |
| payload | LONGTEXT | 4GB | NOT NULL |
| exception | LONGTEXT | 4GB | NOT NULL |
| failed_at | TIMESTAMP | - | NOT NULL, DEFAULT CURRENT_TIMESTAMP |

---

## Index Summary

| Table | Index | Columns | Type |
|---|---|---|---|
| users | PRIMARY | id | BTREE |
| users | users_username_unique | username | UNIQUE |
| users | users_email_unique | email | UNIQUE |
| articles | PRIMARY | id | BTREE |
| articles | articles_slug_unique | slug | UNIQUE |
| articles | articles_author_id_foreign | author_id | BTREE |
| articles | articles_published_article_id_foreign | published_article_id | BTREE |
| comments | PRIMARY | id | BTREE |
| comments | comments_article_id_foreign | article_id | BTREE |
| comments | comments_user_id_foreign | user_id | BTREE |
| comments | comments_parent_id_foreign | parent_id | BTREE |
| comments | comments_reply_to_user_id_foreign | reply_to_user_id | BTREE |
| sessions | sessions_user_id_index | user_id | BTREE |
| sessions | sessions_last_activity_index | last_activity | BTREE |
| cache | cache_expiration_index | expiration | BTREE |

---

## Column Length Optimization

Following columns were optimized from default VARCHAR(255):

| Table | Column | Before | After | Reason |
|---|---|---|---|---|
| users | username | 255 | **50** | Username maksimal 50 karakter |
| users | display_name | 255 | **100** | Nama tampilan |
| users | avatar_color | 255 | **60** | Hanya class Tailwind CSS |
| users | email | 255 | **100** | 99,9% email < 60 karakter |
| users | role | ENUM | **VARCHAR(15)** | reader/pewarta/redaksi (admin dihapus) |
| articles | title | 255 | **TEXT** | Judul berita bisa sangat panjang |
| articles | slug | 255 | **500** | Slug mengikuti judul panjang |
| articles | read_time | 255 | **20** | Format "5 menit" |
| articles | category | 255 | **20** | Nilai enum: games/musik/film/entertainment |
| articles | image | 255 | **1000** | URL gambar/CDN bisa panjang |
| articles | author | 255 | **100** | Nama penulis |
| articles | author_name | 255 | **100** | Nama penulis (workflow) |
| articles | reviewed_by | 255 | **100** | Nama reviewer |
| articles | source | 255 | **10** | seed/admin |
| articles | status | 255 | **15** | draft/pending/approved/rejected/published/withdrawn |
| comments | author_name | 255 | **100** | Nama komentator |
| comments | avatar_color | 255 | **60** | Class Tailwind CSS |

---

## Seed Data

### Users

| Username | Display Name | Role |
|---|---|---|
| rizky | Rizky Pratama | pewarta |
| raka | Raka Aditya | pewarta |
| indri | Indri Sari | pewarta |
| tegar | Tegar Kusuma | redaksi |
| udin | Udin Saputra | redaksi |
| jaya | Jaya Pratama | redaksi |

### Articles

14 artikel seed dari 4 kategori: Games (4), Musik (4), Film (4), Entertainment (4).
