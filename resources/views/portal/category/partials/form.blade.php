@php
    $isEdit = isset($category) && $category !== null;
@endphp

<div class="mb-3">
    <label for="name" class="form-label">Name</label>
    <input type="text" name="name" id="name" value="{{ old('name', $isEdit ? $category->name : '') }}"
        class="form-control" required>
</div>
