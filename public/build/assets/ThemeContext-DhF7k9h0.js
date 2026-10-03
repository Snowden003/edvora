import{c as s}from"./x-Dcwryv6P.js";import{r as o,j as l}from"./app-DmYkvZIa.js";/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const c={name:"circle-question-mark",size:24,node:[["circle",{cx:"12",cy:"12",r:"10",key:"1mglay"}],["path",{d:"M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3",key:"1u773s"}],["path",{d:"M12 17h.01",key:"p32p05"}]],aliases:["help-circle","circle-help"]};c.node;const p=s(c);/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const n={name:"house",size:24,node:[["path",{d:"M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8",key:"5wwlr5"}],["path",{d:"M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z",key:"r6nss1"}]],aliases:["home"]};n.node;const y=s(n);/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const d={name:"moon",size:24,node:[["path",{d:"M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401",key:"kfwtm"}]]};d.node;const g=s(d);/**
 * @license lucide-react v1.42.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const m={name:"sun",size:24,node:[["circle",{cx:"12",cy:"12",r:"4",key:"4exip2"}],["path",{d:"M12 2v2",key:"tus03m"}],["path",{d:"M12 20v2",key:"1lh1kg"}],["path",{d:"m4.93 4.93 1.41 1.41",key:"149t6j"}],["path",{d:"m17.66 17.66 1.41 1.41",key:"ptbguv"}],["path",{d:"M2 12h2",key:"1t8f8n"}],["path",{d:"M20 12h2",key:"1q8mjw"}],["path",{d:"m6.34 17.66-1.41 1.41",key:"1m8zz5"}],["path",{d:"m19.07 4.93-1.41 1.41",key:"1shlcs"}]]};m.node;const v=s(m),h=o.createContext();function _({children:a}){const[t,r]=o.useState(()=>{try{const e=localStorage.getItem("edvora_theme")||localStorage.getItem("edvora_student_theme");return e==="dark"||e==="light"?e:"dark"}catch{return"dark"}});o.useEffect(()=>{try{localStorage.setItem("edvora_theme",t),localStorage.setItem("edvora_student_theme",t);const e=document.documentElement;t==="dark"?(e.classList.add("dark"),e.setAttribute("data-theme","dark")):(e.classList.remove("dark"),e.setAttribute("data-theme","light"))}catch{}},[t]);const i=()=>{r(e=>e==="dark"?"light":"dark")};return l.jsx(h.Provider,{value:{theme:t,isDark:t==="dark",setTheme:r,toggleTheme:i},children:a})}function x(){const a=o.useContext(h);return a||{theme:"dark",isDark:!0,setTheme:()=>{},toggleTheme:()=>{}}}export{p as C,y as H,g as M,v as S,_ as T,x as u};
