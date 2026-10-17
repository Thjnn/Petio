<script>
  // Bảng nhập nhiều dòng: "Thêm" nhân bản dòng cuối với khóa mới và ô trống, "Gỡ" xóa dòng (giữ lại ít nhất 1).
  document.addEventListener('click', (e) => {
    const add = e.target.closest('[data-add-row]');
    if (add) {
      const body = document.getElementById(add.dataset.addRow);
      const row = body.lastElementChild.cloneNode(true);
      const key = Date.now();
      row.querySelectorAll('input').forEach((input) => {
        input.name = input.name.replace(/\[[^\]]+\]/, `[${key}]`);
        if (input.type === 'radio') input.value = key;
        if (input.type === 'checkbox') input.checked = true;
        else if (input.type === 'radio') input.checked = false;
        else input.value = '';
      });
      body.appendChild(row);
      row.querySelector('input:not([type=hidden]):not([type=radio])').focus();
    }

    const remove = e.target.closest('[data-remove-row]');
    if (remove) {
      const body = remove.closest('tbody');
      if (body.children.length > 1) remove.closest('tr').remove();
    }
  });
</script>
