---
description: Panduan Manajemen Siklus Hidup Karyawan (Employee Lifecycle)
---

# Panduan Manajemen Karyawan

Sistem ini menggunakan pendekatan **Siklus Hidup Karyawan** yang memisahkan antara **Data Statis** (Biodata) dan **Data Dinamis** (Karir/Jabatan).

## 1. Input Data Karyawan Baru (Onboarding)
Gunakan form input bertahap (Wizard/Tabs) untuk memudahkan pengisian data yang banyak.

### Tab 1: Identitas Pribadi
- Nama Lengkap, NIK (KTP), Tempat/Tgl Lahir
- Jenis Kelamin, Agama, Status Pernikahan
- Foto Profil

### Tab 2: Kontak & Alamat
- Email, No HP
- Alamat KTP & Domisili
- Kontak Darurat

### Tab 3: Kepegawaian Awal (Kontrak Pertama)
- Cabang, Departemen, Jabatan
- Status Karyawan (Kontrak/Tetap/Probation)
- Tanggal Bergabung
- Gaji Pokok Awal

## 2. Manajemen Karir (Mutasi & Promosi)
**PENTING:** Jangan mengubah jabatan/cabang secara langsung melalui menu "Edit Karyawan". Gunakan fitur **Mutasi / Penetapan**.

### Mengapa?
Agar tercatat **History Karir** karyawan tersebut. Kita bisa melihat rekam jejak:
- Kapan dia dipromosikan?
- Kapan dia pindah cabang?
- Kapan statusnya berubah dari Kontrak ke Tetap?

### Flow Mutasi:
1. Buka Detail Karyawan -> Tab **Karir / History**.
2. Klik tombol **Buat Mutasi / SK Baru**.
3. Isi Form Mutasi:
   - **Jenis**: Promosi, Demosi, Rotasi, Penetapan Karyawan Tetap, Perpanjangan Kontrak.
   - **Tanggal Efektif**: Mulai kapan perubahan ini berlaku.
   - **Posisi Baru**: Pilih Jabatan/Cabang/Departemen baru.
   - **Nomor SK**: Masukkan nomor surat keputusan (opsional).
4. Simpan.
   - Sistem akan otomatis membuat record di tabel `career_histories`.
   - Sistem akan otomatis mengupdate data master di tabel `employees`.

## 3. Resign / Terminasi
Proses keluar juga dicatat sebagai bagian dari history karir dengan tipe "Resignation" atau "Termination".
