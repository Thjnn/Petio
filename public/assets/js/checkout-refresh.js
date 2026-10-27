document.addEventListener('DOMContentLoaded',()=>{
 const form=document.getElementById('petio-checkout');if(!form)return;
 const saved=document.getElementById('saved-address-fields'),fresh=document.getElementById('new-address-fields');
 const toggle=()=>{const mode=form.querySelector('[name=address_mode]:checked')?.value||'new';if(saved){saved.hidden=mode!=='saved';saved.disabled=mode!=='saved';}fresh.hidden=mode==='saved';fresh.disabled=mode==='saved';};
 form.querySelectorAll('[name=address_mode]').forEach(el=>el.addEventListener('change',toggle));toggle();
 const code=document.getElementById('coupon-code'),apply=document.getElementById('apply-coupon'),feedback=document.getElementById('coupon-feedback'),discount=document.getElementById('coupon-discount'),total=document.getElementById('checkout-total');
 const money=n=>new Intl.NumberFormat('vi-VN',{style:'currency',currency:'VND'}).format(n);let revision=0;
 function reset(){revision++;discount.textContent='—';total.textContent=money(Number(total.dataset.base));feedback.textContent='Nhấn Áp dụng để xem tổng sau giảm giá.';}
 code.addEventListener('input',reset);
 async function preview(){const current=++revision;const entered=code.value.trim();if(!entered){reset();feedback.textContent='Hãy nhập mã giảm giá.';return;}apply.disabled=true;feedback.textContent='Đang kiểm tra mã…';
 try{const response=await fetch(apply.dataset.url,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':form.querySelector('[name=_token]').value},body:JSON.stringify({coupon_code:entered})});const data=await response.json();if(current!==revision)return;if(!response.ok)throw new Error(Object.values(data.errors||{}).flat()[0]||'Chưa kiểm tra được mã. Vui lòng thử lại.');discount.textContent='−'+money(data.discount);total.textContent=money(data.total);feedback.textContent='Đã áp dụng '+entered.toUpperCase()+'. Tổng tiền đã được cập nhật.';}catch(error){if(current===revision){discount.textContent='—';total.textContent=money(Number(total.dataset.base));feedback.textContent=error.message;}}finally{apply.disabled=false;}}
 apply.addEventListener('click',preview);document.querySelectorAll('[data-coupon]').forEach(button=>button.addEventListener('click',()=>{code.value=button.dataset.coupon;preview();}));if(code.value.trim())preview();
 form.addEventListener('submit',()=>{const button=form.querySelector('.px-place-order');button.disabled=true;button.textContent='Đang đặt hàng…';});
});
