<?php
/* ============================================================
   DIREKTORAT BANDAR UDARA — KONFIGURASI BERSAMA
   Dipakai oleh folder /frontend dan /admin.
   ============================================================ */

/* ---- Basis data (XAMPP default: root tanpa kata sandi) ---- */
define('DB_HOST', getenv('DBU_DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DBU_DB_PORT') ?: '3306');
define('DB_NAME', getenv('DBU_DB_NAME') ?: 'dbu_cms');
define('DB_USER', getenv('DBU_DB_USER') ?: 'root');
define('DB_PASS', getenv('DBU_DB_PASS') !== false ? getenv('DBU_DB_PASS') : '');

/* ---- Path fisik ---- */
define('BASE_PATH',     dirname(__DIR__));
define('FRONTEND_PATH', BASE_PATH . '/frontend');
define('ADMIN_PATH',    BASE_PATH . '/admin');
define('UPLOAD_PATH',   BASE_PATH . '/uploads');

/* ---- URL dasar (relatif terhadap document root project) ---- */
define('BASE_URL',     rtrim(getenv('DBU_BASE_URL') ?: '', '/'));
define('FRONTEND_URL', BASE_URL . '/frontend');
define('ADMIN_URL',    BASE_URL . '/admin');
define('UPLOAD_URL',   BASE_URL . '/uploads');

/* ---- Unggahan ---- */
define('UPLOAD_MAX_BYTES', 8 * 1024 * 1024);
const UPLOAD_ALLOWED_IMAGE = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
const UPLOAD_ALLOWED_FILE  = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip'];

/* ---- Sesi ---- */
define('SESSION_NAME', 'dbu_cms_session');

/* ---- Zona waktu & tampilan galat ---- */
date_default_timezone_set('Asia/Jakarta');
define('APP_DEBUG', getenv('DBU_DEBUG') === '1');
error_reporting(APP_DEBUG ? E_ALL : E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', APP_DEBUG ? '1' : '0');
