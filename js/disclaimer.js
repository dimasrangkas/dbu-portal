class AppDisclaimer extends HTMLElement {
  connectedCallback() {
    this.innerHTML = `
      <div class="disclaimer">
  <div class="container">
    <i class="bi bi-info-circle-fill"></i>
    <span>Disclaimer: Seluruh data, gambar, statistik, dan informasi pada website ini merupakan contoh (dummy data) untuk kebutuhan desain dan pengembangan sistem.</span>
  </div>
</div>
    `;
  }
}

customElements.define('app-disclaimer', AppDisclaimer);
