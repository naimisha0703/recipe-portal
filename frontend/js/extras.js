/* ===== v3: photos instead of emoji ===== */
const NE=/(?![♥♡])[\u{1F000}-\u{1FAFF}\u{2300}-\u{23FF}\u{2600}-\u{27BF}\u{2B00}-\u{2BFF}\uFE0F\u200D]\s?/gu;
function strip(n){if(n.nodeType==3){const t=n.nodeValue.replace(NE,"");if(t!==n.nodeValue)n.nodeValue=t}else if(n.nodeType==1&&!/^(SCRIPT|STYLE|TEXTAREA)$/.test(n.tagName)){n.childNodes.forEach(strip)}}
function art(t){let h=0;for(const c of t||"x")h=(h*31+c.charCodeAt(0))>>>0;const P=[[14,60],[28,55],[40,50],[95,40],[8,65],[350,45]],[hu,sa]=P[h%6],r=n=>(h=(h*1103515245+12345)>>>0)%n;
 let f="";for(let i=0;i<16;i++){const a=r(628)/100,d=r(62),x=200+Math.cos(a)*d*1.5,y=150+Math.sin(a)*d*1.1,z=14+r(22);f+=`<circle cx="${x|0}" cy="${y|0}" r="${z}" fill="hsl(${hu+r(30)-10},${sa+r(25)}%,${38+r(25)}%)"/>`}
 for(let i=0;i<7;i++)f+=`<ellipse cx="${150+r(100)}" cy="${110+r(80)}" rx="${5+r(7)}" ry="${3+r(4)}" fill="hsl(${95+r(30)},55%,${32+r(18)}%)" transform="rotate(${r(180)} 200 150)"/>`;
 const svg=`<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"><defs><linearGradient id="w" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#8a5a36"/><stop offset="1" stop-color="#5b3820"/></linearGradient><radialGradient id="p"><stop offset=".7" stop-color="#fff"/><stop offset="1" stop-color="#e8e2d9"/></radialGradient></defs><rect width="400" height="300" fill="url(#w)"/><ellipse cx="206" cy="162" rx="128" ry="110" fill="#0004"/><circle cx="200" cy="150" r="118" fill="url(#p)"/><circle cx="200" cy="150" r="88" fill="none" stroke="#d9d2c6" stroke-width="3"/>${f}</svg>`;
 return `url("data:image/svg+xml,${encodeURIComponent(svg)}")`}
function artFix(root){root.querySelectorAll(".ph:not([data-a]),.mh:not([data-a])").forEach(e=>{const im=e.querySelector("img"),t=im?im.alt:"dish";e.dataset.a=1;e.style.backgroundImage=art(t)});
 root.querySelectorAll(".collage div:not([data-a])").forEach(e=>{e.dataset.a=1;e.style.backgroundImage=art(e.dataset.w)})}
const TL=[["Breakfast","Waffle"],["Salad","Spinach_salad"],["Soup","Tomato_soup"],["Main","Pizza_Margherita"],["Indian","Butter_chicken"],["Dessert","Chocolate_brownie"],["Drinks","Lassi"]];
document.querySelector(".tiles").innerHTML=TL.map(t=>`<button class="tile" onclick="goCat('${t[0]}')"><img alt="${t[0]}" data-w="${t[1]}" data-f=""><b>${t[0]}</b></button>`).join("");
hydrate(document.querySelector(".tiles"));
(async()=>{const u=await wimg("Biryani");const b=document.querySelector("#auth .bgp");if(u&&b)b.src=u.replace(/\/\d+px-/,"/960px-")})();
const _toast=toast;toast=function(m){_toast(String(m).replace(NE,""))};
function theme(){const d=document.documentElement,dk=d.dataset.theme?d.dataset.theme=="dark":matchMedia("(prefers-color-scheme:dark)").matches;d.dataset.theme=dk?"light":"dark";$("thm").textContent=dk?"Dark":"Light"}
new MutationObserver(m=>{m.forEach(x=>{x.addedNodes.forEach(n=>{strip(n);if(n.nodeType==1)artFix(n.parentNode||document)});if(x.type=="characterData")strip(x.target)})}).observe(document.body,{childList:true,subtree:true,characterData:true});
strip(document.body);artFix(document);$("bgfl").innerHTML="";