/* ============================================================
   DIREKTORAT BANDAR UDARA — SHARED SITE BEHAVIOR
   ============================================================ */
(function(){
  'use strict';

  document.addEventListener('DOMContentLoaded', function(){

    /* ---------- Sticky header shadow ---------- */
    var header = document.querySelector('.header');
    if(header){
      window.addEventListener('scroll', function(){
        header.classList.toggle('is-stuck', window.scrollY > 8);
      });
    }

    /* ---------- Mobile nav toggle ---------- */
    var burger = document.querySelector('.burger');
    var navbar = document.querySelector('.navbar');
    if(burger && navbar){
      burger.addEventListener('click', function(){
        navbar.classList.toggle('mobile-open');
        burger.querySelector('i').className = navbar.classList.contains('mobile-open') ? 'bi bi-x-lg' : 'bi bi-list';
      });
      /* accordion-style dropdowns on mobile */
      document.querySelectorAll('.navlist > li').forEach(function(li){
        var link = li.querySelector('a');
        var dd = li.querySelector('.dropdown');
        if(dd && link){
          link.addEventListener('click', function(e){
            if(window.innerWidth <= 900){
              e.preventDefault();
              dd.classList.toggle('open');
            }
          });
        }
      });
    }

    /* ---------- Search box toggle ---------- */
    var searchToggle = document.querySelector('[data-search-toggle]');
    var searchBox = document.querySelector('.search-box');
    if(searchToggle && searchBox){
      searchToggle.addEventListener('click', function(){
        searchBox.classList.toggle('open');
        if(searchBox.classList.contains('open')) searchBox.querySelector('input').focus();
      });
    }

    /* ---------- Language switch (cosmetic) ---------- */
    document.querySelectorAll('.lang-switch button').forEach(function(btn){
      btn.addEventListener('click', function(){
        document.querySelectorAll('.lang-switch button').forEach(function(b){ b.classList.remove('active'); });
        btn.classList.add('active');
      });
    });

    /* ---------- Hero carousel ---------- */
    var heroSlides = document.querySelectorAll('.hero-slide');
    if(heroSlides.length){
      var heroIdx = 0, heroTimer;
      var dotsWrap = document.querySelector('.hero-dots');
      heroSlides.forEach(function(_, i){
        var b = document.createElement('button');
        if(i===0) b.classList.add('active');
        b.addEventListener('click', function(){ goHero(i); });
        dotsWrap && dotsWrap.appendChild(b);
      });
      function goHero(i){
        heroSlides[heroIdx].classList.remove('active');
        dotsWrap && dotsWrap.children[heroIdx].classList.remove('active');
        heroIdx = (i + heroSlides.length) % heroSlides.length;
        heroSlides[heroIdx].classList.add('active');
        dotsWrap && dotsWrap.children[heroIdx].classList.add('active');
      }
      function nextHero(){ goHero(heroIdx+1); }
      function resetTimer(){ clearInterval(heroTimer); heroTimer = setInterval(nextHero, 6000); }
      resetTimer();
      var prevBtn = document.querySelector('.hero-arrow.prev');
      var nextBtn = document.querySelector('.hero-arrow.next');
      prevBtn && prevBtn.addEventListener('click', function(){ goHero(heroIdx-1); resetTimer(); });
      nextBtn && nextBtn.addEventListener('click', function(){ goHero(heroIdx+1); resetTimer(); });
    }

    /* ---------- Animated counters ---------- */
    var counters = document.querySelectorAll('[data-count]');
    if(counters.length && 'IntersectionObserver' in window){
      var obs = new IntersectionObserver(function(entries){
        entries.forEach(function(entry){
          if(entry.isIntersecting){
            animateCount(entry.target);
            obs.unobserve(entry.target);
          }
        });
      }, {threshold:.4});
      counters.forEach(function(c){ obs.observe(c); });
    }
    function animateCount(el){
      var target = parseInt(el.getAttribute('data-count'), 10) || 0;
      var suffix = el.getAttribute('data-suffix') || '';
      var dur = 1400, start = null;
      function step(ts){
        if(!start) start = ts;
        var p = Math.min((ts - start) / dur, 1);
        var eased = 1 - Math.pow(1-p, 3);
        el.textContent = Math.round(eased * target).toLocaleString('id-ID') + suffix;
        if(p < 1) requestAnimationFrame(step);
      }
      requestAnimationFrame(step);
    }

    /* ---------- Tabs ---------- */
    document.querySelectorAll('[data-tabs]').forEach(function(group){
      var buttons = group.querySelectorAll('[data-tab]');
      var id = group.getAttribute('data-tabs');
      var panels = document.querySelectorAll('[data-tab-panel="'+id+'"]');
      buttons.forEach(function(btn){
        btn.addEventListener('click', function(){
          buttons.forEach(function(b){ b.classList.remove('active'); });
          panels.forEach(function(p){ p.classList.remove('active'); });
          btn.classList.add('active');
          var target = document.querySelector('[data-tab-panel="'+id+'"][data-panel-id="'+btn.getAttribute('data-tab')+'"]');
          target && target.classList.add('active');
        });
      });
    });

    /* ---------- Accordion ---------- */
    document.querySelectorAll('.accordion-head').forEach(function(head){
      head.addEventListener('click', function(){
        var item = head.closest('.accordion-item');
        var group = item.closest('[data-accordion-group]');
        if(group){
          group.querySelectorAll('.accordion-item').forEach(function(i){ if(i!==item) i.classList.remove('open'); });
        }
        item.classList.toggle('open');
      });
    });

    /* ---------- FAQ ---------- */
    document.querySelectorAll('.faq-q').forEach(function(q){
      q.addEventListener('click', function(){
        q.closest('.faq-item').classList.toggle('open');
      });
    });

    /* ---------- Org chart interactive units ---------- */
    document.querySelectorAll('.org-unit').forEach(function(unit){
      unit.addEventListener('click', function(){
        document.querySelectorAll('.org-unit').forEach(function(u){ u.classList.remove('active'); });
        document.querySelectorAll('.org-detail').forEach(function(d){ d.classList.remove('show'); });
        unit.classList.add('active');
        var key = unit.getAttribute('data-unit');
        var detail = document.querySelector('.org-detail[data-detail="'+key+'"]');
        if(detail){
          detail.classList.add('show');
          detail.scrollIntoView({behavior:'smooth', block:'nearest'});
        }
      });
    });

    /* ---------- Lightbox gallery ---------- */
    var lightbox = document.querySelector('.lightbox');
    if(lightbox){
      var lbMedia = lightbox.querySelector('.lb-media');
      var lbCap = lightbox.querySelector('.lb-cap');
      var items = Array.prototype.slice.call(document.querySelectorAll('[data-lightbox]'));
      var lbIdx = 0;
      function openLb(i){
        lbIdx = i;
        render();
        lightbox.classList.add('open');
      }
      function render(){
        var item = items[lbIdx];
        var artEl = item.querySelector('.art');
        if(artEl){
          var clone = artEl.cloneNode(true);
          clone.style.height = '480px';
          clone.style.borderRadius = 'var(--r-md)';
          lbMedia.innerHTML = '';
          lbMedia.appendChild(clone);
        } else {
          lbMedia.innerHTML = item.innerHTML;
        }
        lbCap.textContent = item.getAttribute('data-caption') || '';
      }
      items.forEach(function(item, i){
        item.addEventListener('click', function(){ openLb(i); });
      });
      var closeBtn = lightbox.querySelector('.lb-close');
      closeBtn && closeBtn.addEventListener('click', function(){ lightbox.classList.remove('open'); });
      lightbox.addEventListener('click', function(e){ if(e.target === lightbox) lightbox.classList.remove('open'); });
      var prev = lightbox.querySelector('.lb-nav.prev');
      var next = lightbox.querySelector('.lb-nav.next');
      prev && prev.addEventListener('click', function(){ lbIdx = (lbIdx - 1 + items.length) % items.length; render(); });
      next && next.addEventListener('click', function(){ lbIdx = (lbIdx + 1) % items.length; render(); });
      document.addEventListener('keydown', function(e){
        if(!lightbox.classList.contains('open')) return;
        if(e.key === 'Escape') lightbox.classList.remove('open');
        if(e.key === 'ArrowLeft') prev && prev.click();
        if(e.key === 'ArrowRight') next && next.click();
      });
    }

    /* ---------- Regulasi table filter ---------- */
    var regTable = document.querySelector('[data-reg-table]');
    if(regTable){
      var yearSel = document.querySelector('[data-reg-year]');
      var catSel = document.querySelector('[data-reg-cat]');
      var searchInp = document.querySelector('[data-reg-search]');
      var rows = Array.prototype.slice.call(regTable.querySelectorAll('tbody tr'));
      var emptyRow = document.querySelector('[data-reg-empty]');
      function filterRows(){
        var y = yearSel ? yearSel.value : 'all';
        var c = catSel ? catSel.value : 'all';
        var s = searchInp ? searchInp.value.trim().toLowerCase() : '';
        var visibleCount = 0;
        rows.forEach(function(row){
          var matchY = (y === 'all') || row.getAttribute('data-year') === y;
          var matchC = (c === 'all') || row.getAttribute('data-cat') === c;
          var matchS = !s || row.textContent.toLowerCase().indexOf(s) !== -1;
          var show = matchY && matchC && matchS;
          row.style.display = show ? '' : 'none';
          if(show) visibleCount++;
        });
        if(emptyRow) emptyRow.style.display = visibleCount === 0 ? '' : 'none';
      }
      yearSel && yearSel.addEventListener('change', filterRows);
      catSel && catSel.addEventListener('change', filterRows);
      searchInp && searchInp.addEventListener('input', filterRows);
    }

    /* ---------- News archive: filter + client-side pagination ---------- */
    var newsGrid = document.querySelector('[data-news-grid]');
    if(newsGrid){
      var newsCards = Array.prototype.slice.call(newsGrid.querySelectorAll('[data-news-item]'));
      var perPage = 6;
      var newsCatSel = document.querySelector('[data-news-cat]');
      var newsSearch = document.querySelector('[data-news-search]');
      var pager = document.querySelector('[data-news-pager]');
      var page = 1;

      function getFiltered(){
        var c = newsCatSel ? newsCatSel.value : 'all';
        var s = newsSearch ? newsSearch.value.trim().toLowerCase() : '';
        return newsCards.filter(function(card){
          var matchC = (c === 'all') || card.getAttribute('data-cat') === c;
          var matchS = !s || card.textContent.toLowerCase().indexOf(s) !== -1;
          return matchC && matchS;
        });
      }
      function renderNews(){
        var filtered = getFiltered();
        var totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
        if(page > totalPages) page = totalPages;
        newsCards.forEach(function(c){ c.style.display = 'none'; });
        filtered.slice((page-1)*perPage, page*perPage).forEach(function(c){ c.style.display = ''; });
        if(pager){
          pager.innerHTML = '';
          for(var i=1;i<=totalPages;i++){
            var b = document.createElement('button');
            b.textContent = i;
            if(i===page) b.classList.add('active');
            (function(n){ b.addEventListener('click', function(){ page = n; renderNews(); window.scrollTo({top: newsGrid.offsetTop - 120, behavior:'smooth'}); }); })(i);
            pager.appendChild(b);
          }
        }
      }
      newsCatSel && newsCatSel.addEventListener('change', function(){ page = 1; renderNews(); });
      newsSearch && newsSearch.addEventListener('input', function(){ page = 1; renderNews(); });
      renderNews();
    }

    /* ---------- Gallery tabs (Photos/Video/Album) reuse data-tabs above ---------- */

    /* ---------- Floating buttons ---------- */
    var fabTop = document.querySelector('.fab-top');
    if(fabTop){
      window.addEventListener('scroll', function(){
        fabTop.classList.toggle('show', window.scrollY > 500);
      });
      fabTop.addEventListener('click', function(){ window.scrollTo({top:0, behavior:'smooth'}); });
    }

    /* ---------- Contact form (front-end only demo) ---------- */
    var contactForm = document.querySelector('[data-contact-form]');
    if(contactForm){
      contactForm.addEventListener('submit', function(e){
        e.preventDefault();
        var note = document.querySelector('[data-form-note]');
        note && note.classList.add('show');
        contactForm.reset();
        setTimeout(function(){ note && note.classList.remove('show'); }, 5000);
      });
    }

    /* ---------- Newsletter form (demo) ---------- */
    var newsletterForm = document.querySelector('[data-newsletter-form]');
    if(newsletterForm){
      newsletterForm.addEventListener('submit', function(e){
        e.preventDefault();
        var btn = newsletterForm.querySelector('button');
        var original = btn.textContent;
        btn.textContent = 'Berhasil berlangganan';
        setTimeout(function(){ btn.textContent = original; newsletterForm.reset(); }, 2500);
      });
    }

  });
})();
