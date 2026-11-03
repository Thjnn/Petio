document.addEventListener('DOMContentLoaded',()=>{
 document.querySelectorAll('[data-image-fallback]').forEach(img=>{const fallback=()=>{img.src=img.dataset.imageFallback;};img.addEventListener('error',fallback,{once:true});if(img.complete&&!img.naturalWidth)fallback();});
 document.querySelectorAll('.pc-cancel form').forEach(form=>{const reason=form.querySelector('[name=reason]'),detail=form.querySelector('[name=detail]');const sync=()=>{detail.required=reason.value==='Khác';};reason.addEventListener('change',sync);sync();});
});
