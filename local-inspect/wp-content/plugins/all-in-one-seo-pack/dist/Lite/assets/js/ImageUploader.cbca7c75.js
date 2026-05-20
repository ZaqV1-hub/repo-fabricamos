import{X as A,a as N}from"./index.4b887428.js";import"./translations.e71e2202.js";import{_ as l}from"./default-i18n.65d58dd6.js";import{n as M,a6 as g,o as k,c as W,_ as L,l as _,a2 as h,a1 as O,a4 as R,a7 as H,$ as G,ae as j}from"./runtime-core.esm-bundler.ce5add0b.js";import{_ as D}from"./Button.9a301412.js";import{B as J}from"./Img.85371eb9.js";import{B as q}from"./Input.f04bee4d.js";import{S as Y}from"./Plus.c9b03af8.js";import{_ as F}from"./Trash.87a7d946.js";import{u as X,a as I}from"./runtime-dom.esm-bundler.dc49ee3e.js";import{_ as K}from"./_plugin-vue_export-helper.eefbdd86.js";const b="all-in-one-seo-pack",V=()=>{var e,a,o;return typeof((e=window.wp)==null?void 0:e.media)=="function"?window.wp.media:typeof((o=(a=window.parent)==null?void 0:a.wp)==null?void 0:o.media)=="function"?window.parent.wp.media:null},Q=()=>{let e=null;const a=({title:i,buttonText:n,multiple:s=!1,type:r=null,onSelect:c})=>{e=V()({title:i,button:{text:n},multiple:s,library:r?{type:r}:{}}),e.on("select",()=>{const u=e.state().get("selection"),d=u.first();d&&c(s?u.toJSON():d.toJSON())}),e.on("close",()=>e.detach()),M(()=>{e.open()})},o=({multiple:i=!1,type:n=null,onSelect:s})=>{var x,C;const u=(((C=(x=N().aioseo)==null?void 0:x.urls)==null?void 0:C.home)||window.location.origin).replace(/\/$/,"")+"/wp-admin/",d=new URLSearchParams({breakdance_wpuiforbuilder_media:"1"});n&&d.set("types",n),i&&d.set("multiple","1");const t=document.createElement("div");if(t.className="aioseo-media-uploader-overlay",t.innerHTML=`
			<div class="aioseo-media-uploader-modal">
				<iframe src="${u}?${d.toString()}"></iframe>
			</div>
		`,!document.getElementById("aioseo-media-uploader-styles")){const m=document.createElement("style");m.id="aioseo-media-uploader-styles",m.textContent=`
				.aioseo-media-uploader-overlay {
					position: fixed;
					inset: 0;
					background: rgba(0, 0, 0, 0.7);
					z-index: 999999;
					display: flex;
					align-items: center;
					justify-content: center;
				}
				.aioseo-media-uploader-modal {
					background: #fff;
					width: 90%;
					height: 90%;
					max-width: 1200px;
					max-height: 800px;
					border-radius: 4px;
					overflow: hidden;
					display: flex;
					align-items: center;
					justify-content: center;
				}
				.aioseo-media-uploader-modal::before {
					content: '';
					width: 40px;
					height: 40px;
					border: 3px solid #e5e5e5;
					border-top-color: #005AE0;
					border-radius: 50%;
					animation: aioseoSpin 0.8s linear infinite;
					position: absolute;
				}
				.aioseo-media-uploader-modal iframe {
					width: 100%;
					height: 100%;
					border: none;
					background: #fff;
					opacity: 0;
					transition: opacity 0.15s ease-out;
				}
				.aioseo-media-uploader-modal.ready::before {
					display: none;
				}
				.aioseo-media-uploader-modal.ready iframe {
					opacity: 1;
				}
				@keyframes aioseoSpin {
					to { transform: rotate(360deg); }
				}
			`,document.head.appendChild(m)}document.body.appendChild(t);const E=t.querySelector("iframe"),P=t.querySelector(".aioseo-media-uploader-modal");E.addEventListener("load",()=>{setTimeout(()=>P.classList.add("ready"),300)});const y=()=>{document.removeEventListener("breakdanceMediaChooserSelect",w),document.removeEventListener("breakdanceMediaChooserClose",S),t.remove()},S=()=>M(y),w=m=>{const p=m.detail,T=i?Array.isArray(p)?p:[p]:Array.isArray(p)?p[0]:p;s(T),y()};t.addEventListener("click",m=>m.target===t&&y()),document.addEventListener("breakdanceMediaChooserSelect",w),document.addEventListener("breakdanceMediaChooserClose",S)};return{openMediaLibrary:({title:i=l("Choose Media",b),buttonText:n=l("Choose",b),multiple:s=!1,type:r=null,onSelect:c})=>{if(V()){a({title:i,buttonText:n,multiple:s,type:r,onSelect:c});return}if(A()){o({multiple:s,type:r,onSelect:c});return}window.alert(l("The media uploader is not available. Please paste the image URL directly.",b))}}},f="all-in-one-seo-pack",v={emits:["update:modelValue"],setup(){const{openMediaLibrary:e}=Q();return{openMediaLibrary:e}},components:{BaseButton:D,BaseImg:J,BaseInput:q,SvgCirclePlus:Y,SvgTrash:F},props:{baseSize:{type:String,default:"medium"},imgPreviewMaxHeight:{type:String,default:"525px"},imgPreviewMaxWidth:{type:String,default:"525px"},description:String,modelValue:{type:String,default:""},useDebounce:{type:Boolean,default:!0}},data(){return{strings:{description:l("Minimum size: 112px x 112px, The image must be in JPG, PNG, GIF, SVG, or WEBP format.",f),pasteYourImageUrl:l("Paste your image URL or select a new image",f),remove:l("Remove",f),uploadOrSelectImage:l("Upload or Select Image",f)}}},computed:{iconWidth(){return this.baseSize==="small"?"16":"20"}},methods:{setImgSrc(e){this.$emit("update:modelValue",e)},openUploadModal(){this.openMediaLibrary({title:l("Choose Image",f),buttonText:l("Choose Image",f),type:"image",onSelect:e=>this.setImgSrc((e==null?void 0:e.url)||null)})}}},z=()=>{X(e=>({"6d8f8f79":e.imgPreviewMaxHeight,bd75c598:e.imgPreviewMaxWidth}))},U=v.setup;v.setup=U?(e,a)=>(z(),U(e,a)):z;const Z={class:"image-upload"},$=["innerHTML"];function ee(e,a,o,B,i,n){const s=g("svg-trash"),r=g("base-button"),c=g("base-input"),u=g("svg-circle-plus"),d=g("base-img");return k(),W("div",{class:j(["aioseo-image-uploader",{"aioseo-image-uploader--has-image":!!o.modelValue}])},[L("div",Z,[_(c,{size:o.baseSize,modelValue:o.modelValue,placeholder:i.strings.pasteYourImageUrl,onChange:a[1]||(a[1]=t=>n.setImgSrc(t))},{"append-icon":h(()=>[o.modelValue?(k(),O(r,{key:0,size:o.baseSize,class:"remove-image",type:"gray",onClick:a[0]||(a[0]=I(t=>n.setImgSrc(null),["prevent"]))},{default:h(()=>[_(s,{width:n.iconWidth},null,8,["width"])]),_:1},8,["size"])):R("",!0)]),_:1},8,["size","modelValue","placeholder"]),_(r,{size:o.baseSize,class:"insert-image",type:"black",onClick:a[2]||(a[2]=I(t=>n.openUploadModal(),["prevent"]))},{default:h(()=>[_(u,{width:"14"}),H(" "+G(i.strings.uploadOrSelectImage),1)]),_:1},8,["size"])]),L("div",{class:"aioseo-description",innerHTML:o.description||i.strings.description},null,8,$),_(d,{class:"image-preview",src:o.modelValue,debounce:o.useDebounce},null,8,["src","debounce"])],2)}const ue=K(v,[["render",ee],["__scopeId","data-v-972281dd"]]);export{ue as C};
