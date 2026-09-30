import{r as d,j as n}from"./app-DGOIQozq.js";/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Z=t=>t==null?void 0:t.replace(/([a-z0-9])([A-Z])/g,"$1-$2").toLowerCase();/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */function G(t,e,a=[]){if(e==null)throw new Error("[lucide]: iconNode is required when icon name is used");return{name:Z(t),size:24,node:e,...a.length>0?{aliases:a}:{}}}/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const J=t=>{let e="",a=!1;for(const o of t){if(o==="-"||o==="_"||o<=" "){a=e.length>0;continue}e.length===0?e+=o.toLowerCase():e+=a?o.toUpperCase():o,a=!1}return e};/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const Q=t=>{const e=J(t);return e.charAt(0).toUpperCase()+e.slice(1)};/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const C=(...t)=>t.filter((e,a,o)=>!!e&&e.trim()!==""&&o.indexOf(e)===a).join(" ").trim();/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const w={xmlns:"http://www.w3.org/2000/svg",width:24,height:24,viewBox:"0 0 24 24",fill:"none",stroke:"currentColor","stroke-width":2,"stroke-linecap":"round","stroke-linejoin":"round"};/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */function j(t){return t!=null}function T(t,e={}){var m,y;const a=e.attributeNames??{},o=r=>a[r]??r,x=t.size??t.width??w.width,h=t.size??t.height??w.height,f=((m=t.aliases)==null?void 0:m.filter(r=>typeof r=="string"&&r.trim()!=="").map(r=>`lucide-${r}`))??[],i=[...t.name?[`lucide-${t.name}`]:[],...f],c=((y=e.className)==null?void 0:y.split(" ").filter(Boolean))??[],l=e.includeDefaultClasses===!1?C(...c):C("lucide",...i,...c),u=e.absoluteStrokeWidth?Number(e.strokeWidth??w["stroke-width"])*Number(t.size??t.width??w.width)/Number(e.size??e.width??w.width):e.strokeWidth??w["stroke-width"];return["svg",{...Object.entries(w).reduce((r,[k,b])=>(r[o(k)]=b,r),{}),..."color"in e&&e.color&&{[o("stroke")]:e.color},..."size"in e&&j(e.size)&&{[o("width")]:e.size,[o("height")]:e.size},..."width"in e&&j(e.width)&&{[o("width")]:e.width},..."height"in e&&j(e.height)&&{[o("height")]:e.height},[o("stroke-width")]:u,...l&&{[o("class")]:l},[o("viewBox")]:`0 0 ${x} ${h}`,...e.hasA11yProp===!1?{[o("aria-hidden")]:"true"}:{},..."attributes"in e&&e.attributes},t.node.map(r=>{const[k,b,g]=r,v=e.nonScalingStroke?{[o("vector-effect")]:"non-scaling-stroke",...b}:b;return g?[k,v,g]:[k,v]})]}/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */function Y(t,e={}){return T(t,{...e,attributeNames:{...e.attributeNames,class:"className","stroke-width":"strokeWidth","stroke-linecap":"strokeLinecap","stroke-linejoin":"strokeLinejoin","vector-effect":"vectorEffect"}})}/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const ee=t=>{for(const e in t)if(e.startsWith("aria-")||e==="role"||e==="title")return!0;return!1},te=d.createContext({}),ne=()=>d.useContext(te),oe=d.forwardRef(({color:t,size:e,width:a,height:o,strokeWidth:x,absoluteStrokeWidth:h,nonScalingStroke:f,className:i="",children:c,iconNode:l=[],icon:u={node:l,aliases:[],size:24},...p},m)=>{const{size:y=24,strokeWidth:r=2,absoluteStrokeWidth:k=!1,nonScalingStroke:b=!1,color:g="currentColor",className:v=""}=ne()??{},V=!!c||ee(p),[F,O,U=[]]=Y(u,{color:t??g,width:a??e??y,height:o??e??y,strokeWidth:x??r,absoluteStrokeWidth:h??k,nonScalingStroke:f??b,className:C(v,i),hasA11yProp:V,attributes:p});return d.createElement(F,{ref:m,...O},[...U.map(([K,X])=>d.createElement(K,X)),...Array.isArray(c)?c:[c]])});/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */function s(t,e=[],a=[]){const o=typeof t=="string"?G(t,e,a):t,x=d.forwardRef(({className:h,...f},i)=>d.createElement(oe,{ref:i,icon:o,className:h,...f}));return o.name&&(x.displayName=Q(o.name)),x}/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const N={name:"bell",size:24,node:[["path",{d:"M10.268 21a2 2 0 0 0 3.464 0",key:"vwvbt9"}],["path",{d:"M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326",key:"11g9vi"}]]};N.node;const de=s(N);/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const _={name:"book-open",size:24,node:[["path",{d:"M12 5v16",key:"1f6ucr"}],["path",{d:"M20.001 19A2 2 0 0022 17V5a2 2 0 00-1.999-2L16 3.002A5 5 0 0012 5a5 5 0 00-4-2H4a2 2 0 00-2 2v12a2 2 0 001.999 2H8a5 5 0 014 2 5 5 0 014-2z",key:"1fyvmf"}]]};_.node;const ce=s(_);/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const S={name:"check",size:24,node:[["path",{d:"M20 6 9 17l-5-5",key:"1gmf2c"}]]};S.node;const se=s(S);/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const M={name:"chevron-down",size:24,node:[["path",{d:"m6 9 6 6 6-6",key:"qrunsl"}]]};M.node;const le=s(M);/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const D={name:"chevron-left",size:24,node:[["path",{d:"m15 18-6-6 6-6",key:"1wnfg3"}]]};D.node;const he=s(D);/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const P={name:"chevron-right",size:24,node:[["path",{d:"m9 18 6-6-6-6",key:"mthhwq"}]]};P.node;const ue=s(P);/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const A={name:"circle-alert",size:24,node:[["circle",{cx:"12",cy:"12",r:"10",key:"1mglay"}],["line",{x1:"12",x2:"12",y1:"8",y2:"12",key:"1pkeuh"}],["line",{x1:"12",x2:"12.01",y1:"16",y2:"16",key:"4dfq90"}]],aliases:["alert-circle"]};A.node;const xe=s(A);/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const $={name:"circle-check-big",size:24,node:[["path",{d:"M21.801 10A10 10 0 1 1 17 3.335",key:"yps3ct"}],["path",{d:"m9 11 3 3L22 4",key:"1pflzl"}]],aliases:["check-circle"]};$.node;const fe=s($);/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const L={name:"download",size:24,node:[["path",{d:"M12 15V3",key:"m9g1x1"}],["path",{d:"M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4",key:"ih7n3h"}],["path",{d:"m7 10 5 5 5-5",key:"brsn70"}]]};L.node;const z=s(L);/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const W={name:"info",size:24,node:[["circle",{cx:"12",cy:"12",r:"10",key:"1mglay"}],["path",{d:"M12 16v-4",key:"1dtifu"}],["path",{d:"M12 8h.01",key:"e9boi3"}]]};W.node;const me=s(W);/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const E={name:"layout-dashboard",size:24,node:[["rect",{width:"7",height:"9",x:"3",y:"3",rx:"1",key:"10lvy0"}],["rect",{width:"7",height:"5",x:"14",y:"3",rx:"1",key:"16une8"}],["rect",{width:"7",height:"9",x:"14",y:"12",rx:"1",key:"1hutg5"}],["rect",{width:"7",height:"5",x:"3",y:"16",rx:"1",key:"ldoo1y"}]]};E.node;const we=s(E);/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const I={name:"log-out",size:24,node:[["path",{d:"m16 17 5-5-5-5",key:"1bji2h"}],["path",{d:"M21 12H9",key:"dn1m92"}],["path",{d:"M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4",key:"1uf3rs"}]]};I.node;const pe=s(I);/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const B={name:"menu",size:24,node:[["path",{d:"M4 5h16",key:"1tepv9"}],["path",{d:"M4 12h16",key:"1lakjw"}],["path",{d:"M4 19h16",key:"1djgab"}]]};B.node;const ke=s(B);/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const H={name:"message-square",size:24,node:[["path",{d:"M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z",key:"18887p"}]]};H.node;const be=s(H);/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const q={name:"sparkles",size:24,node:[["path",{d:"M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z",key:"1s2grr"}],["path",{d:"M20 2v4",key:"1rf3ol"}],["path",{d:"M22 4h-4",key:"gwowj6"}],["circle",{cx:"4",cy:"20",r:"2",key:"6kqj1y"}]],aliases:["stars"]};q.node;const ae=s(q);/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const R={name:"x",size:24,node:[["path",{d:"M18 6 6 18",key:"1bl5f8"}],["path",{d:"m6 6 12 12",key:"d8bk6v"}]]};R.node;const re=s(R);function ye({variant:t="topbar",className:e=""}){const[a,o]=d.useState(!1),[x,h]=d.useState(!1),[f,i]=d.useState(!1);d.useEffect(()=>{if(window.matchMedia("(display-mode: standalone)").matches||window.navigator.standalone===!0){h(!0);return}window.deferredPwaPrompt&&o(!0);const u=()=>{o(!0)},p=()=>{h(!0),o(!1)};return window.addEventListener("pwa-prompt-ready",u),window.addEventListener("beforeinstallprompt",m=>{m.preventDefault(),window.deferredPwaPrompt=m,o(!0)}),window.addEventListener("appinstalled",p),()=>{window.removeEventListener("pwa-prompt-ready",u),window.removeEventListener("appinstalled",p)}},[]);const c=async l=>{if(l.preventDefault(),l.stopPropagation(),window.deferredPwaPrompt)try{window.deferredPwaPrompt.prompt(),(await window.deferredPwaPrompt.userChoice).outcome==="accepted"&&(h(!0),o(!1)),window.deferredPwaPrompt=null}catch(u){console.warn("PWA install error:",u),i(!0)}else i(!0)};return x?t==="menu"?n.jsx("div",{className:"flex items-center justify-between p-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-bold",children:n.jsxs("span",{className:"flex items-center gap-2",children:[n.jsx(se,{size:16}),"اپلیکیشن ادورا روی دستگاه شما نصب است"]})}):null:n.jsxs(n.Fragment,{children:[t==="topbar"?n.jsxs("button",{type:"button",onClick:c,className:`flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-cyan-500/30 dark:border-cyan-500/30 bg-gradient-to-tr from-cyan-500/10 via-brand-500/10 to-indigo-500/10 hover:from-cyan-500/20 hover:to-indigo-500/20 text-cyan-700 dark:text-cyan-300 font-extrabold text-xs shadow-xs hover:shadow-cyan-500/20 transition-all duration-200 active:scale-95 ${e}`,title:"نصب اپلیکیشن وب ادورا",children:[n.jsx(z,{size:14,className:"text-cyan-500 animate-bounce"}),n.jsx("span",{children:"نصب اپلیکیشن"})]}):n.jsxs("button",{type:"button",onClick:c,className:`w-full flex items-center justify-between p-3.5 rounded-2xl bg-gradient-to-r from-cyan-500/15 via-brand-500/15 to-indigo-500/15 border border-cyan-500/30 text-slate-800 dark:text-white hover:border-cyan-400 transition-all shadow-sm active:scale-98 ${e}`,children:[n.jsxs("div",{className:"flex items-center gap-3",children:[n.jsx("div",{className:"w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan-500 to-brand-600 flex items-center justify-center text-white shadow-md shadow-brand-500/20",children:n.jsx(z,{size:20})}),n.jsxs("div",{className:"text-right",children:[n.jsx("p",{className:"text-xs font-black text-slate-800 dark:text-white",children:"نصب نسخه وب‌اپلیکیشن (PWA)"}),n.jsx("p",{className:"text-[11px] text-slate-500 dark:text-slate-400 mt-0.5",children:"اجرای تمام‌صفحه و دسترسی سریع بدون نیاز به مرورگر"})]})]}),n.jsx("span",{className:"px-2.5 py-1 rounded-lg bg-cyan-500 text-white text-[11px] font-extrabold shadow-xs",children:"نصب"})]}),f&&n.jsx("div",{className:"fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4 animate-in fade-in duration-200",onClick:()=>i(!1),children:n.jsxs("div",{className:"bg-white dark:bg-[#0b1730] border border-slate-200 dark:border-cyan-500/30 rounded-3xl p-6 max-w-sm w-full shadow-2xl text-right animate-in zoom-in-95 duration-200",onClick:l=>l.stopPropagation(),dir:"rtl",children:[n.jsxs("div",{className:"flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800",children:[n.jsxs("div",{className:"flex items-center gap-2",children:[n.jsx(ae,{size:18,className:"text-cyan-500"}),n.jsx("h3",{className:"text-sm font-bold text-slate-800 dark:text-white",children:"راهنمای نصب اپلیکیشن"})]}),n.jsx("button",{type:"button",onClick:()=>i(!1),className:"p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200",children:n.jsx(re,{size:16})})]}),n.jsxs("div",{className:"py-4 space-y-3 text-xs text-slate-600 dark:text-slate-300 leading-relaxed",children:[n.jsxs("p",{children:[n.jsx("strong",{children:"در مرورگر گوگل کروم (کامپیوتر یا گوشی):"}),n.jsx("br",{}),"روی آیکون سه‌نقطه یا آیکون دانلود در انتهای نوار آدرس کلیک کنید و گزینه ",n.jsx("strong",{children:"«Install Edvora»"})," یا ",n.jsx("strong",{children:"«افزودن به صفحه اصلی»"})," را انتخاب فرمایید."]}),n.jsxs("p",{children:[n.jsx("strong",{children:"در مرورگر Safari (آیفون / آیپد):"}),n.jsx("br",{}),"دکمه اشتراک‌گذاری ",n.jsx("strong",{children:"(Share)"})," را در پایین صفحه لمس کرده و گزینه ",n.jsx("strong",{children:"«Add to Home Screen»"})," را بزنید."]})]}),n.jsx("button",{type:"button",onClick:()=>i(!1),className:"w-full py-2.5 rounded-xl bg-gradient-to-r from-brand-600 to-cyan-500 text-white font-bold text-xs shadow-md",children:"متوجه شدم"})]})})]})}export{ce as B,se as C,z as D,me as I,we as L,be as M,ye as P,ae as S,re as X,xe as a,he as b,s as c,ue as d,pe as e,ke as f,de as g,le as h,fe as i};
