-- ============================================================
--  MIGRASI: Kepala Tim pada halaman Organisasi + penataan navbar
--  1. Kolom pejabat kedua (Kepala Tim) pada tabel org_units.
--  2. Menu "Regulasi" di baris utama navbar dinonaktifkan
--     (tautannya tetap ada di mega menu Informasi Publik).
--  3. Label kelompok mega menu "Data per Subdirektorat" -> "Subdirektorat".
--  Aman dijalankan pada basis data yang sudah berisi data.
--  Jalankan: mysql -u root -h 127.0.0.1 dbu_cms < database/migrate-organisasi-navbar.sql
-- ============================================================
SET NAMES utf8mb4;
-- USE `dbu_cms`;   <- nama basis data di server sering berbeda.
--                    Pilih basis datanya saat menjalankan, contoh:
--                    mysql -u USER -p NAMA_DB < migrate-organisasi-navbar.sql

-- ---------- 1. Pejabat kedua: Kepala Tim subdirektorat ----------
ALTER TABLE `org_units`
  ADD COLUMN IF NOT EXISTS `team_lead_name`     VARCHAR(160) NULL AFTER `head_photo`,
  ADD COLUMN IF NOT EXISTS `team_lead_position` VARCHAR(160) NULL AFTER `team_lead_name`,
  ADD COLUMN IF NOT EXISTS `team_lead_photo`    VARCHAR(255) NULL AFTER `team_lead_position`;

-- Jabatan bawaan untuk unit yang memang punya pejabat tunggal (bukan unit fungsional).
UPDATE `org_units`
   SET `team_lead_position` = 'Kepala Tim'
 WHERE `unit_key` <> 'tu'
   AND (`team_lead_position` IS NULL OR `team_lead_position` = '');

-- ---------- 2. Navbar: hilangkan menu utama "Regulasi" ----------
UPDATE `menu_items`
   SET `is_active` = 0
 WHERE `location` = 'navbar'
   AND `parent_id` IS NULL
   AND `url` = 'pages/regulasi';

-- ---------- 3. Label kelompok mega menu Informasi Publik ----------
UPDATE `menu_items`
   SET `group_label` = 'Subdirektorat'
 WHERE `location` = 'navbar'
   AND `group_label` = 'Data per Subdirektorat';
