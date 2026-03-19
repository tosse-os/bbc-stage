/**
 * Kollabiert / expandiert die Dashboard-Sidebar.
 * - reduziert effektiv die Sidebar-Breite
 * - passt Padding an
 * - zentriert Icons
 * - blendet Labels aus
 * - skaliert Logo
 * - persistiert Zustand
 */

document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.querySelector('[data-sidebar]');
  const toggle = document.querySelector('[data-sidebar-toggle]');
  if (!sidebar || !toggle) return;

  const labels = sidebar.querySelectorAll('[data-sidebar-label]');
  const logo = sidebar.querySelector('[data-sidebar-logo]');
  const header = sidebar.querySelector('[data-sidebar-header]');
  const nav = sidebar.querySelector('[data-sidebar-nav]');
  const footer = sidebar.querySelector('[data-sidebar-footer]');

  function apply(collapsed) {
    sidebar.dataset.collapsed = collapsed ? '1' : '0';

    sidebar.classList.toggle('w-72', !collapsed);
    sidebar.classList.toggle('w-16', collapsed);

    header.classList.toggle('px-6', !collapsed);
    header.classList.toggle('px-2', collapsed);

    nav.classList.toggle('px-4', !collapsed);
    nav.classList.toggle('px-2', collapsed);

    footer.classList.toggle('px-6', !collapsed);
    footer.classList.toggle('px-2', collapsed);

    labels.forEach(el => el.classList.toggle('hidden', collapsed));

    logo.classList.toggle('h-10', !collapsed);
    logo.classList.toggle('h-6', collapsed);

    localStorage.setItem('dashboard_sidebar_collapsed', collapsed ? '1' : '0');
  }

  apply(localStorage.getItem('dashboard_sidebar_collapsed') === '1');

  toggle.addEventListener('click', () => {
    apply(sidebar.dataset.collapsed !== '1');
  });
});
