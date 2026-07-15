@php
    $isEdit = isset($subcategory) && $subcategory !== null;
@endphp
<div class="mb-3">
    <label for="category_id" class="form-label">Category</label>
    <select name="category_id" id="category_id" class="form-select" required>
        <option value="">Select Category</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" {{ old('category_id', $isEdit ? $subcategory->category_id : '') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label for="name" class="form-label">Subcategory Name</label>
    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $isEdit ? $subcategory->name : '') }}" required>
</div>