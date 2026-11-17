/* ===== MODERN UPGRADE: [description, chef tip, kcal per serving] ===== */
const DD=[
["Crisp spinach, juicy strawberries and tangy feta in a sweet balsamic dressing.","Toast the pecans in a dry pan for 2 minutes — it doubles the flavour.",260],
["Juicy pan-seared pork with garlicky roasted greens and crispy potatoes.","Pat the pork dry before searing so it browns instead of steaming.",520],
["Silky, velvety tomato soup with a hint of fresh basil. Pure comfort in a bowl.","A pinch of sugar balances the acidity of the tomatoes.",190],
["Warm roasted cauliflower and chickpeas with sweet pomegranate pops and zesty lemon.","Roast on a single layer so the edges caramelise.",290],
["Pillowy ravioli in a rich garlicky tomato sauce, ready in under 10 minutes of cooking.","Save a splash of pasta water — it makes the sauce glossy.",430],
["Crisp-edged waffles with toasted pecans, cinnamon and a pour of maple syrup.","Rest the batter 5 minutes for fluffier waffles.",380],
["Restaurant-style paneer in a creamy, buttery tomato gravy.","Soak paneer in warm water for 10 minutes to keep it soft.",430],
["Fragrant layered rice and vegetables slow-cooked with whole spices.","Seal the pot with a tight lid or dough for proper dum steam.",410],
["Paper-thin golden crepe stuffed with spiced potato masala.","The tawa must be hot, then wiped with a cut onion before spreading.",250],
["Charred, bubbly crust with tangy tomato and melty mozzarella.","Get your oven as hot as it will go and preheat the tray.",640],
["Deep, fudgy squares with a shiny crackly top.","Pull them out when a skewer still has moist crumbs — they firm up as they cool.",290],
["Better-than-takeaway fried rice with crunchy vegetables and smoky garlic.","Day-old cold rice gives separate, non-mushy grains.",330],
["Thick, creamy mango yoghurt drink with a whiff of cardamom.","Freeze the mango chunks first for an extra cold, thick lassi.",210],
];
const EI={spinach:"🥬",strawberr:"🍓",feta:"🧀",cheese:"🧀",mozzarella:"🧀",parmesan:"🧀",pecan:"🌰",pork:"🥩",chicken:"🍗",potato:"🥔",garlic:"🧄",tomato:"🍅",onion:"🧅",cream:"🥛",milk:"🥛",yoghurt:"🥛",basil:"🌿",mint:"🌿",parsley:"🌿",coriander:"🌿",chickpea:"🫘",bean:"🫘",lentil:"🫘",cauliflower:"🥦",broccoli:"🥦",lemon:"🍋",pomegranate:"🍎",egg:"🥚",butter:"🧈",ghee:"🧈",flour:"🌾",rice:"🍚",sugar:"🍬",chocolate:"🍫",cocoa:"🍫",mango:"🥭",avocado:"🥑",bread:"🍞",sourdough:"🍞",carrot:"🥕",pea:"🟢",chilli:"🌶️",pepper:"🌶️",oil:"🫒",olive:"🫒",pasta:"🍝",fettuccine:"🍝",ravioli:"🍝",paneer:"🧀",maple:"🍁",cinnamon:"🟤",saffron:"✨",ginger:"🫚",salt:"🧂",masala:"🌶️",water:"💧",ice:"🧊"};
const ie=t=>{t=t.toLowerCase();for(const k in EI)if(t.includes(k))return EI[k];return"🍽️"};
const info=r=>{const d=DD[r.id]||[r.cat+" recipe shared by our community.","Taste as you go and adjust the seasoning at the end.",300];return{desc:d[0],tip:d[1],kcal:d[2],veg:!/chicken|pork|fish|mutton|beef|prawn/i.test(r.ing.join()+r.t),tot:r.prep+r.cook,lvl:r.prep+r.cook<=20?"🟢 Easy":r.prep+r.cook<=40?"🟡 Medium":"🟠 Hearty"}};
let vegOnly=false,quickOnly=false,S={id:null,sv:1,cm:-1,done:{}};
const sc=(t,f)=>f==1?t:t.replace(/^(\d+(?:\.\d+)?)/,m=>+(m*f).toFixed(1));
function confetti(a){const E=a||["🎉","🍓","🥑","✨","🍕","🍰"];for(let i=0;i<26;i++){const e=document.createElement("span");e.className="cf";e.textContent=E[i%E.length];e.style.left=Math.random()*100+"vw";e.style.animationDelay=Math.random()*.6+"s";document.body.appendChild(e);setTimeout(()=>e.remove(),3200)}}
function theme(){const d=document.documentElement,dk=d.dataset.theme?d.dataset.theme=="dark":matchMedia("(prefers-color-scheme:dark)").matches;d.dataset.theme=dk?"light":"dark";$("thm").textContent=dk?"🌙":"☀️"}
function surprise(){const l=all();const r=l[Math.floor(Math.random()*l.length)];toast("🎲 How about "+r.t+"?");openR(r.id)}
const CARD=(r,i)=>{const d=info(r);return`<div class="card" style="animation-delay:${Math.min(i,12)*45}ms" onclick="openR(${r.id})"><div class="ph"><span class="fb">${EM[r.cat]||"🍽️"}</span>${imgTag(r)}<div class="bd"><span>${d.veg?"🟢 Veg":"🔴 Non-veg"}</span></div><button class="lk ${likes[r.id]?"on":""}" aria-label="Like" onclick="event.stopPropagation();like(${r.id})"><span>${likes[r.id]?"♥":"♡"}</span></button><span class="tm">⏱ ${d.tot} min</span></div><div class="cb"><h3>${esc(r.t)}</h3><p class="mt"><span>🔥 ${d.kcal} kcal</span><i>👥 ${r.serv}</i></p></div></div>`};
function showDashboard(){if(!me){toast("Log in to open your personal dashboard");logout();return}renderDashboard();$("dashboard").classList.remove("hide");$("top").classList.add("dashboard-view");history.replaceState(null,"","#dashboard");window.scrollTo(0,0)}
function leaveDashboard(){$("top").classList.remove("dashboard-view");$("dashboard").classList.add("hide")}
function renderDashboard(){
 if(!me||!$("dash-mine"))return;
 const mineNow=mine.filter(r=>r.own&&r.owner===me),faves=all().filter(r=>likes[r.id]),quick=all().filter(r=>r.prep+r.cook<=30);
 $("dash-welcome").textContent="Welcome back, "+me+". Here are your recipes and saved favourites.";
 $("dash-recipe-count").textContent=mineNow.length;$("dash-favorite-count").textContent=faves.length;$("dash-quick-count").textContent=quick.length;
 $("dash-mine").innerHTML=mineNow.length?mineNow.slice().reverse().slice(0,3).map(CARD).join(""):'<div class="dash-empty"><span>ADD</span><b>Your recipe collection is ready.</b><small>Add a recipe you love to see it here.</small></div>';
 $("dash-faves").innerHTML=faves.length?faves.slice(0,3).map(CARD).join(""):'<div class="dash-empty"><span>♡</span><b>No favourites saved yet.</b><small>Tap the heart on a recipe to keep it close.</small></div>'
}
function render(){
 const q=$("q").value.toLowerCase(),so=$("so")?$("so").value:"";
 const dishes=all();$("total-recipes").textContent=dishes.length;$("veg-recipes").textContent=dishes.filter(r=>info(r).veg).length;$("total-categories").textContent=new Set(dishes.map(r=>r.cat)).size;
 let l=all().filter(r=>(cat=="All"||(cat=="♥ Liked"?likes[r.id]:cat=="My recipes"?r.own&&r.owner===me:r.cat==cat))&&(!q||(r.t+r.ing.join()).toLowerCase().includes(q))&&(!vegOnly||info(r).veg)&&(!quickOnly||r.prep+r.cook<=30));
 if(so=="t")l=l.slice().sort((a,b)=>a.prep+a.cook-b.prep-b.cook);if(so=="k")l=l.slice().sort((a,b)=>info(a).kcal-info(b).kcal);
 $("count").textContent="· "+l.length+" dishes";
 $("grid").innerHTML=l.length?l.slice(0,shown).map(CARD).join(""):'<div class="empty">😕 No recipes found — try another filter</div>';hydrate($("grid"));$("more").classList.toggle("hide",l.length<=shown);
 $("quick").innerHTML=all().filter(r=>r.prep+r.cook<=20).slice(0,8).map(r=>`<button class="qk" onclick="openR(${r.id})">${EM[r.cat]||"🍽️"} ${esc(r.t)}<i>${r.prep+r.cook} mins</i></button>`).join("")}
