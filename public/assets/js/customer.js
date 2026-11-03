document.addEventListener('click', async function (e) {
 const button=e.target.closest('[data-copy]'); if(!button) return;
 const status=document.getElementById('pc-copy-status');
 try { await navigator.clipboard.writeText(button.dataset.copy); button.textContent='Đã sao chép ✓'; if(status)status.textContent='Đã sao chép mã '+button.dataset.copy; }
 catch(error) { if(status)status.textContent='Hãy chọn và sao chép mã: '+button.dataset.copy; }
});
document.addEventListener('submit',function(e){if(e.target.matches('[data-confirm]')&&!confirm(e.target.dataset.confirm))e.preventDefault();});
// Preview first, then explicitly save the avatar in its own form.
document.addEventListener('DOMContentLoaded', () => {
 const file=document.getElementById('avatar-file'); if(!file)return;
 const preview=document.getElementById('avatar-preview'), current=document.getElementById('avatar-current'), feedback=document.getElementById('avatar-feedback');let objectUrl;
 file.addEventListener('change',()=>{
  if(objectUrl)URL.revokeObjectURL(objectUrl);
  const photo=file.files[0];preview.hidden=true;current.hidden=false;feedback.textContent='';file.setCustomValidity('');
  if(!photo)return;
  if(!['image/jpeg','image/png','image/webp'].includes(photo.type)||photo.size>2*1024*1024){const msg='Chọn ảnh JPG, PNG hoặc WebP, tối đa 2 MB.';feedback.textContent=msg;file.setCustomValidity(msg);return;}
  objectUrl=URL.createObjectURL(photo);preview.src=objectUrl;preview.hidden=false;current.hidden=true;feedback.textContent='Ảnh đã chọn. Nhấn “Lưu ảnh đại diện” để cập nhật.';
 });
});
