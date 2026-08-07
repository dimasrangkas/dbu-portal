class AppFooter extends HTMLElement {
  connectedCallback() {
    var R = window.SitePaths.root;
    var P = window.SitePaths.page;
    this.innerHTML = `
      <footer class="footer">
  <div class="container footer-top">
    <div class="footer-brand">
      <div class="footer-logos">
        <div class="logo-badge">
          <img src="${R}assets/images/kemenhub.png" alt="Logo Kementerian Perhubungan">
        </div>
        <div class="brand-div"></div>
        <div class="logo-badge small">
          <img src="${R}assets/images/logo-dbu.png" alt="Logo Direktorat Bandar Udara">
        </div>
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
        <li><a href="${P}profil.html">Profil Direktorat</a></li>
        <li><a href="${P}organisasi.html">Struktur Organisasi</a></li>
        <li><a href="${P}regulasi.html">Regulasi</a></li>
        <li><a href="${P}berita.html">Berita Terkini</a></li>
        <li><a href="${P}galeri.html">Galeri</a></li>
      </ul>
    </div>
    <div>
      <h4>Layanan</h4>
      <ul class="footer-links">
        <li><a href="${P}layanan-detail.html">Sertifikasi Bandar Udara</a></li>
        <li><a href="${P}layanan.html">Standardisasi Keselamatan</a></li>
        <li><a href="${P}informasi-publik.html">Informasi Publik</a></li>
        <li><a href="${P}kontak.html">Pengaduan</a></li>
        <li><a href="${P}layanan.html#faq">FAQ</a></li>
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
    <span><a href="#">Kebijakan Privasi</a> &middot; <a href="${P}kontak.html">Peta Situs</a></span>
  </div>
</footer>

<div class="floating-col">
  <a class="fab fab-wa" href="#" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
  <button class="fab fab-top" aria-label="Kembali ke atas"><i class="bi bi-arrow-up"></i></button>
</div>
    `;

    var yearEl = this.querySelector('[data-year]');
    if (yearEl) yearEl.textContent = new Date().getFullYear();
  }
}

customElements.define('app-footer', AppFooter);
