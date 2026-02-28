import{ad as Se,D as w,y as $,p as f,s as I,q as R,at as Be,au as Ve,av as ze,d as S,aa as Re,r as y,A as Ae,ak as De,aw as Te,ax as Me,ay as Ie,z as Z,B as qe,c as O,C as Fe,al as Pe,Q as Ue,az as Ne,aA as Le,aB as He,I as X,aC as K,o as b,X as q,h as V,a1 as L,V as J,aD as Ee,_ as z,a as v,e as t,aE as Ke,t as _,f as A,Z as W,b as g,a3 as Oe,i as N,G as ee,a2 as j,a7 as te,a8 as ae,g as je,aF as Ge,H as We,F as Qe}from"./index-wW9rBK64.js";import{T as oe}from"./Button.vue_vue_type_style_index_0_scoped_39e4a762_lang-BnSR8He6.js";const Xe=Se("n-checkbox-group"),Ye=()=>w("svg",{viewBox:"0 0 64 64",class:"check-icon"},w("path",{d:"M50.42,16.76L22.34,39.45l-8.1-11.46c-1.12-1.58-3.3-1.96-4.88-0.84c-1.58,1.12-1.95,3.3-0.84,4.88l10.26,14.51  c0.56,0.79,1.42,1.31,2.38,1.45c0.16,0.02,0.32,0.03,0.48,0.03c0.8,0,1.57-0.27,2.2-0.78l30.99-25.03c1.5-1.21,1.74-3.42,0.52-4.92  C54.13,15.78,51.93,15.55,50.42,16.76z"})),Ze=()=>w("svg",{viewBox:"0 0 100 100",class:"line-icon"},w("path",{d:"M80.2,55.5H21.4c-2.8,0-5.1-2.5-5.1-5.5l0,0c0-3,2.3-5.5,5.1-5.5h58.7c2.8,0,5.1,2.5,5.1,5.5l0,0C85.2,53.1,82.9,55.5,80.2,55.5z"})),Je=$([f("checkbox",`
 font-size: var(--n-font-size);
 outline: none;
 cursor: pointer;
 display: inline-flex;
 flex-wrap: nowrap;
 align-items: flex-start;
 word-break: break-word;
 line-height: var(--n-size);
 --n-merged-color-table: var(--n-color-table);
 `,[I("show-label","line-height: var(--n-label-line-height);"),$("&:hover",[f("checkbox-box",[R("border","border: var(--n-border-checked);")])]),$("&:focus:not(:active)",[f("checkbox-box",[R("border",`
 border: var(--n-border-focus);
 box-shadow: var(--n-box-shadow-focus);
 `)])]),I("inside-table",[f("checkbox-box",`
 background-color: var(--n-merged-color-table);
 `)]),I("checked",[f("checkbox-box",`
 background-color: var(--n-color-checked);
 `,[f("checkbox-icon",[$(".check-icon",`
 opacity: 1;
 transform: scale(1);
 `)])])]),I("indeterminate",[f("checkbox-box",[f("checkbox-icon",[$(".check-icon",`
 opacity: 0;
 transform: scale(.5);
 `),$(".line-icon",`
 opacity: 1;
 transform: scale(1);
 `)])])]),I("checked, indeterminate",[$("&:focus:not(:active)",[f("checkbox-box",[R("border",`
 border: var(--n-border-checked);
 box-shadow: var(--n-box-shadow-focus);
 `)])]),f("checkbox-box",`
 background-color: var(--n-color-checked);
 border-left: 0;
 border-top: 0;
 `,[R("border",{border:"var(--n-border-checked)"})])]),I("disabled",{cursor:"not-allowed"},[I("checked",[f("checkbox-box",`
 background-color: var(--n-color-disabled-checked);
 `,[R("border",{border:"var(--n-border-disabled-checked)"}),f("checkbox-icon",[$(".check-icon, .line-icon",{fill:"var(--n-check-mark-color-disabled-checked)"})])])]),f("checkbox-box",`
 background-color: var(--n-color-disabled);
 `,[R("border",`
 border: var(--n-border-disabled);
 `),f("checkbox-icon",[$(".check-icon, .line-icon",`
 fill: var(--n-check-mark-color-disabled);
 `)])]),R("label",`
 color: var(--n-text-color-disabled);
 `)]),f("checkbox-box-wrapper",`
 position: relative;
 width: var(--n-size);
 flex-shrink: 0;
 flex-grow: 0;
 user-select: none;
 -webkit-user-select: none;
 `),f("checkbox-box",`
 position: absolute;
 left: 0;
 top: 50%;
 transform: translateY(-50%);
 height: var(--n-size);
 width: var(--n-size);
 display: inline-block;
 box-sizing: border-box;
 border-radius: var(--n-border-radius);
 background-color: var(--n-color);
 transition: background-color 0.3s var(--n-bezier);
 `,[R("border",`
 transition:
 border-color .3s var(--n-bezier),
 box-shadow .3s var(--n-bezier);
 border-radius: inherit;
 position: absolute;
 left: 0;
 right: 0;
 top: 0;
 bottom: 0;
 border: var(--n-border);
 `),f("checkbox-icon",`
 display: flex;
 align-items: center;
 justify-content: center;
 position: absolute;
 left: 1px;
 right: 1px;
 top: 1px;
 bottom: 1px;
 `,[$(".check-icon, .line-icon",`
 width: 100%;
 fill: var(--n-check-mark-color);
 opacity: 0;
 transform: scale(0.5);
 transform-origin: center;
 transition:
 fill 0.3s var(--n-bezier),
 transform 0.3s var(--n-bezier),
 opacity 0.3s var(--n-bezier),
 border-color 0.3s var(--n-bezier);
 `),Be({left:"1px",top:"1px"})])]),R("label",`
 color: var(--n-text-color);
 transition: color .3s var(--n-bezier);
 user-select: none;
 -webkit-user-select: none;
 padding: var(--n-label-padding);
 font-weight: var(--n-label-font-weight);
 `,[$("&:empty",{display:"none"})])]),Ve(f("checkbox",`
 --n-merged-color-table: var(--n-color-table-modal);
 `)),ze(f("checkbox",`
 --n-merged-color-table: var(--n-color-table-popover);
 `))]),et=Object.assign(Object.assign({},Z.props),{size:String,checked:{type:[Boolean,String,Number],default:void 0},defaultChecked:{type:[Boolean,String,Number],default:!1},value:[String,Number],disabled:{type:Boolean,default:void 0},indeterminate:Boolean,label:String,focusable:{type:Boolean,default:!0},checkedValue:{type:[Boolean,String,Number],default:!0},uncheckedValue:{type:[Boolean,String,Number],default:!1},"onUpdate:checked":[Function,Array],onUpdateChecked:[Function,Array],privateInsideTable:Boolean,onChange:[Function,Array]}),tt=S({name:"Checkbox",props:et,setup(e){const l=Re(Xe,null),a=y(null),{mergedClsPrefixRef:i,inlineThemeDisabled:c,mergedRtlRef:m}=Ae(e),r=y(e.defaultChecked),p=De(e,"checked"),x=Te(p,r),s=Me(()=>{if(l){const n=l.valueSetRef.value;return n&&e.value!==void 0?n.has(e.value):!1}else return x.value===e.checkedValue}),h=Ie(e,{mergedSize(n){const{size:B}=e;if(B!==void 0)return B;if(l){const{value:C}=l.mergedSizeRef;if(C!==void 0)return C}if(n){const{mergedSize:C}=n;if(C!==void 0)return C.value}return"medium"},mergedDisabled(n){const{disabled:B}=e;if(B!==void 0)return B;if(l){if(l.disabledRef.value)return!0;const{maxRef:{value:C},checkedCountRef:M}=l;if(C!==void 0&&M.value>=C&&!s.value)return!0;const{minRef:{value:P}}=l;if(P!==void 0&&M.value<=P&&s.value)return!0}return n?n.disabled.value:!1}}),{mergedDisabledRef:k,mergedSizeRef:D}=h,T=Z("Checkbox","-checkbox",Je,He,e,i);function u(n){if(l&&e.value!==void 0)l.toggleCheckbox(!s.value,e.value);else{const{onChange:B,"onUpdate:checked":C,onUpdateChecked:M}=e,{nTriggerFormInput:P,nTriggerFormChange:E}=h,U=s.value?e.uncheckedValue:e.checkedValue;C&&K(C,U,n),M&&K(M,U,n),B&&K(B,U,n),P(),E(),r.value=U}}function o(n){k.value||u(n)}function d(n){if(!k.value)switch(n.key){case" ":case"Enter":u(n)}}function H(n){switch(n.key){case" ":n.preventDefault()}}const se={focus:()=>{var n;(n=a.value)===null||n===void 0||n.focus()},blur:()=>{var n;(n=a.value)===null||n===void 0||n.blur()}},ne=qe("Checkbox",m,i),Q=O(()=>{const{value:n}=D,{common:{cubicBezierEaseInOut:B},self:{borderRadius:C,color:M,colorChecked:P,colorDisabled:E,colorTableHeader:U,colorTableHeaderModal:de,colorTableHeaderPopover:re,checkMarkColor:ie,checkMarkColorDisabled:ce,border:ue,borderFocus:be,borderDisabled:he,borderChecked:fe,boxShadowFocus:me,textColor:pe,textColorDisabled:ve,checkMarkColorDisabledChecked:ke,colorDisabledChecked:ge,borderDisabledChecked:ye,labelPadding:xe,labelLineHeight:Ce,labelFontWeight:we,[X("fontSize",n)]:_e,[X("size",n)]:$e}}=T.value;return{"--n-label-line-height":Ce,"--n-label-font-weight":we,"--n-size":$e,"--n-bezier":B,"--n-border-radius":C,"--n-border":ue,"--n-border-checked":fe,"--n-border-focus":be,"--n-border-disabled":he,"--n-border-disabled-checked":ye,"--n-box-shadow-focus":me,"--n-color":M,"--n-color-checked":P,"--n-color-table":U,"--n-color-table-modal":de,"--n-color-table-popover":re,"--n-color-disabled":E,"--n-color-disabled-checked":ge,"--n-text-color":pe,"--n-text-color-disabled":ve,"--n-check-mark-color":ie,"--n-check-mark-color-disabled":ce,"--n-check-mark-color-disabled-checked":ke,"--n-font-size":_e,"--n-label-padding":xe}}),F=c?Fe("checkbox",O(()=>D.value[0]),Q,e):void 0;return Object.assign(h,se,{rtlEnabled:ne,selfRef:a,mergedClsPrefix:i,mergedDisabled:k,renderedChecked:s,mergedTheme:T,labelId:Pe(),handleClick:o,handleKeyUp:d,handleKeyDown:H,cssVars:c?void 0:Q,themeClass:F==null?void 0:F.themeClass,onRender:F==null?void 0:F.onRender})},render(){var e;const{$slots:l,renderedChecked:a,mergedDisabled:i,indeterminate:c,privateInsideTable:m,cssVars:r,labelId:p,label:x,mergedClsPrefix:s,focusable:h,handleKeyUp:k,handleKeyDown:D,handleClick:T}=this;(e=this.onRender)===null||e===void 0||e.call(this);const u=Ue(l.default,o=>x||o?w("span",{class:`${s}-checkbox__label`,id:p},x||o):null);return w("div",{ref:"selfRef",class:[`${s}-checkbox`,this.themeClass,this.rtlEnabled&&`${s}-checkbox--rtl`,a&&`${s}-checkbox--checked`,i&&`${s}-checkbox--disabled`,c&&`${s}-checkbox--indeterminate`,m&&`${s}-checkbox--inside-table`,u&&`${s}-checkbox--show-label`],tabindex:i||!h?void 0:0,role:"checkbox","aria-checked":c?"mixed":a,"aria-labelledby":p,style:r,onKeyup:k,onKeydown:D,onClick:T,onMousedown:()=>{Le("selectstart",window,o=>{o.preventDefault()},{once:!0})}},w("div",{class:`${s}-checkbox-box-wrapper`}," ",w("div",{class:`${s}-checkbox-box`},w(Ne,null,{default:()=>this.indeterminate?w("div",{key:"indeterminate",class:`${s}-checkbox-icon`},Ze()):w("div",{key:"check",class:`${s}-checkbox-icon`},Ye())}),w("div",{class:`${s}-checkbox-box__border`}))),u)}}),at=S({__name:"Button",props:{circle:{type:Boolean,default:!1},quaternary:{type:Boolean,default:!1},type:{type:String,default:"default"},size:{type:String,default:"medium"},disabled:{type:Boolean,default:!1}},setup(e){return(l,a)=>(b(),q(J(Ee),{circle:e.circle,quaternary:e.quaternary,type:e.type,size:e.size,disabled:e.disabled},{default:V(()=>[L(l.$slots,"default",{},void 0,!0)]),_:3},8,["circle","quaternary","type","size","disabled"]))}}),G=z(at,[["__scopeId","data-v-39e4a762"]]),ot={class:"map-placeholder"},lt={class:"map-placeholder-content"},st=S({__name:"Map",props:{height:{type:Number,default:300}},setup(e){return(l,a)=>(b(),v("div",{class:"map-container",style:Ke({height:e.height+"px"})},[t("div",ot,[t("div",lt,[L(l.$slots,"placeholder-content",{},()=>[a[0]||(a[0]=t("p",null,"Map goes here",-1)),a[1]||(a[1]=t("p",{class:"map-note"},"This is a placeholder for an actual map integration",-1))],!0)])]),L(l.$slots,"default",{},void 0,!0)],4))}}),nt=z(st,[["__scopeId","data-v-1ee605f1"]]),dt={key:0,class:"card-header"},rt={class:"title"},it={key:0,class:"price-container"},ct={class:"price-value"},ut={class:"card-content"},bt=S({__name:"Card",props:{withHeader:{type:Boolean,default:!1},title:{type:String,default:""},price:{type:[String,Number],default:""}},setup(e){return(l,a)=>(b(),v("div",{class:W(["card",{"with-header":e.withHeader}])},[e.withHeader?(b(),v("div",dt,[t("div",rt,_(e.title),1),e.price?(b(),v("div",it,[a[0]||(a[0]=t("span",{class:"price-label"},"Price",-1)),t("span",ct,_(e.price),1)])):A("",!0)])):A("",!0),t("div",ut,[L(l.$slots,"default",{},void 0,!0)])],2))}}),le=z(bt,[["__scopeId","data-v-323badbd"]]),ht={class:"request-details"},ft={class:"details-grid"},mt={class:"detail-group"},pt={class:"detail-value"},vt={class:"detail-group"},kt={class:"detail-value"},gt={class:"detail-group full-width"},yt={class:"detail-value"},xt={class:"detail-group full-width"},Ct={class:"detail-value"},wt=S({__name:"RequestDetails",props:{request:{}},setup(e){return(l,a)=>(b(),v("div",ht,[g(le,{"with-header":!0,title:"Request #"+l.request.id,price:l.request.price+"$"},{default:V(()=>[t("div",ft,[t("div",mt,[a[0]||(a[0]=t("span",{class:"detail-label"},"Type",-1)),t("span",pt,_(l.request.type),1)]),t("div",vt,[a[1]||(a[1]=t("span",{class:"detail-label"},"Time",-1)),t("span",kt,_(l.request.time),1)]),t("div",gt,[a[2]||(a[2]=t("span",{class:"detail-label"},"Loading address",-1)),t("span",yt,_(l.request.loadingAddress),1)]),t("div",xt,[a[3]||(a[3]=t("span",{class:"detail-label"},"Unloading address",-1)),t("span",Ct,_(l.request.unloadingAddress),1)])])]),_:1},8,["title","price"])]))}}),_t=z(wt,[["__scopeId","data-v-7a0c4abf"]]),$t={class:"unloading-point-form"},St={class:"form-group"},Bt={key:0,class:"confirmation-message"},Vt={class:"form-actions"},zt={class:"button-group"},Rt=S({__name:"AddUnloadingPoint",props:{modelValue:{type:Boolean,default:!1},title:{type:String,default:"Add unloading point"},addressLabel:{type:String,default:"Address"},confirmationMessage:{type:String,default:"Are you sure that you want to change number of movers?"},showConfirmation:{type:Boolean,default:!1}},emits:["update:modelValue","cancel","add"],setup(e,{emit:l}){const a=e,i=l,c=y(a.modelValue),m=y(""),r=()=>{c.value=!1,m.value="",i("update:modelValue",!1),i("cancel")},p=()=>{m.value.trim()&&(i("add",{address:m.value}),m.value="",c.value=!1,i("update:modelValue",!1))};return(x,s)=>(b(),q(Oe,{modelValue:c.value,"onUpdate:modelValue":s[1]||(s[1]=h=>c.value=h),title:e.title},{default:V(()=>[t("div",$t,[t("div",St,[t("label",null,_(e.addressLabel),1),g(oe,{modelValue:m.value,"onUpdate:modelValue":s[0]||(s[0]=h=>m.value=h),placeholder:"Address"},null,8,["modelValue"])]),e.showConfirmation?(b(),v("div",Bt,_(e.confirmationMessage),1)):A("",!0),t("div",Vt,[L(x.$slots,"footer",{},()=>[t("div",zt,[g(G,{onClick:r},{default:V(()=>s[2]||(s[2]=[N("Cancel")])),_:1}),g(ee,{"small-button":"true",onClick:p},{default:V(()=>s[3]||(s[3]=[N("Add")])),_:1})])],!0)])])]),_:3},8,["modelValue","title"]))}}),At=z(Rt,[["__scopeId","data-v-24084928"]]),Dt={class:"request-actions-bar"},Tt={class:"action-buttons-container"},Mt=S({__name:"RequestActionsBar",props:{showAddPoint:{type:Boolean,default:!0},showContact:{type:Boolean,default:!0},showTrack:{type:Boolean,default:!1}},emits:["addPoint","contact","track"],setup(e){return(l,a)=>(b(),v("div",Dt,[t("div",Tt,[L(l.$slots,"default",{},()=>[e.showAddPoint?(b(),q(ee,{key:0,"small-button":"true",onClick:a[0]||(a[0]=i=>l.$emit("addPoint")),class:"action-button"},{default:V(()=>a[3]||(a[3]=[N(" Add Unloading Point ")])),_:1})):A("",!0),e.showContact?(b(),q(G,{key:1,type:"primary",onClick:a[1]||(a[1]=i=>l.$emit("contact")),class:"action-button"},{default:V(()=>a[4]||(a[4]=[N(" Contact Customer ")])),_:1})):A("",!0),e.showTrack?(b(),q(G,{key:2,onClick:a[2]||(a[2]=i=>l.$emit("track")),class:"action-button"},{default:V(()=>a[5]||(a[5]=[N(" Track Order ")])),_:1})):A("",!0)],!0)])]))}}),It=z(Mt,[["__scopeId","data-v-637965c9"]]),qt={class:"checkbox-wrapper"},Ft=S({__name:"Checkbox",props:{modelValue:{type:Boolean,default:!1},label:{type:String,default:""},disabled:{type:Boolean,default:!1}},emits:["update:modelValue","change"],setup(e,{emit:l}){const a=e,i=l,c=y(a.modelValue);j(()=>a.modelValue,r=>{c.value=r}),j(c,r=>{i("update:modelValue",r)});const m=r=>{i("change",r)};return(r,p)=>(b(),v("div",qt,[g(J(tt),{checked:c.value,"onUpdate:checked":[p[0]||(p[0]=x=>c.value=x),m],disabled:e.disabled},{default:V(()=>[t("span",{class:W({"checked-label":c.value})},_(e.label),3)]),_:1},8,["checked","disabled"])]))}}),Pt=z(Ft,[["__scopeId","data-v-bb650501"]]),Ut={class:"filter-bar"},Nt={class:"search-container"},Lt={class:"filters-container"},Ht={class:"filter-group"},Et={class:"sort-control"},Kt=S({__name:"RequestFilterBar",props:{initialFilters:{type:Array,default:()=>[]}},emits:["filter","search","sort"],setup(e,{emit:l}){const a=e,i=l,c=y(""),m=y("date-new"),r=y(a.initialFilters.length>0?a.initialFilters:[{label:"1 Bedroom",value:"1-bedroom",selected:!1},{label:"2 Bedroom",value:"2-bedroom",selected:!1},{label:"3+ Bedroom",value:"3-bedroom",selected:!1},{label:"Urgent",value:"urgent",selected:!1}]),p=()=>{i("search",c.value)},x=()=>{i("filter",r.value.filter(s=>s.selected).map(s=>s.value)),i("sort",m.value)};return j(()=>a.initialFilters,s=>{s.length>0&&(r.value=s)},{deep:!0}),(s,h)=>(b(),v("div",Ut,[t("div",Nt,[g(oe,{modelValue:c.value,"onUpdate:modelValue":h[0]||(h[0]=k=>c.value=k),placeholder:"Search requests...",onInput:p,clearable:""},null,8,["modelValue"])]),t("div",Lt,[t("div",Ht,[(b(!0),v(te,null,ae(r.value,(k,D)=>(b(),q(Pt,{key:D,modelValue:k.selected,"onUpdate:modelValue":T=>k.selected=T,label:k.label,onChange:x},null,8,["modelValue","onUpdate:modelValue","label"]))),128))]),t("div",Et,[h[3]||(h[3]=t("label",{class:"sort-label"},"Sort by:",-1)),je(t("select",{"onUpdate:modelValue":h[1]||(h[1]=k=>m.value=k),class:"sort-select",onChange:x},h[2]||(h[2]=[t("option",{value:"date-new"},"Newest first",-1),t("option",{value:"date-old"},"Oldest first",-1),t("option",{value:"price-high"},"Price (high to low)",-1),t("option",{value:"price-low"},"Price (low to high)",-1)]),544),[[Ge,m.value]])])])]))}}),Ot=z(Kt,[["__scopeId","data-v-7edccbf5"]]),jt={class:"badge-text"},Gt=S({__name:"StatusBadge",props:{status:{type:String,required:!0,validator:e=>["active","completed","pending","cancelled","urgent"].includes(e.toLowerCase())},label:{type:String,default:""},size:{type:String,default:"default",validator:e=>["small","default"].includes(e)}},setup(e){return(l,a)=>(b(),v("div",{class:W(["status-badge",[e.status.toLowerCase(),{small:e.size==="small"}]])},[t("span",jt,_(e.label||e.status),1)],2))}}),Y=z(Gt,[["__scopeId","data-v-0d5a75db"]]),Wt={class:"company-view"},Qt={class:"p-5 w-full pb-24"},Xt={class:"request-summary mb-6"},Yt={class:"grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-4"},Zt=["onClick"],Jt={class:"details-grid"},ea={class:"detail-group"},ta={class:"detail-value"},aa={class:"detail-group"},oa={class:"detail-value"},la={key:0,class:"detail-group status-wrapper"},sa={class:"map-section mb-6"},na={key:0,class:"request-details-section mb-6"},da={class:"section-title mb-4"},ra=S({__name:"CompanyView",setup(e){const l=y([{id:1,price:12,type:"3 bedroom",time:"12/11/24 | 12:00",loadingAddress:"123 Main St, City A",unloadingAddress:"456 Elm St, City B",status:"active"},{id:2,price:15,type:"2 bedroom",time:"12/12/24 | 14:00",loadingAddress:"789 Oak St, City C",unloadingAddress:"321 Pine St, City D",status:"pending"},{id:3,price:18,type:"1 bedroom",time:"12/13/24 | 10:30",loadingAddress:"456 Maple St, City E",unloadingAddress:"654 Birch St, City F",status:"urgent"},{id:4,price:20,type:"4 bedroom",time:"12/14/24 | 09:00",loadingAddress:"101 Walnut St, City G",unloadingAddress:"202 Cedar St, City H",status:"completed"}]),a=y(""),i=y([]),c=y("date-new"),m=O(()=>{let u=[...l.value];if(a.value){const o=a.value.toLowerCase();u=u.filter(d=>{var H;return d.loadingAddress.toLowerCase().includes(o)||d.unloadingAddress.toLowerCase().includes(o)||d.type.toLowerCase().includes(o)||((H=d.status)==null?void 0:H.toLowerCase().includes(o))||`request #${d.id}`.toLowerCase().includes(o)})}return i.value.length>0&&(u=u.filter(o=>i.value.some(d=>{if(d==="1-bedroom")return o.type.includes("1 bedroom");if(d==="2-bedroom")return o.type.includes("2 bedroom");if(d!=="3-bedroom")return o.type.includes("3 bedroom")||o.type.includes("4 bedroom")||parseInt(o.type)>=3,d==="urgent"?o.status==="urgent":!1}))),u.sort((o,d)=>{switch(c.value){case"date-new":return new Date(d.time).getTime()-new Date(o.time).getTime();case"date-old":return new Date(o.time).getTime()-new Date(d.time).getTime();case"price-high":return Number(d.price)-Number(o.price);case"price-low":return Number(o.price)-Number(d.price);default:return 0}}),u}),r=y(null),p=y(!1),x=u=>{r.value=u},s=()=>{r.value&&console.log("Contact customer for request:",r.value.id)},h=u=>{r.value&&console.log("Adding unloading point to request:",r.value.id,u)},k=u=>{a.value=u},D=u=>{i.value=u},T=u=>{c.value=u};return(u,o)=>(b(),v("div",Wt,[g(We,{title:"Company Dashboard"}),t("main",Qt,[t("section",Xt,[o[6]||(o[6]=t("h2",{class:"section-title mb-4"},"Current Requests",-1)),g(Ot,{onSearch:k,onFilter:D,onSort:T}),t("div",Yt,[(b(!0),v(te,null,ae(m.value,d=>(b(),v("div",{key:d.id,class:"cursor-pointer hover:shadow-md transition-shadow",onClick:H=>x(d)},[g(le,{"with-header":!0,title:"Request #"+d.id,price:d.price+"$"},{default:V(()=>[t("div",Jt,[t("div",ea,[o[3]||(o[3]=t("span",{class:"detail-label"},"Type",-1)),t("span",ta,_(d.type),1)]),t("div",aa,[o[4]||(o[4]=t("span",{class:"detail-label"},"Time",-1)),t("span",oa,_(d.time),1)]),d.status?(b(),v("div",la,[o[5]||(o[5]=t("span",{class:"detail-label"},"Status",-1)),g(Y,{status:d.status},null,8,["status"])])):A("",!0)])]),_:2},1032,["title","price"])],8,Zt))),128))])]),t("section",sa,[o[7]||(o[7]=t("h2",{class:"section-title mb-4"},"Request Locations",-1)),g(nt,{height:300})]),r.value?(b(),v("section",na,[t("h2",da,[o[8]||(o[8]=N(" Request Details ")),r.value.status?(b(),q(Y,{key:0,status:r.value.status,class:"ml-2"},null,8,["status"])):A("",!0)]),g(_t,{request:r.value},null,8,["request"]),g(It,{onAddPoint:o[0]||(o[0]=d=>p.value=!0),onContact:s})])):A("",!0)]),g(At,{modelValue:p.value,"onUpdate:modelValue":o[1]||(o[1]=d=>p.value=d),onAdd:h,onCancel:o[2]||(o[2]=d=>p.value=!1)},null,8,["modelValue"]),g(Qe)]))}}),ua=z(ra,[["__scopeId","data-v-d92682b8"]]);export{ua as default};
