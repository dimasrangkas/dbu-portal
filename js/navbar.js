class AppNavbar extends HTMLElement {
  connectedCallback() {
    var R = window.SitePaths.root;
    var P = window.SitePaths.page;
    this.innerHTML = `
      <nav class="navbar">
  <div class="container">
    <ul class="navlist">
      <li data-match="index.html"><a href="${R}index.html">Beranda</a></li>
      <li data-match="profil.html"><a href="${P}profil.html">Profil <i class="bi bi-chevron-down" style="font-size:11px"></i></a>
        <div class="dropdown">
          <a href="${P}profil.html#tentang">Tentang Direktorat</a>
          <a href="${P}profil.html#sejarah">Sejarah</a>
          <a href="${P}profil.html#visi-misi">Visi &amp; Misi</a>
          <a href="${P}profil.html#nilai">Nilai Organisasi</a>
        </div>
      </li>
      <li data-match="organisasi.html"><a href="${P}organisasi.html">Organisasi</a></li>
      <li data-match="tugas-fungsi.html"><a href="${P}tugas-fungsi.html">Tugas &amp; Fungsi</a></li>
      <li data-match="layanan.html layanan-detail.html"><a href="${P}layanan.html">Layanan <i class="bi bi-chevron-down" style="font-size:11px"></i></a>
        <div class="dropdown">
          <a href="${P}layanan.html">Layanan Publik</a>
          <a href="${P}layanan.html#online">Layanan Daring</a>
          <a href="${P}layanan.html#faq">FAQ</a>
        </div>
      </li>
      <li data-match="informasi-publik.html informasi-publik-detail.html"><a href="${P}informasi-publik.html">Informasi Publik</a></li>
      <li data-match="regulasi.html regulasi-detail.html"><a href="${P}regulasi.html">Regulasi</a></li>
      <li data-match="berita.html berita-detail.html"><a href="${P}berita.html">Berita</a></li>
      <li data-match="galeri.html galeri-detail.html"><a href="${P}galeri.html">Galeri</a></li>
      <li data-match="kontak.html"><a href="${P}kontak.html">Kontak</a></li>
      <li><a href="https://dbu.edifly-dev.com/kemhub-ems-dev/">Portal AEMS</a></li>
    </ul>
  </div>
</nav>
    `;

    var page = location.pathname.split('/').pop() || 'index.html';
    this.querySelectorAll('.navlist > li[data-match]').forEach(function(li){
      var pages = li.getAttribute('data-match').split(' ');
      if(pages.indexOf(page) !== -1) li.classList.add('active');
    });
  }
}

customElements.define('app-navbar', AppNavbar);
