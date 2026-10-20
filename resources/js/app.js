import './bootstrap';
document.querySelector('[data-menu]')?.addEventListener('click',function(){const open=document.querySelector('#main-menu').classList.toggle('open');this.setAttribute('aria-expanded',String(open));});
const adminMenu = document.querySelector('[data-admin-menu]');
const adminSidebar = document.querySelector('#admin-sidebar');
const sidebarScrim = document.querySelector('.sidebar-scrim');
const mobileSidebar = window.matchMedia('(max-width: 680px)');
function setSidebar(open) {
    if (adminSidebar) adminSidebar.inert = mobileSidebar.matches && !open;
    adminSidebar?.classList.toggle('open', open);
    adminMenu?.setAttribute('aria-expanded', String(open));
    if (sidebarScrim) sidebarScrim.hidden = !open;
    document.body.style.overflow = open && mobileSidebar.matches ? 'hidden' : '';
    if (open) adminSidebar?.querySelector('[data-sidebar-close]')?.focus();
}
adminMenu?.addEventListener('click', () => setSidebar(!adminSidebar?.classList.contains('open')));
document.querySelectorAll('[data-sidebar-close]').forEach(button => button.addEventListener('click', () => { setSidebar(false); adminMenu?.focus(); }));
document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && adminSidebar?.classList.contains('open')) { setSidebar(false); adminMenu?.focus(); }
    if (event.key === 'Tab' && mobileSidebar.matches && adminSidebar?.classList.contains('open')) {
        const items = Array.from(adminSidebar.querySelectorAll('a[href], button:not([disabled])')).filter(item => item.getClientRects().length);
        const first = items[0], last = items[items.length - 1];
        if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last?.focus(); }
        else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first?.focus(); }
    }
});
mobileSidebar.addEventListener('change', () => setSidebar(false));
setSidebar(false);
document.querySelectorAll('[data-editor-tab]').forEach(button=>button.addEventListener('click',()=>{document.querySelectorAll('[data-editor-tab]').forEach(b=>{b.classList.toggle('active',b===button);b.setAttribute('aria-selected',String(b===button));});document.querySelectorAll('[data-editor-panel]').forEach(panel=>panel.classList.toggle('hidden',panel.dataset.editorPanel!==button.dataset.editorTab));}));
document.querySelectorAll('[data-confirm]').forEach(form=>form.addEventListener('submit',event=>{if(!window.confirm(form.dataset.confirm))event.preventDefault();}));
const status=document.querySelector('[data-status]');const schedule=document.querySelector('[data-schedule]');function updateSchedule(){if(schedule){schedule.classList.toggle('hidden',status.value!=='scheduled');schedule.querySelector('input').required=status.value==='scheduled';}}status?.addEventListener('change',updateSchedule);updateSchedule();
document.querySelector('[data-share]')?.addEventListener('click',async function(){try{if(navigator.share){await navigator.share({title:this.dataset.title,url:location.href});}else if(navigator.clipboard){await navigator.clipboard.writeText(location.href);this.textContent=document.documentElement.lang==='bn'?'লিংক কপি হয়েছে ✓':'Link copied ✓';}else{window.prompt(document.documentElement.lang==='bn'?'লিংক কপি করুন':'Copy link',location.href);}}catch{}});
