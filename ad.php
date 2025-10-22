<?php /* ad.php : Ad detail + Seller info + Chat/Contact (localStorage) */ ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Ad • Mini OLX Clone</title>
<style>
:root{--bg:#0f1221;--card:#151936;--card2:#12152c;--acc:#6ee7ff;--acc2:#a78bfa;--text:#eef2ff;--muted:#9aa3b2;--ok:#22c55e;--bad:#ef4444}
*{box-sizing:border-box} body{margin:0;font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;background:radial-gradient(1200px 600px at 10% -10%,#1b1f49 0%,#0f1221 60%),#0b0f1e;color:var(--text)}
.container{max-width:1100px;margin:auto;padding:24px}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
.btn{border:1px solid #2a3256;background:linear-gradient(180deg,var(--card),var(--card2));color:var(--text);padding:10px 14px;border-radius:12px;cursor:pointer;transition:.2s}
.btn:hover{transform:translateY(-1px);border-color:#3a4681}
.btn.primary{border-color:transparent;background:linear-gradient(135deg,var(--acc2),var(--acc));color:#0d1024;font-weight:700}
.badge{padding:3px 8px;border-radius:999px;background:#13183a;border:1px solid #2a3256;color:var(--muted);font-size:12px;display:inline-block}
.grid{display:grid;gap:16px}
@media(min-width:900px){.grid{grid-template-columns:1.4fr .8fr}}
.card{background:linear-gradient(180deg,var(--card),var(--card2));border:1px solid #2a3256;border-radius:18px;padding:16px}
h2{margin:0 0 10px}
.gallery{display:grid;grid-template-columns:1fr;gap:10px}
@media(min-width:700px){.gallery{grid-template-columns:repeat(2,1fr)}}
.thumb{aspect-ratio:16/11;background:#0f1430;border-radius:12px;overflow:hidden;display:grid;place-items:center}
.thumb img{width:100%;height:100%;object-fit:cover}
.row{display:flex;justify-content:space-between;align-items:center;gap:10px}
.price{font-weight:800;background:linear-gradient(135deg,var(--acc),var(--acc2));-webkit-background-clip:text;background-clip:text;color:transparent;font-size:22px}
.sold{color:#ffd1d1;background:#3a0f18;border:1px solid #7a1d2c;padding:2px 8px;border-radius:10px;font-size:12px}
.kv{display:grid;grid-template-columns:1fr 1fr;gap:10px;color:var(--muted)}
.chat{display:grid;grid-template-rows:auto 1fr auto;gap:10px;height:460px}
.thread{border:1px solid #2a3256;background:#0f1430;border-radius:12px;overflow:auto;padding:10px;display:flex;flex-direction:column;gap:8px}
.bubble{max-width:75%;padding:10px 12px;border-radius:14px;border:1px solid #334;background:#12183a}
.me{align-self:flex-end;border-color:#315e4b;background:#0f2c1f}
.you{align-self:flex-start}
.input{width:100%;padding:12px;border-radius:12px;border:1px solid #2a3256;background:#0f1430;color:var(--text)}
.small{font-size:12px;color:var(--muted)}
</style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div style="font-weight:800">Ad Details</div>
      <div>
        <button class="btn" onclick="window.location='index.php'">← Home</button>
        <button class="btn" onclick="window.location='post.php'">+ Post Ad</button>
      </div>
    </div>

    <div class="grid">
      <div class="card" id="left">
        <div class="row">
          <h2 id="title">Loading…</h2>
          <div class="price" id="price"></div>
        </div>
        <div class="row">
          <div class="badge" id="meta1"></div>
          <div id="soldFlag"></div>
        </div>
        <div class="gallery" id="gallery"></div>
        <div style="margin-top:10px" class="small" id="desc"></div>
      </div>

      <div class="card">
        <h2>Seller & Contact</h2>
        <div class="kv">
          <div>Seller</div><div id="sellerName">—</div>
          <div>Phone</div><div id="sellerPhone">—</div>
          <div>Location</div><div id="sellerLoc">—</div>
          <div>Posted</div><div id="sellerDate">—</div>
        </div>
        <div class="small" style="margin-top:8px">You can call or chat below.</div>
        <div class="chat" style="margin-top:12px">
          <div class="row">
            <div class="badge" id="loginReq" style="display:none">Please login to chat</div>
            <div class="badge" id="chatWith">Chat with seller</div>
          </div>
          <div class="thread" id="thread"></div>
          <div class="row">
            <input class="input" id="msg" placeholder="Type your message…" />
            <button class="btn primary" id="send">Send</button>
          </div>
          <div class="small">Messages are stored locally in your browser per ad.</div>
        </div>
      </div>
    </div>
  </div>

<script>
const store = { read(k,f){try{return JSON.parse(localStorage.getItem(k))??f}catch{return f}}, write(k,v){localStorage.setItem(k,JSON.stringify(v))}};
if(!store.read('ads')) store.write('ads',[]);
if(!store.read('users')) store.write('users',[]);
if(!store.read('messages')) store.write('messages',{});
if(!store.read('currentUser')) store.write('currentUser',null);

const params = new URLSearchParams(location.search);
const id = params.get('id');
const ads = store.read('ads',[]);
const ad = ads.find(x=>x.id===id);
if(!ad){ alert('Ad not found'); window.location='index.php'; }

const cu = store.read('currentUser',null);
const users = store.read('users',[]);
const seller = users.find(u=>u.id===ad.sellerId) || {name:'Guest Seller',phone:ad.phone||'—'};

document.getElementById('title').textContent = ad.title;
document.getElementById('price').textContent = 'SAR ' + Number(ad.price).toLocaleString();
document.getElementById('meta1').textContent = `${ad.category} • ${ad.condition}`;
document.getElementById('desc').textContent = ad.desc||'';
document.getElementById('sellerName').textContent = seller.name||'—';
document.getElementById('sellerPhone').textContent = (ad.phone || seller.phone || '—');
document.getElementById('sellerLoc').textContent = ad.location || '—';
document.getElementById('sellerDate').textContent = new Date(ad.createdAt||Date.now()).toLocaleString();
document.getElementById('soldFlag').innerHTML = ad.isSold? '<span class="sold">SOLD</span>':'';

const gal = document.getElementById('gallery');
(gal.innerHTML = (ad.imgs||[]).map(s=>`<div class="thumb"><img src="${s}"></div>`).join('')) || (gal.innerHTML = `<div class="thumb"><div style="color:#556">NO IMAGE</div></div>`);

/* ===== Chat ===== */
const thread = document.getElementById('thread');
const msg = document.getElementById('msg');
const send = document.getElementById('send');
document.getElementById('loginReq').style.display = cu? 'none':'inline-block';
if(!cu){ msg.disabled=true; send.disabled=true; }

function getRoom(){
  let m = store.read('messages',{}); if(!m[id]) m[id]=[];
  return m;
}
function renderChat(){
  const all = store.read('messages',{})[id]||[];
  thread.innerHTML='';
  all.forEach(x=>{
    const div = document.createElement('div');
    div.className = 'bubble ' + (cu && x.from===cu.id ? 'me' : 'you');
    const from = users.find(u=>u.id===x.from)?.name || (x.from===ad.sellerId?'Seller':'User');
    div.innerHTML = `<div style="font-size:12px;color:#9aa3b2;margin-bottom:4px">${from} • ${new Date(x.ts).toLocaleTimeString()}</div>${x.text}`;
    thread.appendChild(div);
  });
  thread.scrollTop = thread.scrollHeight;
}
renderChat();

send.onclick = ()=>{
  const text = msg.value.trim(); if(!text) return;
  const m = getRoom();
  const to = (cu && cu.id===ad.sellerId) ? null : ad.sellerId; // to seller (simulated)
  const from = cu?.id || 'guest';
  m[id].push({from,to,text,ts:Date.now()});
  store.write('messages',m);
  msg.value=''; renderChat();
};
</script>
</body>
</html>
