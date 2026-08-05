class AppFooter extends HTMLElement {
  connectedCallback() {
    this.innerHTML = `
      <footer class="footer">
  <div class="container footer-top">
    <div class="footer-brand">
      <div class="flex items-center gap-12">
        <div class="logo-badge small"><i class="bi bi-airplane-engines" style="font-size:15px"></i></div>
        <div class="name" style="color:#fff; font-size:15px;">Direktorat Bandar Udara</div>
      </div>
      <p>Direktorat Jenderal Perhubungan Udara, Kementerian Perhubungan Republik Indonesia. Mewujudkan
      penyelenggaraan bandar udara yang profesional, aman, dan berkelanjutan.</p>
      <div class="footer-social">
        <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
        <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
        <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
        <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
      </div>
    </div>
    <div>
      <h4>Tautan Cepat</h4>
      <ul class="footer-links">
        <li><a href="profil.html">Profil Direktorat</a></li>
        <li><a href="organisasi.html">Struktur Organisasi</a></li>
        <li><a href="regulasi.html">Regulasi</a></li>
        <li><a href="berita.html">Berita Terkini</a></li>
        <li><a href="galeri.html">Galeri</a></li>
      </ul>
    </div>
    <div>
      <h4>Layanan</h4>
      <ul class="footer-links">
        <li><a href="layanan-detail.html">Sertifikasi Bandar Udara</a></li>
        <li><a href="layanan.html">Standardisasi Keselamatan</a></li>
        <li><a href="informasi-publik.html">Informasi Publik</a></li>
        <li><a href="kontak.html">Pengaduan</a></li>
        <li><a href="layanan.html#faq">FAQ</a></li>
      </ul>
    </div>
    <div>
      <h4>Kontak Kami</h4>
      <ul class="footer-contact">
        <li><i class="bi bi-geo-alt-fill"></i><span>Jl. Medan Merdeka Barat No. 8, Jakarta Pusat 10110</span></li>
        <li><i class="bi bi-telephone-fill"></i><span>(021) 350-0000</span></li>
        <li><i class="bi bi-envelope-fill"></i><span>bandarudara@dephub.go.id</span></li>
        <li><i class="bi bi-clock-fill"></i><span>Senin–Jumat, 08.00–16.00 WIB</span></li>
      </ul>
    </div>
  </div>
  <div class="container footer-bottom">
    <span>&copy; <span data-year>2026</span> Direktorat Bandar Udara — Direktorat Jenderal Perhubungan Udara. Hak cipta dilindungi.</span>
    <span><a href="#">Kebijakan Privasi</a> &middot; <a href="kontak.html">Peta Situs</a></span>
  </div>
</footer>

<div class="floating-col">
  <a class="fab fab-wa" href="#" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
  <button class="fab fab-top" aria-label="Kembali ke atas"><i class="bi bi-arrow-up"></i></button>
</div>
    `;

    var yearEl = this.querySelector('[data-year]');
    if(yearEl) yearEl.textContent = new Date().getFullYear();
  }
}

customElements.define('app-footer', AppFooter);
