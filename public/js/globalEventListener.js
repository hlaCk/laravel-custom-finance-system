let applyOnResources=["expenses"],_contractors=[],_element_contractor_name="contractor_id",_element_project_name="project",_element_entry_category_name="entry_category",preUpdateContractorElement=()=>getNovaElement(_element_contractor_name,!0,(e=>e.data)).then((e=>e&&e.setAttribute("disabled",!0))),updateContractorElement=async e=>{let t=await getNovaElement(_element_contractor_name,!0,(e=>e.data)),a=await getNovaElement(_element_project_name,!0,(e=>e.data));if(!t||!a)return;let r=t.value,n=a.value||"";t.hasAttribute("_value")?r=t.getAttribute("_value"):t.setAttribute("_value",r),t.setAttribute("disabled",!0);let o=async(e,t)=>{let a={};return Array.from(t.options).forEach(((t,r)=>{!t.value||Number(t.value)in e?(t.style.display="",a[t.value||0]=r):t.style.display="none"})),t.value=Number(t.value)in e?t.value:0,t.selectedIndex=a[t.value||0]||0,t.setAttribute("_options",t.options.length),!0};return _contractors[n]?$promise=o(_contractors[n],t):$promise=Nova.request().get("/api/contractor/data").then((e=>{let{data:a,project_id:r}=e.data;return _contractors={},Array.from(a).forEach((e=>{let t={},a=e.data;Object.keys(a).forEach((e=>t[a[e].value]=a[e].label)),_contractors[e.project_id]=t})),!!_contractors[n]&&o(_contractors[n],t)})),$promise.then((e=>{e&&(t.dispatchEvent(new Event("change")),Nova.$emit(`${_element_contractor_name}-change`,t.value))})).finally((()=>{t.removeAttribute("disabled")}))},_callAfter=(e=10)=>callAfter(e,updateContractorElement,preUpdateContractorElement);onNovaElementChange(_element_project_name,_callAfter(),applyOnResources),onNovaElementChange(_element_entry_category_name,_callAfter(),applyOnResources),onNovaLoaded((async()=>{await waitForNovaElements(100,{[_element_contractor_name]:()=>Nova.$emit(`${_element_entry_category_name}-change`,!1)})}),applyOnResources);
