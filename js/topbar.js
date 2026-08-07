class AppTopbar extends HTMLElement {
  connectedCallback() {
    this.innerHTML = `
      <div class="topbar">
  <div class="container">
    <div class="topbar-left">
      <span class="hide-sm"><i class="bi bi-geo-alt"></i> Jakarta, Indonesia</span>
      <span class="hide-sm"><i class="bi bi-envelope"></i> bandarudara@dephub.go.id</span>
    </div>
    <div class="topbar-right">
      <div class="social-mini">
        <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
        <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
        <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
        <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
      </div>
      <div class="lang-switch"><button class="active">ID</button><span>/</span><button>EN</button></div>
    </div>
  </div>
</div>
    `;
  }
}

customElements.define('app-topbar', AppTopbar);
