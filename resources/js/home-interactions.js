// Theme Toggle
const html = document.documentElement;
let isDark = true;
document.getElementById('themeToggle').addEventListener('click', () => {
  isDark = !isDark;
  html.classList.toggle('dark', isDark);
  document.getElementById('sunIcon').classList.toggle('hidden', !isDark);
  document.getElementById('moonIcon').classList.toggle('hidden', isDark);
});

// FAQ
function toggleFaq(i) {
  const body = document.getElementById('faq-' + i);
  const chev = document.getElementById('chevron-' + i);
  const isOpen = body.classList.contains('open');
  document.querySelectorAll('.faq-body').forEach(el => el.classList.remove('open'));
  document.querySelectorAll('.faq-chevron').forEach(el => el.classList.remove('rotated'));
  if (!isOpen) { body.classList.add('open'); chev.classList.add('rotated'); }
}

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const t = document.querySelector(a.getAttribute('href'));
    if (t) { e.preventDefault(); t.scrollIntoView({ behavior: 'smooth' }); }
  });
});