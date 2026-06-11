import{q as l,r as x}from"./index.4b887428.js";import"./translations.e71e2202.js";import{h as s}from"./utils.671ae102.js";import{a as m}from"./icon.5e141fec.js";import{_ as p}from"./default-i18n.65d58dd6.js";import"./runtime-core.esm-bundler.ce5add0b.js";import"./helpers.633a054c.js";const{addFilter:h}=window.wp.hooks,{BlockControls:b}=window.wp.blockEditor,{Button:u,ToolbarGroup:$,ToolbarButton:k}=window.wp.components,{Fragment:I,render:w,unmountComponentAtNode:_}=window.wp.element,{createHigherOrderComponent:B}=window.wp.compose,{select:d,useSelect:A}=window.wp.data,S="all-in-one-seo-pack",g={generateWithAI:p("Generate with AI",S),editWithAI:p("Edit with AI",S)};let y=!1;const f=(a,o={})=>{window.aioseoBus.$emit("do-post-settings-main-tab-change",{name:"aiContent"}),a.classList.add("is-busy"),a.disabled=!0;const e=x(),t=l();setTimeout(()=>{t.initiator=o==null?void 0:o.initiator,(!t.initiator||!t.initiator.slug)&&t.resetInitiator(),e.isModalOpened="image-generator",a.classList.remove("is-busy"),a.disabled=!1},500)},N=()=>{l().extend.imageBlockToolbar&&(y||(h("editor.BlockEdit","aioseo/extend-image-block-toolbar",B(o=>e=>{if(e.name!=="core/image"||!e.attributes.url)return s`<${o} ...${e} />`;const t=A(n=>n("core").getEntityRecord("postType","attachment",e.attributes.id)||null,[`media-${e.attributes.id}`]);return s`
				<${I}>
					<${b}>
						<${$}>
							<${k}
								icon=${m}
								iconSize=${24}
								label=${g.editWithAI}
								onClick=${n=>{f(n.currentTarget,{initiator:{slug:"image-block-toolbar",wpMedia:t}})}}
								style=${{maxHeight:"90%",alignSelf:"center",padding:"0"}}
							/>
						</${$}>
					</${b}>

					<${o} ...${e} />
				</${I}>`},"extendImageBlockToolbar")),y=!0))},z=()=>{var i,c;if(!l().extend.imageBlockPlaceholder)return;const o=d("core/block-editor").getSelectedBlock();if(!o||o.name!=="core/image"||(i=o.attributes)!=null&&i.url)return;const e=document.getElementById(`block-${o.clientId}`),t=e==null?void 0:e.querySelector(".components-form-file-upload");if(!t||e!=null&&e.querySelector(".aioseo-ai-image-generator-btn"))return;const n=document.createElement("div");w(s`
			<${u}
				className=${"aioseo-ai-image-generator-btn"}
				variant=${"secondary"}
				icon=${m}
				iconSize=${"20"}
				__next40pxDefaultSize=${!0}
			>
				${g.generateWithAI}
			</${u}>`,n);const r=(c=n.firstChild)==null?void 0:c.cloneNode(!0);r&&(t.after(r),r.addEventListener("click",()=>{f(r,{initiator:{slug:"image-block-placeholder"}})})),_(n),n.remove()},W=()=>{var e;if(!l().extend.featuredImageButton||d("core/edit-post").getActiveGeneralSidebarName()!=="edit-post/document")return;if(d("core/editor").getEditedPostAttribute("featured_media")){(e=document.querySelector(".aioseo-ai-image-generator-btn-featured-image"))==null||e.remove();return}setTimeout(()=>{var c;const t=document.querySelector(".editor-post-featured-image__container"),n=t==null?void 0:t.querySelector("button");if(!n||t!=null&&t.querySelector(".aioseo-ai-image-generator-btn-featured-image"))return;t.style.display="flex",t.style.gap="8px";const r=document.createElement("div");w(s`
				<${u}
					className=${"aioseo-ai-image-generator-btn-featured-image"}
					variant=${"secondary"}
					icon=${m}
					iconSize=${"20"}
					__next40pxDefaultSize=${!0}
					title=${g.generateWithAI}
				/>`,r);const i=(c=r.firstChild)==null?void 0:c.cloneNode(!0);i&&(n.after(i),i.addEventListener("click",()=>{f(i,{initiator:{slug:"featured-image-btn"}})})),_(r),r.remove()})};export{W as extendFeaturedImageButton,z as extendImageBlockPlaceholder,N as extendImageBlockToolbar};