function like(id){likes[id]=!likes[id];saveLikes();render();renderDashboard();if(S.id==id&&!$("modal").classList.contains("hide")&&$("mbody"))paint()}
function openR(id){const r=all().find(x=>x.id==id);S={id:id,sv:r.serv,cm:-1,done:{}};$("modal").classList.remove("hide");
 $("md").innerHTML=`<div class="mh"><span class="fb">${EM[r.cat]||"🍽️"}</span>${imgTag(r)}<button class="x" aria-label="Close" onclick="closeM()">✕</button></div><div class="mb" id="mbody"></div>`;hydrate($("md"));paint()}
function ingHTML(r){return r.ing.map((x,i)=>`<li><label class="ck"><input type="checkbox"><span>${ie(x)} ${esc(sc(x,S.sv/r.serv))}</span></label></li>`).join("")}
function paint(){const r=all().find(x=>x.id==S.id),d=info(r);
 if(S.cm>=0){const n=r.steps.length,t=r.steps[S.cm],m=t.match(/(\d+)(?:–\d+)?\s*min/);
  $("mbody").innerHTML=`<div class="cmb"><span class="tag">👩‍🍳 Cook mode · ${esc(r.t)}</span><div class="num">${S.cm+1}<small style="font-size:1rem;color:var(--muted)"> / ${n}</small></div><div class="pg"><i style="width:${(S.cm+1)/n*100}%"></i></div><p class="big">${esc(t)}</p>${m?`<button class="btn g" onclick="tmr(this,${m[1]*60})">⏱ Start ${m[1]} min timer</button>`:""}<div class="rw"><button class="btn g" onclick="S.cm=${S.cm>0?S.cm-1:-1};paint()">← ${S.cm>0?"Back":"Exit"}</button><button class="btn" onclick="${S.cm<n-1?"S.cm++;paint()":"S.cm=-1;paint();confetti();toast('🎉 Bon appétit!')"}">${S.cm<n-1?"Next →":"Done 🎉"}</button></div></div>`;return}
 $("mbody").innerHTML=`<span class="tag">${esc(r.cat)}</span> <span class="tag">${d.veg?"🟢 Veg":"🔴 Non-veg"}</span> <span class="tag">${d.lvl}</span><h2>${esc(r.t)}</h2><p class="desc">${esc(d.desc)}</p>
 <div class="st"><div><b>⏱ ${r.prep}m</b>Prep</div><div><b>🔥 ${r.cook}m</b>Cook</div><div><b>🍽️ ${d.kcal}</b>kcal / serving</div></div>
 <h3>🛒 Ingredients <span class="sv"><button aria-label="Fewer" onclick="sv(-1)">−</button><b id="svn">${S.sv}</b> servings<button aria-label="More" onclick="sv(1)">+</button></span></h3><ul class="cl" id="ingl">${ingHTML(r)}</ul>
 <div class="rw" style="margin:0 0 6px"><button class="btn g" onclick="copyList()">📋 Copy shopping list</button><button class="btn" onclick="S.cm=0;paint()">👩‍🍳 Cook mode</button></div>
 <h3>👩‍🍳 Instructions</h3><div class="pg"><i id="pgb"></i></div><small id="pgt" style="color:var(--muted)">0 of ${r.steps.length} steps done</small>
 <ol class="cl">${r.steps.map((t,i)=>{const m=t.match(/(\d+)(?:–\d+)?\s*min/);return`<li><label class="ck"><input type="checkbox" onchange="chk(${i},this)"><span><b>Step ${i+1}.</b> ${esc(t)}</span></label>${m?`<button class="ts" onclick="tmr(this,${m[1]*60})">⏱ ${m[1]}:00</button>`:""}</li>`}).join("")}</ol>
 <div class="tip">💡 <b>Chef's tip:</b> ${esc(d.tip)}</div>
 <div class="rw"><button class="btn g" onclick="like(${r.id})">${likes[r.id]?"♥ Liked":"♡ Like"}</button>${r.own&&me&&r.owner===me?`<button class="btn" onclick="openForm(${r.id})">✏️ Edit</button><button class="btn g" onclick="del(${r.id})">🗑️ Delete</button>`:""}</div>`}
