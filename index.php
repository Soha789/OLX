<?php /* index.php : Home + Search & Filters + Recent & Featured */ ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Mini OLX Clone — Home</title>
<style>
:root{
  --bg:#0f1221;--card:#151936;--card2:#12152c;--acc:#6ee7ff;--acc2:#a78bfa;--text:#eef2ff;--muted:#9aa3b2;
  --ok:#22c55e;--warn:#f59e0b;--bad:#ef4444;
}
*{box-sizing:border-box} body{margin:0;font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;background:radial-gradient(1200px 600px at 10% -10%,#1b1f49 0%,#0f1221 60%),#0b0f1e;color:var(--text)}
a{color:inherit;text-decoration:none}
.container{max-width:1200px;margin:auto;padding:24px}
.topbar{display:flex;gap:12px;align-items:center;justify-content:space-between;margin-bottom:18px}
.brand{display:flex;gap:10px;align-items:center}
.logo{width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg,var(--acc),var(--acc2));display:grid;place-items:center;font-weight:800;color:#0b1020}
.badge{padding:3px 8px;border-radius:999px;background:linear-gradient(135deg,#1e263f,#171c36);border:1px solid #2a3256;color:var(--muted);font-size:12px}
.actions{display:flex;gap:10px}
.btn{border:1px solid #2a3256;background:linear-gradient(180deg,var(--card),var(--card2));color:var(--text);padding:10px 14px;border-radius:12px;cursor:pointer;transition:.2s;display:inline-flex;align-items:center;gap:8px}
.btn:hover{transform:translateY(-1px);border-color:#3a4681}
.btn.primary{border-color:transparent;background:linear-gradient(135deg,var(--acc2),var(--acc));color:#0d1024;font-weight:700}
.grid{display:grid;grid-template-columns:1fr;gap:16px}
@media(min-width:900px){.grid{grid-template-columns:280px 1fr}}
.card{background:linear-gradient(180deg,var(--card),var(--card2));border:1px solid #2a3256;border-radius:18px;padding:16px;box-shadow:0 10px 30px rgba(0,0,0,.25)}
.card h3{margin:0 0 12px;font-size:18px}
.searchbar{display:grid;gap:10px}
.filter-row{display:grid;grid-template-columns:1fr 1fr;gap:10px}
@media(min-width:700px){.filter-row{grid-template-columns:repeat(4,1fr)}}
.input,select{width:100%;padding:12px 14px;border-radius:12px;border:1px solid #2a3256;background:#0f1430;color:var(--text);outline:none}
.pills{display:flex;flex-wrap:wrap;gap:8px;margin-top:6px}
.pill{padding:6px 10px;border-radius:999px;border:1px solid #2a3256;background:#12163a;color:var(--muted);font-size:12px;cursor:pointer}
.pill.active{border-color:transparent;background:linear-gradient(135deg,#2a3466,#222a58);color:#d9e1ff}
.list{display:grid;grid-template-columns:repeat(1,1fr);gap:14px}
@media(min-width:620px){.list{grid-template-columns:repeat(2,1fr)}}
@media(min-width:980px){.list{grid-template-columns:repeat(3,1fr)}}
.item{border:1px solid #2a3256;background:linear-gradient(180deg,#111538,#0f1430);border-radius:16px;overflow:hidden;display:flex;flex-direction:column}
.thumb{aspect-ratio:16/11;background:#0c1130;display:grid;place-items:center}
.thumb img{width:100%;height:100%;object-fit:cover;display:block}
.meta{padding:12px;display:grid;gap:8px}
.title{font-weight:700}
.price{font-weight:800;background:linear-gradient(135deg,var(--acc),var(--acc2));-webkit-background-clip:text;background-clip:text;color:transparent}
.dim{color:var(--muted);font-size:13px}
.row{display:flex;justify-content:space-between;align-items:center}
.sold{color:#ffd1d1;background:#3a0f18;border:1px solid #7a1d2c;padding:2px 8px;border-radius:10px;font-size:12px}
.footer-note{margin-top:20px;color:var(--muted);font-size:13px;text-align:center}
.kicker{display:flex;gap:10px;align-items:center}
hr.sep{border:0;height:1px;background:linear-gradient(90deg,transparent,#2a3256,transparent);margin:14px 0}
</style>
</head>
<body>
  <div class="container">
    <div class="topbar">
      <div class="brand">
        <div class="logo">OX</div>
        <div>
          <div style="font-weight:800;letter-spacing:.3px">Mini OLX Clone</div>
          <div class="badge">Buy • Sell • Chat</div>
        </div>
      </div>
      <div class="actions">
        <button class="btn" id="goProfile">Profile</button>
        <button class="btn primary" id="goPost">+ Post Ad</button>
      </div>
    </div>

    <div class="grid">
      <!-- Filters -->
      <div class="card">
        <h3>Search & Filters</h3>
        <div class="searchbar">
          <input class="input" id="q" placeholder="Search by title or description…" />
          <div class="filter-row">
            <select id="cat">
              <option value="">All Categories</option>
              <option>Electronics</option><option>Vehicles</option><option>Furniture</option>
              <option>Real Estate</option><option>Fashion</option><option>Other</option>
            </select>
            <select id="cond">
              <option value="">Any Condition</option>
              <option>New</option><option>Used - Like New</option><option>Used - Good</option><option>Used - Fair</option>
            </select>
            <input class="input" id="min" placeholder="Min Price" type="number" min="0"/>
            <input class="input" id="max" placeholder="Max Price" type="number" min="0"/>
          </div>
          <input class="input" id="loc" placeholder="Location (e.g., Riyadh)"/>
          <div class="pills" id="categoryPills"></div>
          <div class="kicker">
            <button class="btn" id="apply">Apply Filters</button>
            <button class="btn" id="clear">Reset</button>
          </div>
        </div>
      </div>
      <!-- Listings -->
      <div class="card">
        <div class="row">
          <h3>Featured & Recent</h3>
          <div class="badge" id="countInfo">0 items</div>
        </div>
        <div class="list" id="list"></div>
        <div class="footer-note">Tip: Click a card to open full details & chat.</div>
      </div>
    </div>
    <hr class="sep">
    <div class="footer-note">Made with ♥ — No database. Everything saved in your browser (localStorage).</div>
  </div>

<script>
/* ====== Mini Shared Store (localStorage) ====== */
const store = {
  read(key, fallback){ try{ return JSON.parse(localStorage.getItem(key)) ?? fallback }catch{ return fallback } },
  write(key, value){ localStorage.setItem(key, JSON.stringify(value)) }
};
function uid(){ return 'id_'+Math.random().toString(36).slice(2,9) }
if(!store.read('users')) store.write('users',[]);
if(!store.read('currentUser')) store.write('currentUser',null);
if(!store.read('ads')){
  // seed a few demo ads
  const demo = [
    {id:uid(),title:"iPhone 13 Pro",price:2600,location:"Riyadh",condition:"Used - Good",category:"Electronics",desc:"256GB • Graphite • Great battery",imgs:[],sellerId:null,createdAt:Date.now()-86400000,isSold:false},
    {id:uid(),title:"Honda Civic 2017",price:44500,location:"Jeddah",condition:"Used - Good",category:"Vehicles",desc:"85k km • GCC spec • Full service history",imgs:[],sellerId:null,createdAt:Date.now()-172800000,isSold:false},
    {id:uid(),title:"Sofa Set (3+2+1)",price:1900,location:"Dammam",condition:"Used - Like New",category:"Furniture",desc:"Clean, smoke-free home",imgs:[],sellerId:null,createdAt:Date.now()-3600000,isSold:true}
  ];
  store.write('ads', demo);
}
if(!store.read('messages')) store.write('messages',{}); // { [adId]: [ {from,to,text,ts} ] }

/* ====== UI Helpers ====== */
const el = sel => document.querySelector(sel);
const listEl = el('#list');
const countInfo = el('#countInfo');

const categories = ["Electronics","Vehicles","Furniture","Real Estate","Fashion","Other"];
const pills = el('#categoryPills');
categories.forEach(c=>{
  const b = document.createElement('button');
  b.className = 'pill'; b.textContent = c;
  b.onclick = ()=>{ el('#cat').value = (el('#cat').value===c? "":c); syncPills(); };
  pills.appendChild(b);
});
function syncPills(){
  const c = el('#cat').value;
  [...pills.children].forEach(x=> x.classList.toggle('active', x.textContent===c));
}
syncPills();

/* ====== Render Listings with Filters ====== */
function priceOk(v,min,max){
  if(min && v < +min) return false;
  if(max && v > +max) return false;
  return true;
}
function render(){
  const q = el('#q').value.trim().toLowerCase();
  const cat = el('#cat').value;
  const cond = el('#cond').value;
  const min = el('#min').value;
  const max = el('#max').value;
  const loc = el('#loc').value.trim().toLowerCase();

  const ads = (store.read('ads',[])).slice().sort((a,b)=>b.createdAt-a.createdAt);
  const filtered = ads.filter(a=>{
    const text = (a.title+' '+a.desc).toLowerCase();
    const okQ = !q || text.includes(q);
    const okC = !cat || a.category===cat;
    const okCond = !cond || a.condition===cond;
    const okLoc = !loc || a.location.toLowerCase().includes(loc);
    const okPrice = priceOk(a.price,min,max);
    return okQ && okC && okCond && okLoc && okPrice;
  });
  countInfo.textContent = `${filtered.length} item${filtered.length!==1?'s':''}`;
  listEl.innerHTML = '';
  filtered.forEach(a=>{
    const card = document.createElement('div'); card.className='item';
    const img = a.imgs?.[0] ? `<img src="${a.imgs[0]}" alt="${a.title}">` : `<div style="color:#566; font-weight:700">NO IMAGE</div>`;
    card.innerHTML = `
      <div class="thumb">${img}</div>
      <div class="meta">
        <div class="row">
          <div class="title">${a.title}</div>
          <div class="price">SAR ${Number(a.price).toLocaleString()}</div>
        </div>
        <div class="dim">${a.category} • ${a.condition}</div>
        <div class="row">
          <div class="dim">📍 ${a.location}</div>
          ${a.isSold ? '<span class="sold">SOLD</span>' : ''}
        </div>
        <div class="row">
          <button class="btn" data-id="${a.id}">View</button>
          <button class="btn" onclick="window.location='post.php?edit=${encodeURIComponent(a.id)}'">Edit</button>
        </div>
      </div>`;
    card.querySelector('[data-id]').onclick = () => window.location = 'ad.php?id='+encodeURIComponent(a.id);
    listEl.appendChild(card);
  });
}
render();

/* ====== Events ====== */
el('#apply').onclick = render;
el('#clear').onclick = ()=>{
  ['q','min','max','loc'].forEach(id=> el('#'+id).value='');
  el('#cat').value=''; el('#cond').value=''; syncPills(); render();
};
el('#goPost').onclick = ()=> window.location='post.php';
el('#goProfile').onclick = ()=> window.location='auth.php';
</script>
</body>
</html>
