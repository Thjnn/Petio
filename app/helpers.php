<?php

/** 57000 -> "57.000đ" */
function vnd(int|float|null $amount): string
{
    return number_format((float) $amount, 0, ',', '.').'đ';
}

/**
 * Lưu ảnh admin tải lên vào public/assets/file_anh/uploads, trả về đường dẫn dạng asset().
 * Không chọn ảnh mới thì giữ $current.
 */
function store_upload(?\Illuminate\Http\UploadedFile $file, ?string $current = null): ?string
{
    if (! $file) {
        return $current;
    }

    $name = now()->format('YmdHis').'-'.\Illuminate\Support\Str::random(6).'.'.$file->extension();
    $file->move(public_path('assets/file_anh/uploads'), $name);

    return 'assets/file_anh/uploads/'.$name;
}
