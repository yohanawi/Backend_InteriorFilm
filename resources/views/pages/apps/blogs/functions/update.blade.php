<x-default-layout>

    @section('title')
        Blog Update
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('apps.blogs.update') }}
    @endsection

    <div class="card">
        <form action="{{ route('blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data" class="p-10">
            @csrf
            @method('PUT')

            <div class="mb-6 row">
                <div class="col-md-8">
                    <!-- Featured Image -->
                    <div class="mb-5">
                        <label class="form-label required">Featured Image</label>
                        <div class="pt-0 text-center card-body">
                            <style>
                                .image-input-featured-placeholder {
                                    background-image: url('{{ asset('assets/media/svg/files/blank-image.svg') }}');
                                }

                                [data-bs-theme="dark"] .image-input-featured-placeholder {
                                    background-image: url('{{ asset('assets/media/svg/files/blank-image-dark.svg') }}');
                                }
                            </style>
                            <div class="mb-3 image-input image-input-outline image-input-featured-placeholder {{ $blog->featured_image ? '' : 'image-input-empty' }}"
                                data-kt-image-input="true"
                                @if ($blog->featured_image) style="background-image: url('{{ asset('storage/' . $blog->featured_image) }}')" @endif>
                                <div class="image-input-wrapper w-300px h-200px"
                                    @if ($blog->featured_image) style="background-image: url('{{ asset('storage/' . $blog->featured_image) }}')" @endif>
                                </div>
                                <label
                                    class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                    data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                    title="Change featured image">
                                    <i class="ki-outline ki-pencil fs-7"></i>
                                    <input type="file" name="featured_image"
                                        accept=".png, .jpg, .jpeg, .webp, .gif" />
                                    <input type="hidden" name="featured_image_remove" />
                                </label>
                                <span
                                    class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                    data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                    title="Cancel featured image">
                                    <i class="ki-outline ki-cross fs-2"></i>
                                </span>
                                <span
                                    class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                    data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                    title="Remove featured image">
                                    <i class="ki-outline ki-cross fs-2"></i>
                                </span>
                            </div>
                            <div class="text-muted fs-7">Set the blog featured image. Only *.png, *.jpg, *.jpeg,
                                *.webp, and *.gif image files are accepted.</div>
                        </div>
                    </div>
                    <!-- Blog Title -->
                    <div class="mb-5">
                        <label class="form-label required">Blog Title</label>
                        <input type="text" name="title" class="form-control form-control-solid"
                            placeholder="Enter blog title" value="{{ old('title', $blog->title) }}" required>
                    </div>

                    <!-- Slug -->
                    <div class="mb-5">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control form-control-solid"
                            placeholder="Auto-generated or enter custom slug" value="{{ old('slug', $blog->slug) }}">
                    </div>

                    <!-- Excerpt -->
                    <div class="mb-5">
                        <label class="form-label">Excerpt</label>
                        <textarea name="excerpt" class="form-control form-control-solid" rows="3" placeholder="Short summary of the blog">{{ old('excerpt', $blog->excerpt) }}</textarea>
                    </div>

                    <!-- Tags -->
                    <div class="mb-5">
                        <label class="form-label">Tags</label>
                        <div id="tags-container" class="mb-2"></div>
                        <input type="text" id="tags-input" class="form-control form-control-solid"
                            placeholder="Type tag and press Enter or comma">
                        <input type="hidden" name="tags" id="tags-hidden" value="{{ old('tags', $blog->tags) }}">
                        <div class="form-text">Press Enter or type comma to add tags</div>
                    </div>

                    <div class="mb-5 row">
                        <!-- Publish Date -->
                        <div class="col-md-6">
                            <label class="form-label required">Publish Date</label>
                            <input type="date" name="publish_date" class="form-control form-control-solid"
                                value="{{ old('publish_date', $blog->publish_date->format('Y-m-d')) }}" required>
                        </div>
                        <!-- Category (optional) -->
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="catalog_id" class="form-control form-control-solid" data-control="select2">
                                <option value="">Select category</option>
                                @foreach ($catalogs as $catalog)
                                    <option value="{{ $catalog->id }}"
                                        {{ old('catalog_id', $blog->catalog_id) == $catalog->id ? 'selected' : '' }}>
                                        {{ $catalog->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Blog Content -->
                    <div class="mb-5">
                        <label class="form-label required">Blog Content</label>
                        <textarea name="content" id="editor" class="form-control form-control-solid" rows="10">{{ old('content', $blog->content) }}</textarea>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Meta Title -->
                    <div class="mb-5">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control form-control-solid"
                            placeholder="Meta title for SEO" value="{{ old('meta_title', $blog->meta_title) }}">
                    </div>

                    <!-- Meta Description -->
                    <div class="mb-5">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control form-control-solid" rows="3"
                            placeholder="Meta description for SEO">{{ old('meta_description', $blog->meta_description) }}</textarea>
                    </div>

                    <!-- Keywords -->
                    <div class="mb-5">
                        <label class="form-label">Keywords</label>
                        <input type="text" name="keywords" class="form-control form-control-solid"
                            placeholder="Comma separated keywords" value="{{ old('keywords', $blog->keywords) }}">
                    </div>
                    <!-- Divider -->
                    <hr class="my-6">
                    <!-- Author Name -->
                    <div class="mb-5">
                        <label class="form-label required">Author Name</label>
                        <input type="text" name="author_name" class="form-control form-control-solid"
                            placeholder="Enter author name" value="{{ old('author_name', $blog->author_name) }}"
                            required>
                    </div>

                    <!-- Author Position -->
                    <div class="mb-5">
                        <label class="form-label">Author Position</label>
                        <input type="text" name="author_position" class="form-control form-control-solid"
                            placeholder="Author's position"
                            value="{{ old('author_position', $blog->author_position) }}">
                    </div>

                    <!-- Author Profile Image -->
                    <div class="mb-5">
                        <label class="form-label">Author Profile Image</label>
                        <div class="pt-0 text-center card-body">
                            <style>
                                .image-input-placeholder {
                                    background-image: url('{{ asset('assets/media/svg/files/blank-image.svg') }}');
                                }

                                [data-bs-theme="dark"] .image-input-placeholder {
                                    background-image: url('{{ asset('assets/media/svg/files/blank-image-dark.svg') }}');
                                }
                            </style>
                            <div class="mb-3 image-input image-input-outline image-input-placeholder {{ $blog->author_profile_image ? '' : 'image-input-empty' }}"
                                data-kt-image-input="true">
                                <div class="image-input-wrapper w-150px h-150px"
                                    @if ($blog->author_profile_image) style="background-image: url('{{ asset('storage/' . $blog->author_profile_image) }}')" @endif>
                                </div>
                                <label
                                    class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                    data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                    title="Change avatar">
                                    <i class="ki-outline ki-pencil fs-7"></i>
                                    <input type="file" name="author_profile_image"
                                        accept=".png, .jpg, .jpeg, .webp, .gif" />
                                    <input type="hidden" name="author_profile_image_remove" />
                                </label>
                                <span
                                    class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                    data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                    title="Cancel avatar">
                                    <i class="ki-outline ki-cross fs-2"></i>
                                </span>
                                <span
                                    class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                    data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                    title="Remove avatar">
                                    <i class="ki-outline ki-cross fs-2"></i>
                                </span>
                            </div>
                            <div class="text-muted fs-7">Set the author profile image. Only *.png, *.jpg, *.jpeg,
                                *.webp, and *.gif image files are accepted.</div>
                        </div>
                    </div>

                    <!-- Status Dropdown -->
                    <div class="mb-10">
                        <label class="form-label required">Status</label>
                        <select name="status" class="form-control form-control-solid" data-control="select2"
                            required>
                            <option value="" disabled>Select status</option>
                            <option value="draft" {{ old('status', $blog->status) == 'draft' ? 'selected' : '' }}>
                                Draft</option>
                            <option value="published"
                                {{ old('status', $blog->status) == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived"
                                {{ old('status', $blog->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>

                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="allow_comments" value="1"
                            id="allowComments" @checked(old('allow_comments', $blog->allow_comments)) />
                        <label class="form-check-label" for="allowComments">Allow Comments</label>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('blogs.index') }}" class="btn btn-secondary ms-2">
                    <i class="ki-duotone ki-cross fs-2"></i> Cancel
                </a>
                <button type="reset" class="btn btn-light ms-2">
                    <i class="ki-duotone ki-refresh fs-2"></i> Reset
                </button>
                <button type="submit" class="btn btn-primary ms-2">
                    <i class="ki-duotone ki-check fs-2"></i> Update Blog
                </button>
            </div>
        </form>

        @push('scripts')
            <script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                // Initialize CKEditor
                let blogEditor;
                ClassicEditor
                    .create(document.querySelector('#editor'), {
                        toolbar: {
                            items: [
                                'heading', '|',
                                'bold', 'italic', 'underline', 'strikethrough', '|',
                                'link', 'bulletedList', 'numberedList', '|',
                                'blockQuote', 'insertTable', 'mediaEmbed', '|',
                                'undo', 'redo'
                            ]
                        },
                        table: {
                            contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
                        }
                    })
                    .then(editor => {
                        blogEditor = editor;
                    })
                    .catch(error => {
                        console.error(error);
                    });

                // Ensure CKEditor content is submitted (otherwise HTML5 required can block submit silently)
                function isEditorEmpty(html) {
                    const text = html
                        .replace(/<[^>]*>/g, ' ')
                        .replace(/&nbsp;/g, ' ')
                        .trim();
                    return text.length === 0;
                }

                const blogForm = document.querySelector('form[action^="{{ url('blogs') }}"]');
                if (blogForm) {
                    blogForm.addEventListener('submit', function(e) {
                        if (!blogEditor) {
                            return;
                        }

                        const html = blogEditor.getData() || '';
                        document.querySelector('#editor').value = html;

                        if (isEditorEmpty(html)) {
                            e.preventDefault();
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error!',
                                text: 'Blog Content is required.'
                            }).then(() => {
                                blogEditor.editing.view.focus();
                            });
                        }
                    });
                }

                // Auto-generate slug and meta title from title
                const titleInput = document.querySelector('input[name="title"]');
                const slugInput = document.querySelector('input[name="slug"]');
                const metaTitleInput = document.querySelector('input[name="meta_title"]');

                titleInput.addEventListener('input', function() {
                    const title = this.value;

                    // Auto-generate slug
                    const slug = title
                        .toLowerCase()
                        .trim()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/[\s_-]+/g, '-')
                        .replace(/^-+|-+$/g, '');
                    slugInput.value = slug;

                    // Auto-generate meta title if empty
                    if (!metaTitleInput.value) {
                        metaTitleInput.value = title;
                    }
                });

                // Professional Tags Input System
                let tags = [];
                const tagsInput = document.getElementById('tags-input');
                const tagsContainer = document.getElementById('tags-container');
                const tagsHidden = document.getElementById('tags-hidden');

                // Initialize existing tags
                const existingTags = tagsHidden.value;
                if (existingTags) {
                    tags = existingTags.split(',').map(tag => tag.trim()).filter(tag => tag);
                    updateTagsDisplay();
                }

                function updateTagsDisplay() {
                    tagsContainer.innerHTML = '';
                    tags.forEach((tag, index) => {
                        const tagElement = document.createElement('span');
                        tagElement.className = 'badge badge-light-primary me-2 mb-2';
                        tagElement.innerHTML = `
                            ${tag}
                            <i class="ki-outline ki-cross fs-7 ms-1" style="cursor: pointer;" onclick="removeTag(${index})"></i>
                        `;
                        tagsContainer.appendChild(tagElement);
                    });
                    tagsHidden.value = tags.join(',');
                }

                function addTag(tag) {
                    tag = tag.trim();
                    if (tag && !tags.includes(tag)) {
                        tags.push(tag);
                        updateTagsDisplay();
                        tagsInput.value = '';
                    }
                }

                function removeTag(index) {
                    tags.splice(index, 1);
                    updateTagsDisplay();
                }

                tagsInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ',') {
                        e.preventDefault();
                        const tag = this.value.replace(',', '').trim();
                        if (tag) {
                            addTag(tag);
                        }
                    }
                });

                tagsInput.addEventListener('blur', function() {
                    const tag = this.value.trim();
                    if (tag) {
                        addTag(tag);
                    }
                });

                // Make removeTag available globally
                window.removeTag = removeTag;

                // Show success message if available
                @if (session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: '{{ session('success') }}',
                        timer: 3000,
                        showConfirmButton: false
                    });
                @endif

                // Show error messages if available
                @if ($errors->any())
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error!',
                        html: '<ul style="text-align: left;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                    });
                @endif
            </script>
        @endpush
    </div>
</x-default-layout>
