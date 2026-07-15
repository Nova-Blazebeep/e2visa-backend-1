<div class="mb-3">
    <label for="blog_category_name" class="form-label">Category name <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="blog_category_name" name="name"
        value="{{ old('name', optional($category)->name) }}" required maxlength="255"
        placeholder="e.g. Immigration tips">
</div>
