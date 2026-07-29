# Ringkasan Tabel Database — NEXUS.id

## Tabel Utama (Aplikasi)

| No | Nama Tabel | Deskripsi | Relasi Utama |
|---|---|---|---|
| 1 | **users** | Menyimpan data pengguna: pewarta (reporter), redaksi (editor), dan pembaca (reader) | 1:N ke articles & comments |
| 2 | **articles** | Menyimpan artikel berita: konten, metadata, status workflow (draft → pending → approved → published) | N:1 ke users, 1:N ke comments |
| 3 | **comments** | Menyimpan komentar & balasan pada artikel, mendukung nested replies dan soft delete | N:1 ke articles & users, self-referencing (parent_id) |

## Tabel Sistem (Laravel)

| No | Nama Tabel | Fungsi |
|---|---|---|
| 4 | **sessions** | Menyimpan sesi login pengguna |
| 5 | **password_reset_tokens** | Menyimpan token reset password |
| 6 | **cache** / **cache_locks** | Menyimpan cache aplikasi |
| 7 | **jobs** / **job_batches** / **failed_jobs** | Antrian job & job yang gagal |

## Ringkasan Kolom Kunci

| Tabel | Primary Key | Foreign Keys | Unique Keys | Soft Delete |
|---|---|---|---|---|
| users | id | - | username, email | - |
| articles | id | author_id → users.id, published_article_id → articles.id | slug | - |
| comments | id | article_id → articles.id, user_id → users.id, parent_id → comments.id, reply_to_user_id → users.id | - | ✅ deleted_at |

## Role Pengguna

| Role | Hak Akses |
|---|---|
| **reader** (pembaca) | Membaca artikel, memberi komentar |
| **pewarta** (reporter) | Menulis & mengirim artikel untuk direview |
| **redaksi** (editor) | Menyetujui/menolak artikel, mempublikasikan |

## Workflow Artikel

```
draft ──→ pending ──→ approved ──→ published
  ↑                      │               │
  └── rejected ←─────────┘               │
                                         ↓
                                    withdrawn
```
