<?php /* post.php : Create/Edit/Delete/Mark-Sold Ads (images saved Base64) */ ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Post an Ad • Mini OLX Clone</title>
<style>
:root{--bg:#0f1221;--card:#151936;--card2:#12152c;--acc:#6ee7ff;--acc2:#a78bfa;--text:#eef2ff;--muted:#9aa3b2;--warn:#f59e0b;--bad:#ef4444}
*{box-sizing:border-box} body{margin:0;font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;background:radial-gradient(1200px 600px at 10% -10%,#1b1f49 0%,#0f1221 60%),#0b0f1e;color:var(--text)}
.container{max-width:900px;margin:auto;padding:24px}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
.btn{border:1px solid #2a3256;background:linear-gradient(180deg,var(--card),var(--card2));color:var(--text);padding:10px 14px;border-radius:12px;cursor:pointer;transition:.2s}
.btn:hover{transform:translateY(-1px);border-color:#3a4681}
.btn.primary{border-color:transparent;background:linear-gradient(135deg,var(--acc2),var(--acc));color:#0d1024;font-weight:700}
.btn.warn{border-color:#7a5312;background:#2a1f0f}
.btn.danger{border-color:#7a1d2c;background:#2a0f16}
.card{background:linear-gradient(180deg,var(--card),var(--card2));border:1px solid #2a3256;border-radius:18px;padding:16px}
h2{margin:0 0 10px}
.grid{display:grid;gap:12px}
.row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
@media(min-width:760px){.row3{grid-template-columns:repeat(3,1fr)}}
.input,select,textarea{width:100%;padding:12px;border-radius:12px;border:1px solid #2a3256;background:#0f1430;color:var(--text)}
textarea{min-height:120px;resize:vertical}
.gallery{display:grid;grid-template-columns:repeat(2,1fr);gap:10px}
@media(min-width:700px){.gallery{grid-template-columns:repeat(4,1fr)}}
.thumb{aspect-ratio:1/1;background:#0f1430;border:1px dashed #334;display:grid;place-items:center;border-radius:12px;overflow:hidden}
.thumb img{width:100%;height:100%;object-fit:cover}
.small{font-size:12px;color:var(--muted)}
.spread{display:flex;gap:10px;flex-wrap:wrap}
</style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div style="font-weight:800">Post / Edit Ad</div>
      <div>
        <button class="btn" onclick="window.location='index.php'">← Home</button>
        <button class="btn" onclick="window.location='auth.php'">Profile</button>
      </div>
    </div>

    <div class="card">
      <h2 id="modeTitle">Create New Ad</h2>
      <div class="grid">
        <div class="row">
          <input class="input" id="title" placeholder="Title (e.g., iPhone 13 Pro)"/>
          <input class="input" id="price" type="number" min="0" placeholder="Price (SAR)"/>
        </div>
        <div class="row">
          <select id="category">
            <option>Electronics</option><option>Vehicles</option><option>Furniture</option>
            <option>Real Estate</option><option>Fashion</option><option>Other</option>
          </select>
          <select id="condition">
            <option>New</option><option>Used - Like New</option><option>Used - Good</option><option>Used - Fair</option>
          </select>
        </div>
        <div class="row">
          <input class="input" id="location" placeholder="Location (e.g., Riyadh)"/>
          <input class="input" id="phone" placeholder="Contact Phone (optional override)"/>
        </div>
        <textarea id="desc" placeholder="Describe your item… include key specs, age, reason for selling, etc."></textarea>
        <div>
          <label class="small">Images (up to 6) — stored locally in your browser</label><br>
          <input type="file" id="imgs" accept="image/*" multiple />
          <div class="gallery" id="gallery"></div>
        </div>
        <div class="spread">
          <button class="btn primary" id="saveBtn">Save Ad</button>
          <button class="btn warn" id="markSoldBtn" style="display:none">Mark as Sold</button>
          <button class="btn danger" id="deleteBtn" style="display:none">Delete Ad</button>
        </div>
        <div class="small">Note: Without backend, your ads are visible only on this browser.</div>
      </div>
    </div>
  </div>

<script>
const store = { read(k,f){try{return JSON.parse(localStorage.getItem(k))??f}catch{return f}}, write(k,v){localStorage.setItem(k,JSON.stringify(v))}};
if(!store.read('ads')) store.write('ads',[]);
if(!store.read('currentUser')) store.write('currentUser',null);
if(!store.read('users')) store.write('users',[]);

const cu = store.read('currentUser',null);
if(!cu){ alert('Please login first.'); window.location='auth.php'; }

const params = new URLSearchParams(location.search);
const editId = params.get('edit');

let ad = null;
if(editId){
  const ads = store.read('ads',[]);
  ad = ads.find(x=>x.id===editId);
  if(!ad){ alert('Ad not found.'); window.location='index.php'; }
  if(ad.sellerId && ad.sellerId!==cu.id){ alert('You can only edit your own ad.'); window.location='index.php'; }
  document.getElementById('modeTitle').textContent = 'Edit Ad';
}

const fields = {
  title:document.getElementById('title'), price:document.getElementById('price'),
  category:document.getElementById('category'), condition:document.getElementById('condition'),
  location:document.getElementById('location'), phone:document.getElementById('phone'), desc:document.getElementById('desc')
};
const gallery = document.getElementById('gallery');
let images = [];

function fillForm(){
  if(!ad) return;
  fields.title.value = ad.title||'';
  fields.price.value = ad.price||'';
  fields.category.value = ad.category||'Electronics';
  fields.condition.value = ad.condition||'New';
  fields.location.value = ad.location||'';
  fields.phone.value = ad.phone||'';
  fields.desc.value = ad.desc||'';
  images = ad.imgs||[];
  renderGallery();
  document.getElementById('markSoldBtn').style.display='inline-block';
  document.getElementById('deleteBtn').style.display='inline-block';
}
fillForm();

function renderGallery(){
  gallery.innerHTML='';
  images.slice(0,6).forEach((src,i)=>{
    const d=document.createElement('div'); d.className='thumb';
    d.innerHTML=`<img src="${src}"/><button style="position:absolute;margin:6px;padding:6px 10px;border-radius:10px;border:1px solid #7a1d2c;background:#2a0f16;color:#ffd1d1;cursor:pointer" onclick="removeImg(${i})">Remove</button>`;
    d.style.position='relative';
    gallery.appendChild(d);
  });
}
window.removeImg = (i)=>{ images.splice(i,1); renderGallery(); };

document.getElementById('imgs').addEventListener('change', async (e)=>{
  const files = [...e.target.files].slice(0,6-images.length);
  for(const f of files){
    const data = await fileToDataURL(f);
    images.push(data);
  }
  renderGallery();
});
function fileToDataURL(file){ return new Promise(res=>{ const r=new FileReader(); r.onload=()=>res(r.result); r.readAsDataURL(file); })}

document.getElementById('saveBtn').onclick = ()=>{
  const payload = {
    title:fields.title.value.trim(),
    price:+fields.price.value||0,
    category:fields.category.value,
    condition:fields.condition.value,
    location:fields.location.value.trim(),
    phone:fields.phone.value.trim(),
    desc:fields.desc.value.trim(),
    imgs:images.slice(0,6),
    sellerId: cu.id,
  };
  if(!payload.title || !payload.price || !payload.location){ alert('Please fill Title, Price and Location.'); return; }
  const ads = store.read('ads',[]);
  if(ad){
    Object.assign(ad,payload);
    store.write('ads', ads.map(x=>x.id===ad.id? ad:x));
    alert('Ad updated!'); window.location='ad.php?id='+encodeURIComponent(ad.id);
  }else{
    const newAd = { id: 'id_'+Math.random().toString(36).slice(2,9), createdAt:Date.now(), isSold:false, ...payload };
    ads.push(newAd); store.write('ads',ads);
    alert('Ad posted!'); window.location='ad.php?id='+encodeURIComponent(newAd.id);
  }
};

document.getElementById('markSoldBtn').onclick = ()=>{
  const ads = store.read('ads',[]);
  ad.isSold = !ad.isSold; store.write('ads', ads.map(x=>x.id===ad.id?ad:x));
  alert(ad.isSold? 'Marked as SOLD' : 'Marked as Available');
};

document.getElementById('deleteBtn').onclick = ()=>{
  if(!confirm('Delete this ad?')) return;
  let ads = store.read('ads',[]);
  ads = ads.filter(x=>x.id!==ad.id); store.write('ads',ads);
  alert('Ad deleted'); window.location='index.php';
};
</script>
</body>
</html>
