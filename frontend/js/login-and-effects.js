/* ===== v4: photos wired in, lively login, photo strip ===== */
(function(){
const T=R.map(r=>r.t),mk=(s,c)=>{const i=new Image();i.alt="";i.src=s;if(c)i.className=c;return i};
const S=[7,9,6,5,10,2,8],sl=$("slides"),bl=$("bubbles"),dish=$("dish");
S.forEach(k=>sl.appendChild(mk(IMG[k])));
[[4,8,96,0],[78,6,120,-3],[2,62,110,-6],[82,58,90,-2],[44,84,100,-5],[60,-5,80,-8]].forEach((p,i)=>{const b=mk(IMG[S[(i*2+1)%S.length]],"bub");b.style.cssText=`left:${p[0]}%;top:${p[1]}%;width:${p[2]}px;height:${p[2]}px;animation-duration:${7+i*1.3}s;animation-delay:${p[3]}s`;bl.appendChild(b)});
let c=0,pv=-1;const E=sl.children;
function show(){[...E].forEach(e=>e.className="");if(pv>=0)E[pv].className="off";E[c].className="on";dish.src=IMG[S[c]]}
show();setInterval(()=>{pv=c;c=(c+1)%E.length;show()},4500);
const st=$("strip"),fr=document.createDocumentFragment();
T.forEach((t,i)=>{const b=document.createElement("button");b.className="si";b.onclick=()=>openR(i);b.appendChild(mk(IMG[i]));b.insertAdjacentHTML("beforeend","<b>"+esc(t)+"</b>");fr.appendChild(b)});
st.appendChild(fr.cloneNode(true));st.appendChild(fr);
const TM={Breakfast:5,Salad:0,Soup:2,Main:9,Indian:6,Dessert:10,Drinks:12},tl=document.querySelector(".tiles");tl.innerHTML="";
Object.keys(TM).forEach(k=>{const b=document.createElement("button");b.className="tile";b.onclick=()=>goCat(k);b.appendChild(mk(IMG[TM[k]]));b.insertAdjacentHTML("beforeend","<b>"+k+"</b>");tl.appendChild(b)});
document.querySelectorAll(".collage div").forEach((d,i)=>{d.innerHTML="";d.appendChild(mk(IMG[[9,6,10][i]]))});
})();