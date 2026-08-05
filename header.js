class AppHeader extends HTMLElement {
    connectedCallback() {
      this.innerHTML = `
        <header class="header">
  <div class="container brandrow">
    <a href="index.html" class="brand">
      <div class="brand-logos">
        <div class="logo-badge">
            <img src="assets/images/kemenhub.png" alt="Logo 1">
        </div>
    
        <div class="brand-div"></div>
    
        <div class="logo-badge small">
            <img src="assets/images/logo-dbu.png" alt="Logo 2">
        </div>
    </div>
      <div class="brand-text">
        <div class="kop">Kementerian Perhubungan Republik Indonesia</div>
        <div class="name">Direktorat <span>Bandar Udara</span></div>
      </div>
    </a>
    <div class="header-actions">
      <div class="search-box">
        <input type="text" placeholder="Cari informasi...">
      </div>
      <button class="icon-btn" data-search-toggle aria-label="Cari"><i class="bi bi-search"></i></button>
      <button class="icon-btn burger" aria-label="Menu"><i class="bi bi-list"></i></button>
    </div>
  </div>
</header>

        `;
    }
  }
  
  customElements.define("app-header", AppHeader);