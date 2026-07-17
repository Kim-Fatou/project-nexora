/*page interests*/


const ICONS = {
  palette:'<circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/><circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/><circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/><circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"/>',
  music:'<path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/>',
  camera:'<path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/>',
  brush:'<path d="m9.06 11.9 8.07-8.06a2.85 2.85 0 1 1 4.03 4.03l-8.06 8.08"/><path d="M7.07 14.94c-1.66 0-3 1.35-3 3.02 0 1.33-2.5 1.52-2 2.02 1.08 1.1 2.49 2.02 4 2.02 2.2 0 4-1.8 4-4.04a3.01 3.01 0 0 0-3-3.02z"/>',
  film:'<rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 3v18M17 3v18M3 7.5h4M3 12h18M3 16.5h4M17 7.5h4M17 16.5h4"/>',
  mic:'<path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" x2="12" y1="19" y2="22"/>',
  code:'<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>',
  brain:'<path d="M12 5a3 3 0 1 0-5.997.142 4 4 0 0 0-2.526 5.77 4 4 0 0 0 .556 6.588A4 4 0 1 0 12 18Z"/><path d="M12 5a3 3 0 1 1 5.997.142 4 4 0 0 1 2.526 5.77 4 4 0 0 1-.556 6.588A4 4 0 1 1 12 18Z"/>',
  rocket:'<path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/>',
  game:'<line x1="6" x2="10" y1="11" y2="11"/><line x1="8" x2="8" y1="9" y2="13"/><line x1="15" x2="15.01" y1="12" y2="12"/><line x1="18" x2="18.01" y1="10" y2="10"/><path d="M17.32 5H6.68a4 4 0 0 0-3.978 3.59c-.006.052-.01.101-.017.152C2.604 9.416 2 14.456 2 16a3 3 0 0 0 3 3c1 0 1.5-.5 2-1l1.414-1.414A2 2 0 0 1 9.828 16h4.344a2 2 0 0 1 1.414.586L17 18c.5.5 1 1 2 1a3 3 0 0 0 3-3c0-1.545-.604-6.584-.685-7.258Z"/>',
  sparkles:'<path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/>',
  plane:'<path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>',
  chef:'<path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/><line x1="6" x2="18" y1="17" y2="17"/>',
  leaf:'<path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>',
  coffee:'<path d="M10 2v2M14 2v2M16 8a1 1 0 0 1 1 1v8a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V9a1 1 0 0 1 1-1h14a4 4 0 1 1 0 8h-1"/><path d="M6 2v2"/>',
  wine:'<path d="M8 22h8M7 10h10M12 15v7M12 15a5 5 0 0 0 5-5c0-2-.5-4-1-8H8c-.5 4-1 6-1 8a5 5 0 0 0 5 5Z"/>',
  heart:'<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>',
  dumbbell:'<path d="m6.5 6.5 11 11M21 21l-1-1M3 3l1 1M18 22l4-4M2 6l4-4M3 10l7-7M14 21l7-7"/>',
  bike:'<circle cx="18.5" cy="17.5" r="3.5"/><circle cx="5.5" cy="17.5" r="3.5"/><circle cx="15" cy="5" r="1"/><path d="M12 17.5V14l-3-3 4-3 2 3h2"/>',
  mountain:'<path d="m8 3 4 8 5-5 5 15H2L8 3z"/>',
  book:'<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>',
  globe:'<circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20M2 12h20"/>',
  headphones:'<path d="M3 14h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2zM21 14h-3a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2z"/><path d="M3 14a9 9 0 0 1 18 0"/>',
  stars:'<path d="M12 3l1.9 5.8H20l-5 3.6 1.9 5.8L12 14.6 7.1 18.2 9 12.4l-5-3.6h6.1L12 3z"/>'
};

