<x-default-layout>
    @section('title')
        SEO Settings - {{ $page->title }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('pages.seo-edit', $page) }}
    @endsection

    <div class="d-flex flex-column flex-lg-row">
        {{-- Main Content --}}
        <div class="order-2 mb-10 flex-lg-row-fluid me-lg-4 order-lg-1 mb-lg-0">
            <div class="mb-2 card">
                <div class="pb-0 card-body">
                    {{-- Back Button --}}
                    <div class="flex-wrap mb-2 d-flex">
                        <a href="{{ route('pages.index') }}" class="btn btn-sm btn-icon btn-active-color-primary me-3">
                            <i class="ki-duotone ki-arrow-left fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </a>
                        <div class="flex-grow-1">
                            <div class="flex-wrap d-flex justify-content-between align-items-start">
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center">
                                        <span class="text-gray-900 fs-2 fw-bold me-1">{{ $page->title }}</span>
                                        <span class="badge badge-light-success fs-8 fw-semibold ms-2">Active</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tabs Navigation --}}
                    <ul class="border-transparent nav nav-stretch nav-line-tabs nav-line-tabs-2x fs-5 fw-bold">
                        <li class="mt-2 nav-item">
                            <a class="py-5 nav-link text-active-primary ms-0 me-10 active" data-bs-toggle="tab"
                                href="#kt_tab_basic">
                                Basic Meta
                            </a>
                        </li>
                        <li class="mt-2 nav-item">
                            <a class="py-5 nav-link text-active-primary me-10" data-bs-toggle="tab"
                                href="#kt_tab_social">
                                Social Media
                            </a>
                        </li>
                        <li class="mt-2 nav-item">
                            <a class="py-5 nav-link text-active-primary me-10" data-bs-toggle="tab"
                                href="#kt_tab_structured">
                                Structured Data
                            </a>
                        </li>
                    </ul>
                </div>

                <form action="{{ route('pages.seo-update', $page) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <!-- Page Info -->
                        <div class="mb-10 alert alert-info d-flex align-items-center">
                            {!! getIcon('information', 'fs-2x text-info me-4') !!}
                            <div>
                                <div class="fw-bold fs-5">Page: {{ $page->title }}</div>
                                <div class="text-gray-700">Slug: <code>{{ $page->slug }}</code></div>
                            </div>
                        </div>
                        <div class="tab-content" id="kt_seo_tab_content">
                            <div class="tab-pane fade show active" id="kt_tab_basic" role="tabpanel">
                                <!-- Basic Meta Tags -->
                                <div class="mb-10">
                                    <h3 class="mb-5 fw-bold">
                                        <i class="fas fa-tags"></i>
                                        Basic Meta Tags
                                    </h3>

                                    <div class="row">
                                        <div class="col-md-6 mb-7">
                                            <label class="form-label">Meta Title</label>
                                            <input type="text" id="meta_title_input"
                                                class="form-control @error('meta_title') is-invalid @enderror"
                                                name="meta_title" value="{{ old('meta_title', $page->meta_title) }}"
                                                placeholder="Leave empty to use page title" />
                                            <div class="form-text">Recommended length: 50-60 characters. Current: <span
                                                    id="metaTitleCount">{{ strlen($page->meta_title ?? '') }}</span>
                                            </div>
                                            @error('meta_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-7">
                                            <label class="form-label">Canonical URL</label>
                                            <input type="url"
                                                class="form-control @error('canonical_url') is-invalid @enderror"
                                                name="canonical_url"
                                                value="{{ old('canonical_url', $page->canonical_url) }}"
                                                placeholder="https://example.com/page-url" />
                                            <div class="form-text">The preferred version of the page URL</div>
                                            @error('canonical_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mb-7">
                                        <label class="form-label">Meta Description</label>
                                        <textarea class="form-control  @error('meta_description') is-invalid @enderror" name="meta_description" rows="3"
                                            id="meta_description_input" placeholder="Brief description for search engines">{{ old('meta_description', $page->meta_description) }}</textarea>
                                        <div class="form-text">Recommended length: 150-160 characters. Current: <span
                                                id="metaDescCount">{{ strlen($page->meta_description ?? '') }}</span>
                                        </div>
                                        @error('meta_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-7">
                                        <label class="form-label">Meta Keywords</label>
                                        <input type="text"
                                            class="form-control @error('meta_keywords') is-invalid @enderror"
                                            name="meta_keywords"
                                            value="{{ old('meta_keywords', $page->meta_keywords) }}"
                                            placeholder="keyword1, keyword2, keyword3" />
                                        <div class="form-text">Separate keywords with commas</div>
                                        @error('meta_keywords')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Social Media Tab --}}
                            <div class="tab-pane fade" id="kt_tab_social" role="tabpanel">
                                <!-- Open Graph Tags -->
                                <div class="mb-10">
                                    <h3 class="mb-5 fw-bold">
                                        <i class="bi bi-facebook fs-2 me-2"></i>
                                        Open Graph (Facebook) Tags
                                    </h3>

                                    <div class="row">
                                        <div class="col-md-6 mb-7">
                                            <label class="form-label">OG Title</label>
                                            <input type="text" id="og_title_input"
                                                class="form-control @error('og_title') is-invalid @enderror"
                                                name="og_title" value="{{ old('og_title', $page->og_title) }}"
                                                placeholder="Title for social media sharing" />
                                            <div class="form-text">Leave empty to use meta title or page title</div>
                                            @error('og_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-7">
                                            <label class="form-label">OG Type</label>
                                            <select class="form-select @error('og_type') is-invalid @enderror"
                                                name="og_type" data-control="select2"
                                                data-placeholder="Select OG Type" data-hide-search="true">
                                                <option value="website"
                                                    {{ old('og_type', $page->og_type) == 'website' ? 'selected' : '' }}>
                                                    Website
                                                </option>
                                                <option value="article"
                                                    {{ old('og_type', $page->og_type) == 'article' ? 'selected' : '' }}>
                                                    Article
                                                </option>
                                                <option value="product"
                                                    {{ old('og_type', $page->og_type) == 'product' ? 'selected' : '' }}>
                                                    Product
                                                </option>
                                                <option value="blog"
                                                    {{ old('og_type', $page->og_type) == 'blog' ? 'selected' : '' }}>
                                                    Blog</option>
                                            </select>
                                            @error('og_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mb-7">
                                        <label class="form-label">OG Description</label>
                                        <textarea class="form-control @error('og_description') is-invalid @enderror" name="og_description" rows="3"
                                            id="og_description_input" placeholder="Description for social media sharing">{{ old('og_description', $page->og_description) }}</textarea>
                                        @error('og_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-7">
                                        <label class="form-label">OG Image URL</label>
                                        <input type="url" id="og_image_input"
                                            class="form-control @error('og_image') is-invalid @enderror"
                                            name="og_image" value="{{ old('og_image', $page->og_image) }}"
                                            placeholder="https://example.com/image.jpg" />
                                        <div class="form-text">Recommended size: 1200x630 pixels</div>
                                        @error('og_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Twitter Card Tags -->
                                <div class="mb-10">
                                    <h3 class="mb-5 fw-bold">
                                        <i class="bi bi-twitter fs-2 me-2"></i>
                                        Twitter Card Tags
                                    </h3>

                                    <div class="row">
                                        <div class="col-md-8 mb-7">
                                            <label class="form-label">Twitter Title</label>
                                            <input type="text"
                                                class="form-control @error('twitter_title') is-invalid @enderror"
                                                name="twitter_title"
                                                value="{{ old('twitter_title', $page->twitter_title) }}"
                                                placeholder="Title for Twitter cards" />
                                            <div class="form-text">Leave empty to use OG title</div>
                                            @error('twitter_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-7">
                                            <label class="form-label">Twitter Card Type</label>
                                            <select class="form-select @error('twitter_card') is-invalid @enderror"
                                                name="twitter_card" data-control="select2"
                                                data-placeholder="Select Twitter Card Type" data-hide-search="true">
                                                <option value="summary"
                                                    {{ old('twitter_card', $page->twitter_card) == 'summary' ? 'selected' : '' }}>
                                                    Summary
                                                </option>
                                                <option value="summary_large_image"
                                                    {{ old('twitter_card', $page->twitter_card) == 'summary_large_image' ? 'selected' : '' }}>
                                                    Summary Large Image</option>
                                                <option value="app"
                                                    {{ old('twitter_card', $page->twitter_card) == 'app' ? 'selected' : '' }}>
                                                    App
                                                </option>
                                                <option value="player"
                                                    {{ old('twitter_card', $page->twitter_card) == 'player' ? 'selected' : '' }}>
                                                    Player
                                                </option>
                                            </select>
                                            @error('twitter_card')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mb-7">
                                        <label class="form-label">Twitter Description</label>
                                        <textarea class="form-control @error('twitter_description') is-invalid @enderror" name="twitter_description"
                                            rows="3" placeholder="Description for Twitter cards">{{ old('twitter_description', $page->twitter_description) }}</textarea>
                                        @error('twitter_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-7">
                                        <label class="form-label">Twitter Image URL</label>
                                        <input type="url"
                                            class="form-control @error('twitter_image') is-invalid @enderror"
                                            name="twitter_image"
                                            value="{{ old('twitter_image', $page->twitter_image) }}"
                                            placeholder="https://example.com/twitter-image.jpg" />
                                        <div class="form-text">Recommended size: 1200x628 pixels</div>
                                        @error('twitter_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Structured Data Tab --}}
                            <div class="tab-pane fade" id="kt_tab_structured" role="tabpanel">
                                <div class="mb-6 card card-flush">
                                    <div class="card-header pt-7">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="text-gray-800 card-label fw-bold">JSON-LD Schema Markup</span>
                                            <span class="mt-1 text-gray-500 fw-semibold fs-6">Add structured data for
                                                rich
                                                search results</span>
                                        </h3>
                                    </div>
                                    <div class="pt-6 card-body">
                                        <div class="mb-2">
                                            <label class="form-label">JSON-LD Schema
                                                <i class="ki-duotone ki-information-5 fs-7 ms-1"
                                                    data-bs-toggle="tooltip" title="Must be valid JSON format">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                            </label>
                                            <textarea class="form-control @error('structured_data') is-invalid @enderror" name="structured_data" rows="8"
                                                id="structured_data_input"
                                                placeholder='{"@context": "https://schema.org", "@type": "WebPage", "name": "Page Name"}'>{{ old('structured_data', is_array($page->structured_data) ? json_encode($page->structured_data, JSON_PRETTY_PRINT) : $page->structured_data) }}</textarea>
                                            <div class="form-text">Enter valid JSON-LD schema markup. Leave empty if
                                                not
                                                needed.
                                            </div>
                                            @error('structured_data')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="gap-3 mb-10 d-flex justify-content-end me-4">
                        <button type="reset" class="btn btn-light btn-active-light-primary">
                            <i class="ki-duotone ki-arrows-circle fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Reset Changes
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ki-duotone ki-check fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Save SEO Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="order-1 flex-column flex-lg-row-auto w-lg-250px w-xl-300px order-lg-2 mb-lg-0">
            {{-- SEO Score Card --}}
            <div class="mb-6 card card-flush">
                <div class="card-header pt-7">
                    <h3 class="card-title">
                        <span class="text-gray-800 card-label fw-bold">SEO Health</span>
                    </h3>
                </div>
                <div class="pt-5 card-body">
                    <div class="mb-5 d-flex flex-center">
                        <div class="text-center">
                            <div class="mb-2 text-gray-800 fs-2hx fw-bold">
                                <span id="seo_score">0</span>%
                            </div>
                            <span class="text-gray-500 fs-6 fw-semibold">Overall Score</span>
                        </div>
                    </div>
                    <div class="mb-5 separator separator-dashed"></div>
                    <div class="d-flex flex-column">
                        <div class="mb-5 d-flex align-items-center" id="seo_meta_title">
                            <div class="bullet me-3 seo-bullet" style="width:8px;height:8px;border-radius:50%;"></div>
                            <div class="flex-grow-1">
                                <span class="text-gray-800 fw-semibold fs-7">Meta Title</span>
                            </div>
                            <i class="ki-duotone seo-icon fs-2" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </div>
                        <div class="mb-5 d-flex align-items-center" id="seo_meta_description">
                            <div class="bullet me-3 seo-bullet" style="width:8px;height:8px;border-radius:50%;"></div>
                            <div class="flex-grow-1">
                                <span class="text-gray-800 fw-semibold fs-7">Meta Description</span>
                            </div>
                            <i class="ki-duotone seo-icon fs-2" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </div>
                        <div class="mb-5 d-flex align-items-center" id="seo_og">
                            <div class="bullet me-3 seo-bullet" style="width:8px;height:8px;border-radius:50%;"></div>
                            <div class="flex-grow-1">
                                <span class="text-gray-800 fw-semibold fs-7">OG Tags</span>
                            </div>
                            <i class="ki-duotone seo-icon fs-2" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </div>
                        <div class="d-flex align-items-center" id="seo_structured">
                            <div class="bullet me-3 seo-bullet" style="width:8px;height:8px;border-radius:50%;"></div>
                            <div class="flex-grow-1">
                                <span class="text-gray-800 fw-semibold fs-7">Structured Data</span>
                            </div>
                            <i class="ki-duotone seo-icon fs-2" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6 card card-flush">
                <div class="card-header pt-7">
                    <h3 class="card-title">
                        <span class="text-gray-800 card-label fw-bold">
                            AI SEO Suggestions
                        </span>
                    </h3>
                </div>

                <div class="pt-4 card-body">
                    <ul id="seo_ai_suggestions" class="mb-0 ps-0 list-unstyled">
                        <li class="text-gray-500 fs-7">Start typing to get SEO suggestions…</li>
                    </ul>
                </div>
            </div>

            {{-- Quick Tips Card --}}
            <div class="mb-6 card card-flush">
                <div class="card-header pt-7">
                    <h3 class="card-title">
                        <span class="text-gray-800 card-label fw-bold">SEO Tips</span>
                    </h3>
                </div>
                <div class="pt-5 card-body">
                    <div class="d-flex align-items-start mb-7">
                        <span class="bullet bullet-vertical h-40px bg-primary me-5"></span>
                        <div class="flex-grow-1">
                            <span class="mb-1 text-gray-800 fw-semibold fs-7 d-block">Meta Title Length</span>
                            <span class="text-gray-500 fw-semibold fs-8">Keep between 50-60 characters for best
                                display</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-7">
                        <span class="bullet bullet-vertical h-40px bg-success me-5"></span>
                        <div class="flex-grow-1">
                            <span class="mb-1 text-gray-800 fw-semibold fs-7 d-block">Meta Description</span>
                            <span class="text-gray-500 fw-semibold fs-8">Aim for 150-160 characters for optimal
                                results</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-start">
                        <span class="bullet bullet-vertical h-40px bg-warning me-5"></span>
                        <div class="flex-grow-1">
                            <span class="mb-1 text-gray-800 fw-semibold fs-7 d-block">Social Images</span>
                            <span class="text-gray-500 fw-semibold fs-8">Use 1200x630px for best social media
                                display</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Character counter for meta title
                $('input[name="meta_title"]').on('keyup', function() {
                    $('#metaTitleCount').text($(this).val().length);
                });

                // Character counter for meta description
                $('textarea[name="meta_description"]').on('keyup', function() {
                    $('#metaDescCount').text($(this).val().length);
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                // Meta Title Counter
                const metaTitleInput = document.getElementById('meta_title_input');
                const metaTitleCount = document.getElementById('metaTitleCount');

                if (metaTitleInput && metaTitleCount) {
                    metaTitleInput.addEventListener('input', function() {
                        metaTitleCount.textContent = this.value.length;
                        updateCountColor(metaTitleCount, this.value.length, 50, 60);
                    });
                }

                // Meta Description Counter
                const metaDescInput = document.getElementById('meta_description_input');
                const metaDescCount = document.getElementById('metaDescCount');

                if (metaDescInput && metaDescCount) {
                    metaDescInput.addEventListener('input', function() {
                        metaDescCount.textContent = this.value.length;
                        updateCountColor(metaDescCount, this.value.length, 150, 160);
                    });
                }

                function updateCountColor(element, length, min, max) {
                    element.classList.remove('text-danger', 'text-warning', 'text-success', 'text-primary');

                    if (length === 0) {
                        element.classList.add('text-primary');
                    } else if (length < min) {
                        element.classList.add('text-warning');
                    } else if (length >= min && length <= max) {
                        element.classList.add('text-success');
                    } else {
                        element.classList.add('text-danger');
                    }
                }

                // Initialize tooltips
                const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                tooltips.forEach(tooltip => {
                    new bootstrap.Tooltip(tooltip);
                });
            });

            document.addEventListener('DOMContentLoaded', function() {

                const seoScoreEl = document.getElementById('seo_score');

                const inputs = [
                    'meta_title_input',
                    'meta_description_input',
                    'og_title_input',
                    'og_description_input',
                    'og_image_input',
                    'structured_data_input'
                ];

                inputs.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.addEventListener('input', calculateSeoScore);
                });

                calculateSeoScore(); // initial load

                function calculateSeoScore() {
                    let score = 0;

                    score += metaTitleScore();
                    score += metaDescriptionScore();
                    score += ogScore();
                    score += structuredDataScore();

                    seoScoreEl.textContent = score;
                }

                function metaTitleScore() {
                    const val = document.getElementById('meta_title_input').value.trim();
                    if (!val) return 0;
                    if (val.length >= 50 && val.length <= 60) return 25;
                    return 15;
                }

                function metaDescriptionScore() {
                    const val = document.getElementById('meta_description_input').value.trim();
                    if (!val) return 0;
                    if (val.length >= 150 && val.length <= 160) return 25;
                    return 15;
                }

                function ogScore() {
                    const title = document.getElementById('og_title_input').value.trim();
                    const desc = document.getElementById('og_description_input').value.trim();
                    const img = document.getElementById('og_image_input').value.trim();

                    if (title && desc && img) return 25;
                    if (title || desc || img) return 15;
                    return 0;
                }

                function structuredDataScore() {
                    const val = document.getElementById('structured_data_input').value.trim();
                    if (!val) return 0;

                    try {
                        JSON.parse(val);
                        return 25;
                    } catch {
                        return 10;
                    }
                }

            });

            function updateSeoRow(rowId, status) {
                const row = document.getElementById(rowId);
                if (!row) return;

                const bullet = row.querySelector('.seo-bullet');
                const icon = row.querySelector('.seo-icon');

                bullet.className = 'bullet me-3 seo-bullet';
                icon.className = 'ki-duotone seo-icon fs-2';

                if (status === 'success') {
                    bullet.classList.add('bg-success');
                    icon.classList.add('ki-check-circle', 'text-success');
                } else if (status === 'warning') {
                    bullet.classList.add('bg-warning');
                    icon.classList.add('ki-information-5', 'text-warning');
                } else {
                    bullet.classList.add('bg-danger');
                    icon.classList.add('ki-cross-circle', 'text-danger');
                }
            }

            function refreshSeoIndicators() {
                // Meta Title
                const metaTitle = document.getElementById('meta_title_input').value.trim();
                if (!metaTitle) {
                    updateSeoRow('seo_meta_title', 'danger');
                } else if (metaTitle.length >= 50 && metaTitle.length <= 60) {
                    updateSeoRow('seo_meta_title', 'success');
                } else {
                    updateSeoRow('seo_meta_title', 'warning');
                }

                // Meta Description
                const metaDesc = document.getElementById('meta_description_input').value.trim();
                if (!metaDesc) {
                    updateSeoRow('seo_meta_description', 'danger');
                } else if (metaDesc.length >= 150 && metaDesc.length <= 160) {
                    updateSeoRow('seo_meta_description', 'success');
                } else {
                    updateSeoRow('seo_meta_description', 'warning');
                }

                // OG Tags
                const ogTitle = document.getElementById('og_title_input').value.trim();
                const ogDesc = document.getElementById('og_description_input').value.trim();
                const ogImg = document.getElementById('og_image_input').value.trim();

                if (ogTitle && ogDesc && ogImg) {
                    updateSeoRow('seo_og', 'success');
                } else if (ogTitle || ogDesc || ogImg) {
                    updateSeoRow('seo_og', 'warning');
                } else {
                    updateSeoRow('seo_og', 'danger');
                }

                // Structured Data
                const structured = document.getElementById('structured_data_input').value.trim();
                if (!structured) {
                    updateSeoRow('seo_structured', 'danger');
                } else {
                    try {
                        JSON.parse(structured);
                        updateSeoRow('seo_structured', 'success');
                    } catch {
                        updateSeoRow('seo_structured', 'warning');
                    }
                }
            }

            // Hook into existing SEO score calculation
            document.addEventListener('DOMContentLoaded', () => {
                refreshSeoIndicators();

                document.querySelectorAll(
                    '#meta_title_input, #meta_description_input, #og_title_input, #og_description_input, #og_image_input, #structured_data_input'
                ).forEach(el => {
                    el.addEventListener('input', refreshSeoIndicators);
                });

                document.querySelectorAll(
                    '#meta_title_input, #meta_description_input, #og_title_input, #og_description_input, #og_image_input, #structured_data_input'
                ).forEach(el => {
                    el.addEventListener('input', generateAiSeoSuggestions);
                });

                document.addEventListener('DOMContentLoaded', generateAiSeoSuggestions);
            });

            function updateSeoRow(rowId, status, message) {
                const row = document.getElementById(rowId);
                if (!row) return;

                const bullet = row.querySelector('.seo-bullet');
                const icon = row.querySelector('.seo-icon');

                bullet.className = 'bullet me-3 seo-bullet';
                icon.className = 'ki-duotone seo-icon fs-2';

                // Tooltip message
                icon.setAttribute('title', message || '');
                bootstrap.Tooltip.getOrCreateInstance(icon).setContent({
                    '.tooltip-inner': message || ''
                });

                if (status === 'success') {
                    bullet.classList.add('bg-success');
                    icon.classList.add('ki-check-circle', 'text-success');
                } else if (status === 'warning') {
                    bullet.classList.add('bg-warning');
                    icon.classList.add('ki-information-5', 'text-warning');
                } else {
                    bullet.classList.add('bg-danger');
                    icon.classList.add('ki-cross-circle', 'text-danger');
                }
            }

            function refreshSeoIndicators() {

                /* ================= META TITLE ================= */
                const metaTitle = document.getElementById('meta_title_input').value.trim();

                if (!metaTitle) {
                    updateSeoRow(
                        'seo_meta_title',
                        'danger',
                        'Meta title is missing. Add a title between 50–60 characters.'
                    );
                } else if (metaTitle.length < 50 || metaTitle.length > 60) {
                    updateSeoRow(
                        'seo_meta_title',
                        'warning',
                        `Meta title length is ${metaTitle.length}. Recommended: 50–60 characters.`
                    );
                } else {
                    updateSeoRow(
                        'seo_meta_title',
                        'success',
                        'Meta title length is optimal.'
                    );
                }

                /* ================= META DESCRIPTION ================= */
                const metaDesc = document.getElementById('meta_description_input').value.trim();

                if (!metaDesc) {
                    updateSeoRow(
                        'seo_meta_description',
                        'danger',
                        'Meta description is missing. Add 150–160 characters.'
                    );
                } else if (metaDesc.length < 150 || metaDesc.length > 160) {
                    updateSeoRow(
                        'seo_meta_description',
                        'warning',
                        `Meta description length is ${metaDesc.length}. Recommended: 150–160 characters.`
                    );
                } else {
                    updateSeoRow(
                        'seo_meta_description',
                        'success',
                        'Meta description length is optimal.'
                    );
                }

                /* ================= OG TAGS ================= */
                const ogTitle = document.getElementById('og_title_input').value.trim();
                const ogDesc = document.getElementById('og_description_input').value.trim();
                const ogImg = document.getElementById('og_image_input').value.trim();

                if (ogTitle && ogDesc && ogImg) {
                    updateSeoRow(
                        'seo_og',
                        'success',
                        'All Open Graph tags are set.'
                    );
                } else if (ogTitle || ogDesc || ogImg) {
                    updateSeoRow(
                        'seo_og',
                        'warning',
                        'Some Open Graph fields are missing. Add title, description, and image.'
                    );
                } else {
                    updateSeoRow(
                        'seo_og',
                        'danger',
                        'Open Graph tags are missing. Social sharing may look bad.'
                    );
                }

                /* ================= STRUCTURED DATA ================= */
                const structured = document.getElementById('structured_data_input').value.trim();

                if (!structured) {
                    updateSeoRow(
                        'seo_structured',
                        'danger',
                        'No structured data found. Add JSON-LD for rich results.'
                    );
                } else {
                    try {
                        JSON.parse(structured);
                        updateSeoRow(
                            'seo_structured',
                            'success',
                            'Valid JSON-LD schema detected.'
                        );
                    } catch {
                        updateSeoRow(
                            'seo_structured',
                            'warning',
                            'Structured data is not valid JSON. Fix syntax errors.'
                        );
                    }
                }
            }

            function generateAiSeoSuggestions() {
                const suggestions = [];

                const pageTitle = document.querySelector('span.fs-2.fw-bold')?.innerText || '';
                const metaTitle = document.getElementById('meta_title_input').value.trim();
                const metaDesc = document.getElementById('meta_description_input').value.trim();
                const ogImg = document.getElementById('og_image_input').value.trim();
                const structured = document.getElementById('structured_data_input').value.trim();

                const primaryKeyword = pageTitle.split(' ')[0];

                // META TITLE
                if (!metaTitle) {
                    suggestions.push({
                        text: 'Add a meta title to improve search visibility.',
                        target: 'meta_title_input'
                    });
                } else if (metaTitle.length < 50 || metaTitle.length > 60) {
                    suggestions.push({
                        text: `Meta title length is ${metaTitle.length}. Aim for 50–60 characters.`,
                        target: 'meta_title_input'
                    });
                }

                // META DESCRIPTION
                if (!metaDesc) {
                    suggestions.push({
                        text: 'Add a meta description to increase click-through rate.',
                        target: 'meta_description_input'
                    });
                } else if (metaDesc.length < 150 || metaDesc.length > 160) {
                    suggestions.push({
                        text: `Meta description length is ${metaDesc.length}. Aim for 150–160 characters.`,
                        target: 'meta_description_input'
                    });
                }

                // OG IMAGE
                if (!ogImg) {
                    suggestions.push({
                        text: 'Add an Open Graph image for better social sharing.',
                        target: 'og_image_input'
                    });
                }

                // STRUCTURED DATA
                if (!structured) {
                    suggestions.push({
                        text: 'Add JSON-LD structured data to enable rich results.',
                        target: 'structured_data_input'
                    });
                } else {
                    try {
                        JSON.parse(structured);
                    } catch {
                        suggestions.push({
                            text: 'Fix JSON syntax errors in structured data.',
                            target: 'structured_data_input'
                        });
                    }
                }

                renderSeoSuggestions(suggestions);
            }

            function renderSeoSuggestions(items) {
                const list = document.getElementById('seo_ai_suggestions');
                list.innerHTML = '';

                if (items.length === 0) {
                    list.innerHTML = `
            <li class="text-success fs-7">
                🎉 Your page is well optimized!
            </li>`;
                    return;
                }

                items.forEach(item => {
                    const li = document.createElement('li');
                    li.className = 'd-flex align-items-start mb-3';

                    li.innerHTML = `
                            <div class="text-gray-700 flex-grow-1 fs-7">
                                ${item.text}
                            </div> 
                        `;

                    list.appendChild(li);
                });
            }

            function fixSeoIssue(targetId) {
                const input = document.getElementById(targetId);
                if (!input) return;

                input.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                setTimeout(() => {
                    input.focus();
                }, 300);
            }
        </script>
    @endpush

</x-default-layout>
