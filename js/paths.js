/* ============================================================
   DIREKTORAT BANDAR UDARA — SHARED PATH HELPER
   Files live either at the site root (index.html) or inside
   /pages/. Components render the same markup for both, so links
   and asset URLs are prefixed at runtime from here.
   ============================================================ */
(function (global) {
  var inPages = /\/pages\//.test(location.pathname);

  global.SitePaths = {
    /* prefix to reach the site root (index.html, assets/, css/, js/) */
    root: inPages ? '../' : '',
    /* prefix to reach a page inside /pages/ */
    page: inPages ? '' : 'pages/'
  };
})(window);