const INTERESTS = [
  {id:'art',label:'Art & Design',icon:'palette',group:'Créatif',color:'#e85d75',emoji:'🎨'},
  {id:'musique',label:'Musique',icon:'music',group:'Créatif',color:'#9b5de5',emoji:'🎵'},
  {id:'photo',label:'Photographie',icon:'camera',group:'Créatif',color:'#4a4e69',emoji:'📷'},
  {id:'illustration',label:'Illustration',icon:'brush',group:'Créatif',color:'#f15bb5',emoji:'🖌️'},
  {id:'cinema',label:'Cinéma',icon:'film',group:'Créatif',color:'#c1121f',emoji:'🎬'},
  {id:'podcast',label:'Podcasts',icon:'mic',group:'Créatif',color:'#7209b7',emoji:'🎙️'},
  {id:'code',label:'Développement',icon:'code',group:'Tech',color:'#0077b6',emoji:'💻'},
  {id:'ia',label:'Intelligence A.',icon:'brain',group:'Tech',color:'#3a0ca3',emoji:'🧠'},
  {id:'startup',label:'Startups',icon:'rocket',group:'Tech',color:'#f77f00',emoji:'🚀'},
  {id:'gaming',label:'Jeux vidéo',icon:'game',group:'Tech',color:'#06d6a0',emoji:'🎮'},
  {id:'tech',label:'Tech & Gadgets',icon:'sparkles',group:'Tech',color:'#118ab2',emoji:'✨'},
  {id:'voyage',label:'Voyage',icon:'plane',group:'Style de vie',color:'#00afb9',emoji:'✈️'},
  {id:'cuisine',label:'Cuisine',icon:'chef',group:'Style de vie',color:'#e76f51',emoji:'🍳'},
  {id:'nature',label:'Nature',icon:'leaf',group:'Style de vie',color:'#2a9d8f',emoji:'🌿'},
  {id:'cafe',label:'Café & Thé',icon:'coffee',group:'Style de vie',color:'#774936',emoji:'☕'},
  {id:'vin',label:'Vins',icon:'wine',group:'Style de vie',color:'#9d0208',emoji:'🍷'},
  {id:'bienetre',label:'Bien-être',icon:'heart',group:'Style de vie',color:'#ff70a6',emoji:'💗'},
  {id:'sport',label:'Fitness',icon:'dumbbell',group:'Mouvement',color:'#ef476f',emoji:'💪'},
  {id:'velo',label:'Vélo',icon:'bike',group:'Mouvement',color:'#06d6a0',emoji:'🚴'},
  {id:'rando',label:'Randonnée',icon:'mountain',group:'Mouvement',color:'#588157',emoji:'🏔️'},
  {id:'lecture',label:'Lecture',icon:'book',group:'Esprit',color:'#bc6c25',emoji:'📚'},
  {id:'culture',label:'Cultures',icon:'globe',group:'Esprit',color:'#2196f3',emoji:'🌍'},
  {id:'vinyle',label:'Vinyles',icon:'headphones',group:'Esprit',color:'#6a4c93',emoji:'🎧'},
  {id:'astro',label:'Astronomie',icon:'stars',group:'Esprit',color:'#3d348b',emoji:'🌌'},
];
const GROUPS = ['Tous','Créatif','Tech','Style de vie','Mouvement','Esprit'];

const selected = new Set();
let filter = 'Tous';

const svg = (name) => `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${ICONS[name]}</svg>`;
const check = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';

function renderFilters(){
  const el = document.getElementById('filters');
  if(!el) return;
  el.innerHTML = '';
  GROUPS.forEach(g => {
    const b = document.createElement('button');
    b.className = 'filter' + (filter===g?' active':'');
    b.textContent = g;
    b.onclick = () => { filter = g; renderFilters(); renderGrid(); };
    el.appendChild(b);
  });
}

