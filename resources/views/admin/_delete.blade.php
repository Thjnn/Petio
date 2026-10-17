<form action="{{ $action }}" method="post" onsubmit="return confirm('{{ $confirm ?? 'Xóa mục này? Không thể hoàn tác.' }}')">
  @csrf
  @method('DELETE')
  <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
</form>
