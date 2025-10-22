<?php /* auth.php : Signup / Login / Profile (localStorage only) */ ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Account • Mini OLX Clone</title>
<style>
:root{--bg:#0f1221;--card:#151936;--card2:#12152c;--acc:#6ee7ff;--acc2:#a78bfa;--text:#eef2ff;--muted:#9aa3b2}
*{box-sizing:border-box} body{margin:0;font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;background:radial-gradient(1200px 600px at 10% -10%,#1b1f49 0%,#0f1221 60%),#0b0f1e;color:var(--text)}
.container{max-width:780px;margin:auto;padding:24px}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
.btn{border:1px solid #2a3256;background:linear-gradient(180deg,var(--card),var(--card2));color:var(--text);padding:10px 14px;border-radius:12px;cursor:pointer;transition:.2s}
.btn:hover{transform:translateY(-1px);border-color:#3a4681}
.btn.primary{border-color:transparent;background:linear-gradient(135deg,var(--acc2),var(--acc));color:#0d1024;font-weight:700}
.grid{display:grid;gap:16px}
.card{background:linear-gradient(180deg,var(--card),var(--card2));border:1px solid #2a3256;border-radius:18px;padding:16px}
h2{margin:0 0 10px}
.row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.input{width:100%;padding:12px;border-radius:12px;border:1px solid #2a3256;background:#0f1430;color:var(--text)}
.muted{color:var(--muted);font-size:13px}
.hr{height:1px;background:linear-gradient(90deg,transparent,#2a3256,transparent);margin:14px 0}
.badge{padding:3px 8px;border-radius:999px;background:#13183a;border:1px solid #2a3256;color:var(--muted);font-size:12px;display:inline-block}
ul{margin:8px 0 0 18px;color:var(--muted)}
</style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div style="font-weight:800">Account</div>
      <div>
        <button class="btn" onclick="window.location='index.php'">← Home</button>
        <button class="btn primary" onclick="window.location='post.php'">+ Post Ad</button>
      </div>
    </div>

    <div class="grid">
      <div class="card" id="profileCard" style="display:none">
        <h2>Your Profile</h2>
        <div class="row">
          <input class="input" id="p_name" placeholder="Full Name" />
          <input class="input" id="p_email" placeholder="Email" />
        </div>
        <div class="row">
          <input class="input" id="p_phone" placeholder="Phone (for buyers)" />
          <input class="input" id="p_password" type="password" placeholder="Change Password (optional)" />
        </div>
        <div class="row">
          <button class="btn primary" id="saveProfile">Save</button>
          <button class="btn" id="logout">Log out</button>
        </div>
        <div class="hr"></div>
        <div class="badge">Note</div>
        <ul>
          <li>No backend. Your account lives in your browser only.</li>
          <li>Use the same browser to see your ads & chats later.</li>
        </ul>
      </div>

      <div class="card" id="authCard">
        <h2>Create Account</h2>
        <div class="row">
          <input class="input" id="s_name" placeholder="Full Name" />
          <input class="input" id="s_email" placeholder="Email" />
        </div>
        <div class="row">
          <input class="input" id="s_phone" placeholder="Phone" />
          <input class="input" id="s_pass" type="password" placeholder="Password" />
        </div>
        <button class="btn primary" id="signup">Sign Up</button>
        <div class="hr"></div>
        <h2>Login</h2>
        <div class="row">
          <input class="input" id="l_email" placeholder="Email" />
          <input class="input" id="l_pass" type="password" placeholder="Password" />
        </div>
        <button class="btn" id="login">Login</button>
      </div>
    </div>
  </div>

<script>
const store = {
  read(k,f){try{return JSON.parse(localStorage.getItem(k))??f}catch{return f}},
  write(k,v){localStorage.setItem(k,JSON.stringify(v))}
};
if(!store.read('users')) store.write('users',[]);
if(!store.read('currentUser')) store.write('currentUser',null);

const profileCard = document.getElementById('profileCard');
const authCard = document.getElementById('authCard');

function refresh(){
  const cu = store.read('currentUser',null);
  if(cu){
    profileCard.style.display='block'; authCard.style.display='none';
    document.getElementById('p_name').value = cu.name||'';
    document.getElementById('p_email').value = cu.email||'';
    document.getElementById('p_phone').value = cu.phone||'';
    document.getElementById('p_password').value = '';
  }else{
    profileCard.style.display='none'; authCard.style.display='block';
  }
}
refresh();

function uid(){ return 'u_'+Math.random().toString(36).slice(2,9) }
function hash(t){ return btoa(unescape(encodeURIComponent(t))) } // simple

document.getElementById('signup').onclick = ()=>{
  const name = document.getElementById('s_name').value.trim();
  const email = document.getElementById('s_email').value.trim().toLowerCase();
  const phone = document.getElementById('s_phone').value.trim();
  const pass = document.getElementById('s_pass').value;
  if(!name || !email || !pass){ alert('Please fill name, email, and password.'); return; }
  const users = store.read('users',[]);
  if(users.some(u=>u.email===email)){ alert('Email already registered.'); return; }
  const user = {id:uid(),name,email,phone,password:hash(pass)};
  users.push(user); store.write('users',users); store.write('currentUser',user);
  alert('Account created!'); window.location='index.php';
};

document.getElementById('login').onclick = ()=>{
  const email = document.getElementById('l_email').value.trim().toLowerCase();
  const pass = document.getElementById('l_pass').value;
  const users = store.read('users',[]);
  const u = users.find(x=>x.email===email && x.password===hash(pass));
  if(!u){ alert('Invalid email or password'); return; }
  store.write('currentUser',u); alert('Welcome back!'); window.location='index.php';
};

document.getElementById('saveProfile').onclick = ()=>{
  const cu = store.read('currentUser',null); if(!cu) return;
  const users = store.read('users',[]);
  cu.name = document.getElementById('p_name').value.trim();
  cu.email = document.getElementById('p_email').value.trim().toLowerCase();
  cu.phone = document.getElementById('p_phone').value.trim();
  const newPass = document.getElementById('p_password').value;
  if(newPass) cu.password = hash(newPass);
  const i = users.findIndex(u=>u.id===cu.id); if(i>-1) users[i]=cu;
  store.write('users',users); store.write('currentUser',cu);
  alert('Saved!');
};

document.getElementById('logout').onclick = ()=>{ store.write('currentUser',null); window.location='index.php' };
</script>
</body>
</html>
