document.addEventListener('DOMContentLoaded',()=>{
 const nav=document.querySelector('.site-nav'); const topBtn=document.querySelector('.backtop');
 const onScroll=()=>{if(nav&&!nav.classList.contains('inner'))nav.classList.toggle('scrolled',scrollY>20); if(topBtn)topBtn.classList.toggle('show',scrollY>500)}; onScroll(); addEventListener('scroll',onScroll,{passive:true});
 const toggle=document.querySelector('.mobile-toggle'),panel=document.querySelector('.mobile-panel'),overlay=document.querySelector('.mobile-overlay'),close=document.querySelector('.mobile-close');
 const openMenu=()=>{panel?.classList.add('open');overlay?.classList.add('open');document.body.classList.add('menu-open');toggle?.setAttribute('aria-expanded','true')};
 const closeMenu=()=>{panel?.classList.remove('open');overlay?.classList.remove('open');document.body.classList.remove('menu-open');toggle?.setAttribute('aria-expanded','false')};
 toggle?.addEventListener('click',openMenu);close?.addEventListener('click',closeMenu);overlay?.addEventListener('click',closeMenu);
 document.querySelectorAll('.mobile-acc-btn').forEach(btn=>btn.addEventListener('click',()=>btn.closest('.mobile-acc')?.classList.toggle('open')));
 document.querySelectorAll('.faq-q').forEach(btn=>btn.addEventListener('click',()=>btn.closest('.faq-item')?.classList.toggle('open')));
 document.querySelectorAll('[data-filter]').forEach(btn=>btn.addEventListener('click',()=>{const group=btn.closest('[data-filter-group]');group?.querySelectorAll('[data-filter]').forEach(b=>b.classList.remove('active'));btn.classList.add('active');const value=btn.dataset.filter;document.querySelectorAll('[data-category]').forEach(card=>card.style.display=(value==='all'||card.dataset.category===value)?'':'none')}));
 document.querySelectorAll('.gallery-item').forEach(item=>item.addEventListener('click',()=>{const src=item.querySelector('img')?.src;const lb=document.querySelector('.lightbox');if(src&&lb){lb.querySelector('img').src=src;lb.classList.add('open')}}));
 document.querySelector('.lightbox-close')?.addEventListener('click',()=>document.querySelector('.lightbox')?.classList.remove('open'));document.querySelector('.lightbox')?.addEventListener('click',e=>{if(e.target.classList.contains('lightbox'))e.currentTarget.classList.remove('open')});
 document.querySelectorAll('[data-year]').forEach(el=>el.textContent=new Date().getFullYear());
 topBtn?.addEventListener('click',()=>scrollTo({top:0,behavior:'smooth'}));
});