function sv(n){const r=all().find(x=>x.id==S.id);S.sv=Math.max(1,Math.min(24,S.sv+n));$("svn").textContent=S.sv;$("ingl").innerHTML=ingHTML(r)}
function chk(i,el){const r=all().find(x=>x.id==S.id);S.done[i]=el.checked;const n=Object.values(S.done).filter(Boolean).length;$("pgb").style.width=n/r.steps.length*100+"%";$("pgt").textContent=n+" of "+r.steps.length+" steps done";if(n==r.steps.length){confetti();toast("🎉 All done — enjoy your meal!")}}
function tmr(b,s){if(b._t){clearInterval(b._t);b._t=0;b.textContent="⏱ Restart";return}const f=()=>{b.textContent="⏳ "+Math.floor(s/60)+":"+String(s%60).padStart(2,"0")};f();b._t=setInterval(()=>{s--;f();if(s<=0){clearInterval(b._t);b._t=0;b.textContent="✅ Time's up!";toast("⏰ Timer finished!");try{navigator.vibrate&&navigator.vibrate([200,100,200])}catch(e){}}},1000)}
function copyList(){const r=all().find(x=>x.id==S.id),t="🛒 "+r.t+" ("+S.sv+" servings)\n"+r.ing.map(x=>"• "+sc(x,S.sv/r.serv)).join("\n");try{navigator.clipboard.writeText(t).then(()=>toast("📋 Shopping list copied"),()=>toast(t))}catch(e){toast("Copy not supported")}}
document.querySelectorAll(".collage div").forEach(async d=>{const u=await wimg(d.dataset.w);if(u){const i=new Image();i.alt="";i.onload=()=>d.appendChild(i);i.src=u.replace(/\/\d+px-/,"/480px-")}});