function renderGrid(){
  const el = document.getElementById('grid');
  if(!el) return;
  el.innerHTML = '';
  const list = filter==='Tous' ? INTERESTS : INTERESTS.filter(i=>i.group===filter);
  list.forEach((it, idx) => {
    const isOn = selected.has(it.id);
    const wrap = document.createElement('div');
    wrap.className = 'item';
    wrap.style.animationDelay = (idx*0.03)+'s';
    wrap.innerHTML = `
      <button class="bubble${isOn?' on':''}" style="${isOn
        ? `background:${it.color}; box-shadow:0 0 0 4px ${it.color}33, 0 16px 38px -10px ${it.color}99;`
        : ''}">
        <span class="ring" style="border:2px solid ${it.color}"></span>
        <span style="color:${isOn?'#fff':it.color}; display:flex;">${svg(it.icon)}</span>
        <span class="check" style="color:${it.color}">${check}</span>
      </button>
      <span class="label" style="${isOn?`color:${it.color}`:''}">${it.label}</span>`;
    wrap.querySelector('.bubble').onclick = () => toggle(it);
    el.appendChild(wrap);
  });
}

function toggle(it){
  if(selected.has(it.id)){
    selected.delete(it.id);
  } else {
    selected.add(it.id);
    burst(it);
  }
  renderGrid();
  updateCta();
}

function updateCta(){
  const n = selected.size;
  const cta = document.getElementById('cta');
  if(!cta) return;
  document.getElementById('count').textContent = n;
  document.getElementById('ctaText').textContent = n>=3 ? "Parfait, ton univers est prêt" : `Encore ${3-n} pour continuer`;
  document.getElementById('continue').disabled = n < 3;
  cta.classList.toggle('show', n > 0);
}

function burst(it){
  const layer = document.createElement('div');
  layer.className = 'burst';
  const flash = document.createElement('div');
  flash.className = 'flash';
  flash.style.background = `radial-gradient(circle at 50% 55%, ${it.color}40, transparent 60%)`;
  layer.appendChild(flash);
  const big = document.createElement('div');
  big.className = 'big-emoji';
  big.textContent = it.emoji;
  layer.appendChild(big);
  for(let i=0;i<18;i++){
    const angle = (i/18)*Math.PI*2;
    const dist = 220 + Math.random()*260;
    const p = document.createElement('span');
    p.className = 'particle';
    p.textContent = it.emoji;
    p.animate([
      { transform:'translate(0,0) scale(0) rotate(0deg)', opacity:0 },
      { transform:`translate(${Math.cos(angle)*dist}px,${Math.sin(angle)*dist}px) scale(1) rotate(${Math.random()*360}deg)`, opacity:1, offset:0.5 },
      { transform:`translate(${Math.cos(angle)*dist*1.1}px,${Math.sin(angle)*dist*1.1}px) scale(.5) rotate(${Math.random()*360}deg)`, opacity:0 }
    ], { duration: 1300, easing:'ease-out', delay: 50 + Math.random()*150, fill:'forwards' });
    layer.appendChild(p);
  }
  document.body.appendChild(layer);
  setTimeout(()=>layer.remove(), 1500);
}

function submitInterests() {
  
   // 1. On récupère le tableau des IDs sélectionnés à partir de ton Set ou Array "selected"
    const arrayInterests = Array.from(selected);

    // 2. On vérifie s'il y en a bien au moins 3
    if (arrayInterests.length >= 3) {
        
        // On récupère le composant Livewire via son identifiant DOM
        const component = Livewire.find(
            document.querySelector('[wire\\:id]').getAttribute('wire:id')
        );

        if (component) {
            // On appelle la fonction PHP 'saveInterests' en lui passant notre tableau d'IDs
            component.call('saveInterests', arrayInterests);
        }

    } else {
        alert("Sélectionne au moins 3 centres d'intérêt !");
    }
}

document.addEventListener('livewire:navigated', () => {
    renderFilters();
    renderGrid();
    updateCta();
});

// Premier appel
renderFilters();
renderGrid();
updateCta();