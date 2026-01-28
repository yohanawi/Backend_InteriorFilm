<x-default-layout>
    @section('title')
        Edit Content - {{ $page->title }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('pages.content-edit', $page) }}
    @endsection

    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="py-3 app-toolbar py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
                    {{ Breadcrumbs::render('pages.content-edit', $page) }}
                    <div class="gap-2 d-flex align-items-center gap-lg-3">
                        <a href="{{ route('pages.index') }}" class="btn btn-sm btn-secondary">
                            <i class="ki-duotone ki-left fs-3"></i> Back to List
                        </a>
                        <a href="{{ route('pages.show', $page->id) }}" class="btn btn-sm btn-light">
                            <i class="ki-duotone ki-eye fs-3"></i> View Page
                        </a>
                        <button type="button" class="btn btn-sm btn-info" onclick="previewContent()">
                            <i class="ki-duotone ki-monitor-mobile fs-3"></i> Preview
                        </button>
                    </div>
                </div>
            </div>

            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-fluid">
                    <!-- Page Info Card -->
                    <div class="mb-5 card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h2 class="mb-1">{{ $page->title }}</h2>
                                    <div class="text-muted">
                                        <span
                                            class="badge badge-light-{{ $page->status === 'published' ? 'success' : 'warning' }}">
                                            {{ ucfirst($page->status) }}
                                        </span>
                                        <span class="mx-2">•</span>
                                        <span>{{ $page->slug }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Builder Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Content Builder</h3>
                            <div class="card-toolbar">
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#addBlockModal">
                                    <i class="ki-duotone ki-plus fs-2"></i> Add Content Block
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="contentForm" action="{{ route('pages.content-update', $page->id) }}"
                                method="POST">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="content_blocks" id="contentBlocksInput">

                                <div id="contentBlocks" class="content-blocks-container">
                                    @if (empty($page->content_blocks))
                                        <div class="py-10 text-center">
                                            <i class="mb-3 ki-duotone ki-abstract-26 fs-3x text-muted">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <p class="text-muted fs-5">No content blocks yet. Click "Add Content Block"
                                                to
                                                start building your page.</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="my-10 separator"></div>

                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-light"
                                        onclick="window.location='{{ route('pages.index') }}'">
                                        Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="saveContentBtn">
                                        <i class="ki-duotone ki-save fs-2"><span class="path1"></span><span
                                                class="path2"></span></i>
                                        Save Content
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Block Modal -->
    <div class="modal fade" id="addBlockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Select Content Block Type</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row g-5">
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card" onclick="addBlock('paragraph'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="text-center card-body">
                                        <i class="mb-3 ki-duotone ki-text-align-left fs-3x text-primary"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        <h5 class="mb-0">Paragraph</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card" onclick="addBlock('text'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="text-center card-body">
                                        <i class="mb-3 ki-duotone ki-text fs-3x text-info"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        <h5 class="mb-0">Text</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card" onclick="addBlock('heading'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="text-center card-body">
                                        <i class="mb-3 ki-duotone ki-text-bold fs-3x text-success"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        <h5 class="mb-0">Heading</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card" onclick="addBlock('custom'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="text-center card-body">
                                        <i class="mb-3 ki-duotone ki-code fs-3x text-warning"><span
                                                class="path1"></span><span class="path2"></span><span
                                                class="path3"></span><span class="path4"></span></i>
                                        <h5 class="mb-0">Custom (CKEditor)</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card" onclick="addBlock('image'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="text-center card-body">
                                        <i class="mb-3 ki-duotone ki-picture fs-3x text-info"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        <h5 class="mb-0">Image</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card" onclick="addBlock('video'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="text-center card-body">
                                        <i class="mb-3 ki-duotone ki-youtube fs-3x text-danger"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        <h5 class="mb-0">Video</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card" onclick="addBlock('url'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="text-center card-body">
                                        <i class="mb-3 ki-duotone ki-link fs-3x text-primary"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        <h5 class="mb-0">URL</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card" onclick="addBlock('email'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="text-center card-body">
                                        <i class="mb-3 ki-duotone ki-sms fs-3x text-success"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        <h5 class="mb-0">Email</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card" onclick="addBlock('number'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="text-center card-body">
                                        <i class="mb-3 ki-duotone ki-calculator fs-3x text-info"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        <h5 class="mb-0">Number</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card" onclick="addBlock('date'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="text-center card-body">
                                        <i class="mb-3 ki-duotone ki-calendar fs-3x text-danger"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        <h5 class="mb-0">Date</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card" onclick="addBlock('boolean'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="text-center card-body">
                                        <i class="mb-3 ki-duotone ki-toggle-on fs-3x text-warning"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        <h5 class="mb-0">Boolean</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card" onclick="addBlock('list'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="text-center card-body">
                                        <i class="mb-3 ki-duotone ki-row-vertical fs-3x text-primary"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        <h5 class="mb-0">List</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card" onclick="addBlock('select'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="text-center card-body">
                                        <i class="mb-3 ki-duotone ki-row-horizontal fs-3x text-success"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        <h5 class="mb-0">Select</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card" onclick="addBlock('button'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="text-center card-body">
                                        <i class="mb-3 ki-duotone ki-mouse-circle fs-3x text-info"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        <h5 class="mb-0">Button</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card" onclick="addBlock('table'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="text-center card-body">
                                        <i class="mb-3 ki-duotone ki-tablet-text-down fs-3x text-danger"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        <h5 class="mb-0">Table</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card" onclick="addBlock('component'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="text-center card-body">
                                        <i class="mb-3 ki-duotone ki-abstract-14 fs-3x text-warning"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        <h5 class="mb-0">Component Group</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Content Preview</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span
                                class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body" id="previewContent">
                    <!-- Preview will be rendered here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add Child Block Modal -->
    <div class="modal fade" id="addChildBlockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Select Child Block Type</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span
                                class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body">
                    <p class="mb-5 text-muted">Select a content type to add inside the component group</p>
                    <div class="row g-4">
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card"
                                onclick="const parentId = $('#addChildBlockModal').data('parent-id'); addChildBlockType(parentId, 'paragraph'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="py-4 text-center card-body">
                                        <i class="mb-2 ki-duotone ki-text-align-left fs-2x text-primary"><span
                                                class="path1"></span></i>
                                        <h6 class="mb-0">Paragraph</h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card"
                                onclick="const parentId = $('#addChildBlockModal').data('parent-id'); addChildBlockType(parentId, 'text'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="py-4 text-center card-body">
                                        <i class="mb-2 ki-duotone ki-text fs-2x text-info"><span
                                                class="path1"></span></i>
                                        <h6 class="mb-0">Text</h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card"
                                onclick="const parentId = $('#addChildBlockModal').data('parent-id'); addChildBlockType(parentId, 'heading'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="py-4 text-center card-body">
                                        <i class="mb-2 ki-duotone ki-text-bold fs-2x text-success"><span
                                                class="path1"></span></i>
                                        <h6 class="mb-0">Heading</h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card"
                                onclick="const parentId = $('#addChildBlockModal').data('parent-id'); addChildBlockType(parentId, 'image'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="py-4 text-center card-body">
                                        <i class="mb-2 ki-duotone ki-picture fs-2x text-warning"><span
                                                class="path1"></span></i>
                                        <h6 class="mb-0">Image</h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card"
                                onclick="const parentId = $('#addChildBlockModal').data('parent-id'); addChildBlockType(parentId, 'video'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="py-4 text-center card-body">
                                        <i class="mb-2 ki-duotone ki-youtube fs-2x text-danger"><span
                                                class="path1"></span></i>
                                        <h6 class="mb-0">Video</h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card"
                                onclick="const parentId = $('#addChildBlockModal').data('parent-id'); addChildBlockType(parentId, 'button'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="py-4 text-center card-body">
                                        <i class="mb-2 ki-duotone ki-mouse-circle fs-2x text-info"><span
                                                class="path1"></span></i>
                                        <h6 class="mb-0">Button</h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card"
                                onclick="const parentId = $('#addChildBlockModal').data('parent-id'); addChildBlockType(parentId, 'list'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="py-4 text-center card-body">
                                        <i class="mb-2 ki-duotone ki-row-vertical fs-2x text-primary"><span
                                                class="path1"></span></i>
                                        <h6 class="mb-0">List</h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 col-6">
                            <a href="#" class="block-type-card"
                                onclick="const parentId = $('#addChildBlockModal').data('parent-id'); addChildBlockType(parentId, 'table'); return false;">
                                <div class="card card-flush h-100">
                                    <div class="py-4 text-center card-body">
                                        <i class="mb-2 ki-duotone ki-tablet-text-down fs-2x text-success"><span
                                                class="path1"></span></i>
                                        <h6 class="mb-0">Table</h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .content-blocks-container {
            min-height: 200px;
        }

        .content-block {
            background: #f9f9f9;
            border: 1px solid #e4e6ef;
            border-radius: 0.625rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            position: relative;
            transition: all 0.3s ease;
        }

        .content-block:hover {
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        .content-block.sortable-ghost {
            opacity: 0.4;
            background: #e4e6ef;
        }

        .block-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e4e6ef;
        }

        .block-type-badge {
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 0.425rem;
        }

        .block-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .drag-handle {
            cursor: move;
            padding: 0.25rem 0.5rem;
        }

        .block-type-card {
            text-decoration: none;
            display: block;
            transition: transform 0.2s ease;
        }

        .block-type-card:hover {
            transform: translateY(-5px);
        }

        .block-type-card .card {
            border: 2px solid transparent;
            transition: border-color 0.2s ease;
        }

        .block-type-card:hover .card {
            border-color: #009ef7;
        }

        .repeatable-items {
            border-left: 3px solid #009ef7;
            padding-left: 1rem;
            margin-top: 1rem;
        }

        .repeatable-item {
            background: white;
            padding: 1rem;
            border-radius: 0.425rem;
            margin-bottom: 0.75rem;
            border: 1px solid #e4e6ef;
        }

        .table-builder {
            overflow-x: auto;
        }

        .table-builder table {
            min-width: 100%;
        }

        .table-builder input {
            min-width: 100px;
        }

        .component-children {
            background: #f5f8fa;
            border: 2px dashed #e4e6ef;
            border-radius: 0.5rem;
            padding: 1rem;
            min-height: 100px;
        }

        .component-children .child-block {
            background: white;
        }

        .component-children .alert {
            margin: 0;
        }
    </style>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
        <script>
            let blockCounter = 0;
            let contentBlocks = [];

            // Initialize with existing blocks
            @if (!empty($page->content_blocks))
                contentBlocks = @json($page->content_blocks);
            @endif

            $(document).ready(function() {
                // Initialize sortable
                const el = document.getElementById('contentBlocks');
                if (el) {
                    Sortable.create(el, {
                        handle: '.drag-handle',
                        animation: 150,
                        ghostClass: 'sortable-ghost',
                        onEnd: function() {
                            updateBlockOrder();
                        }
                    });
                }

                // Load existing blocks
                if (contentBlocks.length > 0) {
                    contentBlocks.forEach(block => {
                        renderBlock(block);
                    });
                }

                // Form submit
                $('#contentForm').on('submit', function(e) {
                    e.preventDefault();
                    saveContent();
                });

                // Auto-save every 30 seconds
                setInterval(function() {
                    saveToLocalStorage();
                }, 30000);
            });

            function addBlock(type) {
                const block = {
                    id: 'block_' + Date.now() + '_' + (++blockCounter),
                    type: type,
                    label: type.charAt(0).toUpperCase() + type.slice(1),
                    repeatable: false,
                    data: getDefaultData(type),
                    items: []
                };

                contentBlocks.push(block);
                renderBlock(block);

                // Close modal
                $('#addBlockModal').modal('hide');

                toastr.success('Block added successfully');
            }

            function getDefaultData(type) {
                const defaults = {
                    'paragraph': {
                        content: ''
                    },
                    'text': {
                        content: ''
                    },
                    'custom': {
                        content: ''
                    },
                    'heading': {
                        content: '',
                        level: 'h2'
                    },
                    'image': {
                        url: '',
                        alt: '',
                        caption: ''
                    },
                    'video': {
                        url: '',
                        platform: 'youtube'
                    },
                    'url': {
                        url: '',
                        text: ''
                    },
                    'email': {
                        email: ''
                    },
                    'number': {
                        value: 0,
                        min: null,
                        max: null
                    },
                    'date': {
                        date: ''
                    },
                    'boolean': {
                        value: false,
                        label: ''
                    },
                    'list': {
                        items: []
                    },
                    'select': {
                        options: [],
                        value: ''
                    },
                    'button': {
                        text: '',
                        url: '',
                        style: 'primary'
                    },
                    'table': {
                        headers: [],
                        rows: []
                    },
                    'component': {
                        name: '',
                        children: []
                    }
                };
                return defaults[type] || {};
            }

            function renderBlock(block) {
                const $container = $('#contentBlocks');

                // Remove empty state
                $container.find('.text-center.py-10').remove();

                const blockHtml = `
        <div class="content-block" data-block-id="${block.id}">
            <div class="block-header">
                <div class="gap-3 d-flex align-items-center">
                    <span class="drag-handle">
                        <i class="ki-duotone ki-dots-vertical fs-1 text-muted"></i>
                    </span>
                    <span class="block-type-badge badge badge-light-primary">${block.type.toUpperCase()}</span>
                    <input type="text" class="form-control form-control-sm w-200px block-label-input" 
                           value="${escapeHtml(block.label)}" placeholder="Block Label"
                           onchange="updateBlockLabel('${block.id}', this.value)">
                </div>
                <div class="block-actions">
                    <label class="form-check form-check-custom form-check-sm">
                        <input class="form-check-input" type="checkbox" 
                               ${block.repeatable ? 'checked' : ''}
                               onchange="toggleRepeatable('${block.id}')">
                        <span class="form-check-label text-muted">Repeatable</span>
                    </label>
                    <button type="button" class="btn btn-sm btn-icon btn-light-danger" 
                            onclick="removeBlock('${block.id}')">
                        <i class="ki-duotone ki-trash fs-2"></i>
                    </button>
                </div>
            </div>
            <div class="block-content">
                ${renderBlockContent(block)}
            </div>
        </div>
    `;

                $container.append(blockHtml);
            }

            function renderBlockContent(block) {
                const blockId = block.id;
                const data = block.data;

                let html = '';

                switch (block.type) {
                    case 'paragraph':
                        html = `
                <div class="mb-3">
                    <label class="form-label">Paragraph Content</label>
                    <textarea class="form-control" rows="4" 
                              onchange="updateBlockData('${blockId}', 'content', this.value)"
                              placeholder="Enter paragraph content...">${escapeHtml(data.content || '')}</textarea>
                    <div class="form-text">Simple paragraph text (plain text)</div>
                </div>
            `;
                        break;

                    case 'custom':
                        html = `
                <div class="mb-3">
                    <label class="form-label">Custom Content (CKEditor)</label>
                    <div id="ckeditor-${blockId}"></div>
                    <input type="hidden" id="ckeditor-data-${blockId}" value="${escapeHtml(data.content || '')}">
                </div>
            `;
                        // Initialize CKEditor after rendering
                        setTimeout(() => {
                            initCKEditor(blockId, data.content || '');
                        }, 100);
                        break;

                    case 'text':
                        html = `
                <div class="mb-3">
                    <textarea class="form-control" rows="5" 
                              onchange="updateBlockData('${blockId}', 'content', this.value)"
                              placeholder="Enter text content...">${escapeHtml(data.content || '')}</textarea>
                </div>
            `;
                        break;

                    case 'heading':
                        html = `
                <div class="row">
                    <div class="mb-3 col-md-8">
                        <input type="text" class="form-control" value="${escapeHtml(data.content || '')}"
                               onchange="updateBlockData('${blockId}', 'content', this.value)"
                               placeholder="Heading text...">
                    </div>
                    <div class="mb-3 col-md-4">
                        <select class="form-select" onchange="updateBlockData('${blockId}', 'level', this.value)">
                            <option value="h1" ${data.level === 'h1' ? 'selected' : ''}>H1</option>
                            <option value="h2" ${data.level === 'h2' ? 'selected' : ''}>H2</option>
                            <option value="h3" ${data.level === 'h3' ? 'selected' : ''}>H3</option>
                            <option value="h4" ${data.level === 'h4' ? 'selected' : ''}>H4</option>
                            <option value="h5" ${data.level === 'h5' ? 'selected' : ''}>H5</option>
                            <option value="h6" ${data.level === 'h6' ? 'selected' : ''}>H6</option>
                        </select>
                    </div>
                </div>
            `;
                        break;

                    case 'image':
                        html = `
                <div class="mb-3">
                    <label class="form-label">Image URL</label>
                    <input type="url" class="form-control" value="${escapeHtml(data.url || '')}"
                           onchange="updateBlockData('${blockId}', 'url', this.value)"
                           placeholder="https://example.com/image.jpg">
                </div>
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Alt Text</label>
                        <input type="text" class="form-control" value="${escapeHtml(data.alt || '')}"
                               onchange="updateBlockData('${blockId}', 'alt', this.value)"
                               placeholder="Image description">
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Caption</label>
                        <input type="text" class="form-control" value="${escapeHtml(data.caption || '')}"
                               onchange="updateBlockData('${blockId}', 'caption', this.value)"
                               placeholder="Image caption">
                    </div>
                </div>
            `;
                        break;

                    case 'video':
                        html = `
                <div class="row">
                    <div class="mb-3 col-md-8">
                        <label class="form-label">Video URL</label>
                        <input type="url" class="form-control" value="${escapeHtml(data.url || '')}"
                               onchange="updateBlockData('${blockId}', 'url', this.value)"
                               placeholder="https://youtube.com/watch?v=...">
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Platform</label>
                        <select class="form-select" onchange="updateBlockData('${blockId}', 'platform', this.value)">
                            <option value="youtube" ${data.platform === 'youtube' ? 'selected' : ''}>YouTube</option>
                            <option value="vimeo" ${data.platform === 'vimeo' ? 'selected' : ''}>Vimeo</option>
                            <option value="other" ${data.platform === 'other' ? 'selected' : ''}>Other</option>
                        </select>
                    </div>
                </div>
            `;
                        break;

                    case 'url':
                        html = `
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="form-label">URL</label>
                        <input type="url" class="form-control" value="${escapeHtml(data.url || '')}"
                               onchange="updateBlockData('${blockId}', 'url', this.value)"
                               placeholder="https://example.com">
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Link Text</label>
                        <input type="text" class="form-control" value="${escapeHtml(data.text || '')}"
                               onchange="updateBlockData('${blockId}', 'text', this.value)"
                               placeholder="Click here">
                    </div>
                </div>
            `;
                        break;

                    case 'email':
                        html = `
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" value="${escapeHtml(data.email || '')}"
                           onchange="updateBlockData('${blockId}', 'email', this.value)"
                           placeholder="email@example.com">
                </div>
            `;
                        break;

                    case 'number':
                        html = `
                <div class="row">
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Value</label>
                        <input type="number" class="form-control" value="${data.value || 0}"
                               onchange="updateBlockData('${blockId}', 'value', this.value)"
                               step="any">
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Min</label>
                        <input type="number" class="form-control" value="${data.min || ''}"
                               onchange="updateBlockData('${blockId}', 'min', this.value)"
                               placeholder="Optional">
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Max</label>
                        <input type="number" class="form-control" value="${data.max || ''}"
                               onchange="updateBlockData('${blockId}', 'max', this.value)"
                               placeholder="Optional">
                    </div>
                </div>
            `;
                        break;

                    case 'date':
                        html = `
                <div class="mb-3">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-control" value="${data.date || ''}"
                           onchange="updateBlockData('${blockId}', 'date', this.value)">
                </div>
            `;
                        break;

                    case 'boolean':
                        html = `
                <div class="row">
                    <div class="mb-3 col-md-8">
                        <label class="form-label">Label</label>
                        <input type="text" class="form-control" value="${escapeHtml(data.label || '')}"
                               onchange="updateBlockData('${blockId}', 'label', this.value)"
                               placeholder="Option label">
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Value</label>
                        <div class="mt-2 form-check form-switch">
                            <input class="form-check-input" type="checkbox" ${data.value ? 'checked' : ''}
                                   onchange="updateBlockData('${blockId}', 'value', this.checked)">
                            <label class="form-check-label">${data.value ? 'True' : 'False'}</label>
                        </div>
                    </div>
                </div>
            `;
                        break;

                    case 'list':
                        html = `
                <div class="mb-3">
                    <label class="form-label">List Items</label>
                    <div class="list-items" id="list-items-${blockId}">
                        ${(data.items || []).map((item, idx) => `
                                                    <div class="mb-2 input-group">
                                                        <input type="text" class="form-control" value="${escapeHtml(item)}"
                                                               onchange="updateListItem('${blockId}', ${idx}, this.value)">
                                                        <button class="btn btn-light-danger" type="button" onclick="removeListItem('${blockId}', ${idx})">
                                                            <i class="ki-duotone ki-trash fs-2"></i>
                                                        </button>
                                                    </div>
                                                `).join('')}
                    </div>
                    <button type="button" class="btn btn-sm btn-light-primary" onclick="addListItem('${blockId}')">
                        <i class="ki-duotone ki-plus fs-2"></i> Add Item
                    </button>
                </div>
            `;
                        break;

                    case 'select':
                        html = `
                <div class="mb-3">
                    <label class="form-label">Options</label>
                    <div class="select-options" id="select-options-${blockId}">
                        ${(data.options || []).map((option, idx) => `
                                                    <div class="mb-2 input-group">
                                                        <input type="text" class="form-control" value="${escapeHtml(option)}"
                                                               onchange="updateSelectOption('${blockId}', ${idx}, this.value)"
                                                               placeholder="Option ${idx + 1}">
                                                        <button class="btn btn-light-danger" type="button" onclick="removeSelectOption('${blockId}', ${idx})">
                                                            <i class="ki-duotone ki-trash fs-2"></i>
                                                        </button>
                                                    </div>
                                                `).join('')}
                    </div>
                    <button type="button" class="mb-3 btn btn-sm btn-light-primary" onclick="addSelectOption('${blockId}')">
                        <i class="ki-duotone ki-plus fs-2"></i> Add Option
                    </button>
                    <label class="form-label">Selected Value</label>
                    <input type="text" class="form-control" value="${escapeHtml(data.value || '')}"
                           onchange="updateBlockData('${blockId}', 'value', this.value)"
                           placeholder="Default selected value">
                </div>
            `;
                        break;

                    case 'button':
                        html = `
                <div class="row">
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Button Text</label>
                        <input type="text" class="form-control" value="${escapeHtml(data.text || '')}"
                               onchange="updateBlockData('${blockId}', 'text', this.value)"
                               placeholder="Click Me">
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">URL</label>
                        <input type="url" class="form-control" value="${escapeHtml(data.url || '')}"
                               onchange="updateBlockData('${blockId}', 'url', this.value)"
                               placeholder="https://...">
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Style</label>
                        <select class="form-select" onchange="updateBlockData('${blockId}', 'style', this.value)">
                            <option value="primary" ${data.style === 'primary' ? 'selected' : ''}>Primary</option>
                            <option value="secondary" ${data.style === 'secondary' ? 'selected' : ''}>Secondary</option>
                            <option value="success" ${data.style === 'success' ? 'selected' : ''}>Success</option>
                            <option value="danger" ${data.style === 'danger' ? 'selected' : ''}>Danger</option>
                            <option value="warning" ${data.style === 'warning' ? 'selected' : ''}>Warning</option>
                            <option value="info" ${data.style === 'info' ? 'selected' : ''}>Info</option>
                        </select>
                    </div>
                </div>
            `;
                        break;

                    case 'table':
                        const headers = data.headers || [];
                        const rows = data.rows || [];
                        html = `
                <div class="table-builder">
                    <div class="mb-3">
                        <label class="form-label">Table Headers</label>
                        <div id="table-headers-${blockId}">
                            ${headers.map((header, idx) => `
                                                        <div class="mb-2 input-group">
                                                            <input type="text" class="form-control" value="${escapeHtml(header)}"
                                                                   onchange="updateTableHeader('${blockId}', ${idx}, this.value)"
                                                                   placeholder="Column ${idx + 1}">
                                                            <button class="btn btn-light-danger" type="button" onclick="removeTableColumn('${blockId}', ${idx})">
                                                                <i class="ki-duotone ki-trash fs-2"></i>
                                                            </button>
                                                        </div>
                                                    `).join('')}
                        </div>
                        <button type="button" class="btn btn-sm btn-light-primary" onclick="addTableColumn('${blockId}')">
                            <i class="ki-duotone ki-plus fs-2"></i> Add Column
                        </button>
                    </div>
                    <div class="my-5 separator"></div>
                    <div class="mb-3">
                        <label class="form-label">Table Rows</label>
                        <div id="table-rows-${blockId}">
                            ${rows.map((row, rowIdx) => `
                                                        <div class="mb-2 card">
                                                            <div class="py-2 card-body">
                                                                <div class="mb-2 d-flex justify-content-between align-items-center">
                                                                    <strong>Row ${rowIdx + 1}</strong>
                                                                    <button class="btn btn-sm btn-light-danger" type="button" onclick="removeTableRow('${blockId}', ${rowIdx})">
                                                                        <i class="ki-duotone ki-trash fs-2"></i>
                                                                    </button>
                                                                </div>
                                                                ${row.map((cell, cellIdx) => `
                                            <input type="text" class="mb-2 form-control form-control-sm" 
                                                   value="${escapeHtml(cell)}"
                                                   onchange="updateTableCell('${blockId}', ${rowIdx}, ${cellIdx}, this.value)"
                                                   placeholder="${headers[cellIdx] || 'Cell ' + (cellIdx + 1)}">
                                        `).join('')}
                                                            </div>
                                                        </div>
                                                    `).join('')}
                        </div>
                        <button type="button" class="btn btn-sm btn-light-primary" onclick="addTableRow('${blockId}')">
                            <i class="ki-duotone ki-plus fs-2"></i> Add Row
                        </button>
                    </div>
                </div>
            `;
                        break;

                    case 'component':
                        const children = data.children || [];
                        html = `
                <div class="mb-3">
                    <label class="form-label">Component Group Name</label>
                    <input type="text" class="form-control" value="${escapeHtml(data.name || '')}"
                           onchange="updateBlockData('${blockId}', 'name', this.value)"
                           placeholder="Component Group Name">
                </div>
                <div class="my-5 separator"></div>
                <div class="mb-3">
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <label class="mb-0 form-label">Nested Content Blocks</label>
                        <button type="button" class="btn btn-sm btn-light-primary" onclick="addChildBlock('${blockId}')">
                            <i class="ki-duotone ki-plus fs-2"></i> Add Child Block
                        </button>
                    </div>
                    <div id="component-children-${blockId}" class="component-children">
                        ${children.length === 0 ? '<div class="alert alert-light">No child blocks yet. Click "Add Child Block" to add content to this component group.</div>' : ''}
                        ${children.map((child, idx) => renderChildBlock(blockId, child, idx)).join('')}
                    </div>
                </div>
            `;
                        break;
                }

                return html;
            }

            function updateBlockLabel(blockId, label) {
                const block = contentBlocks.find(b => b.id === blockId);
                if (block) {
                    block.label = label;
                }
            }

            function toggleRepeatable(blockId) {
                const block = contentBlocks.find(b => b.id === blockId);
                if (block) {
                    block.repeatable = !block.repeatable;
                }
            }

            function updateBlockData(blockId, field, value) {
                const block = contentBlocks.find(b => b.id === blockId);
                if (block) {
                    block.data[field] = value;
                }
            }

            function removeBlock(blockId) {
                Swal.fire({
                    text: "Are you sure you want to remove this block?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel",
                    customClass: {
                        confirmButton: "btn btn-danger",
                        cancelButton: "btn btn-active-light"
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        contentBlocks = contentBlocks.filter(b => b.id !== blockId);
                        $(`.content-block[data-block-id="${blockId}"]`).remove();

                        if (contentBlocks.length === 0) {
                            $('#contentBlocks').html(`
                    <div class="py-10 text-center">
                        <i class="mb-3 ki-duotone ki-abstract-26 fs-3x text-muted">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <p class="text-muted fs-5">No content blocks yet. Click "Add Content Block" to start building your page.</p>
                    </div>
                `);
                        }

                        toastr.success('Block removed successfully');
                    }
                });
            }

            function updateBlockOrder() {
                const newOrder = [];
                $('.content-block').each(function() {
                    const blockId = $(this).data('block-id');
                    const block = contentBlocks.find(b => b.id === blockId);
                    if (block) {
                        newOrder.push(block);
                    }
                });
                contentBlocks = newOrder;
            }

            // List functions
            function addListItem(blockId) {
                const block = contentBlocks.find(b => b.id === blockId);
                if (block) {
                    if (!block.data.items) block.data.items = [];
                    block.data.items.push('');
                    refreshBlock(blockId);
                }
            }

            function updateListItem(blockId, index, value) {
                const block = contentBlocks.find(b => b.id === blockId);
                if (block && block.data.items) {
                    block.data.items[index] = value;
                }
            }

            function removeListItem(blockId, index) {
                const block = contentBlocks.find(b => b.id === blockId);
                if (block && block.data.items) {
                    block.data.items.splice(index, 1);
                    refreshBlock(blockId);
                }
            }

            // Select functions
            function addSelectOption(blockId) {
                const block = contentBlocks.find(b => b.id === blockId);
                if (block) {
                    if (!block.data.options) block.data.options = [];
                    block.data.options.push('');
                    refreshBlock(blockId);
                }
            }

            function updateSelectOption(blockId, index, value) {
                const block = contentBlocks.find(b => b.id === blockId);
                if (block && block.data.options) {
                    block.data.options[index] = value;
                }
            }

            function removeSelectOption(blockId, index) {
                const block = contentBlocks.find(b => b.id === blockId);
                if (block && block.data.options) {
                    block.data.options.splice(index, 1);
                    refreshBlock(blockId);
                }
            }

            // Table functions
            function addTableColumn(blockId) {
                const block = contentBlocks.find(b => b.id === blockId);
                if (block) {
                    if (!block.data.headers) block.data.headers = [];
                    block.data.headers.push('');
                    // Add new cell to each row
                    if (block.data.rows) {
                        block.data.rows.forEach(row => row.push(''));
                    }
                    refreshBlock(blockId);
                }
            }

            function updateTableHeader(blockId, index, value) {
                const block = contentBlocks.find(b => b.id === blockId);
                if (block && block.data.headers) {
                    block.data.headers[index] = value;
                }
            }

            function removeTableColumn(blockId, index) {
                const block = contentBlocks.find(b => b.id === blockId);
                if (block) {
                    if (block.data.headers) block.data.headers.splice(index, 1);
                    if (block.data.rows) {
                        block.data.rows.forEach(row => row.splice(index, 1));
                    }
                    refreshBlock(blockId);
                }
            }

            function addTableRow(blockId) {
                const block = contentBlocks.find(b => b.id === blockId);
                if (block) {
                    if (!block.data.rows) block.data.rows = [];
                    const columnCount = block.data.headers ? block.data.headers.length : 1;
                    block.data.rows.push(new Array(columnCount).fill(''));
                    refreshBlock(blockId);
                }
            }

            function updateTableCell(blockId, rowIdx, cellIdx, value) {
                const block = contentBlocks.find(b => b.id === blockId);
                if (block && block.data.rows && block.data.rows[rowIdx]) {
                    block.data.rows[rowIdx][cellIdx] = value;
                }
            }

            function removeTableRow(blockId, rowIdx) {
                const block = contentBlocks.find(b => b.id === blockId);
                if (block && block.data.rows) {
                    block.data.rows.splice(rowIdx, 1);
                    refreshBlock(blockId);
                }
            }

            function refreshBlock(blockId) {
                const block = contentBlocks.find(b => b.id === blockId);
                if (block) {
                    const $blockEl = $(`.content-block[data-block-id="${blockId}"]`);
                    $blockEl.find('.block-content').html(renderBlockContent(block));
                }
            }

            function saveContent() {
                // Update block order before saving
                updateBlockOrder();

                // Get data from all CKEditor instances before saving
                Object.keys(editorInstances).forEach(blockId => {
                    if (editorInstances[blockId]) {
                        const data = editorInstances[blockId].getData();
                        updateBlockData(blockId, 'content', data);
                    }
                });

                const formData = new FormData($('#contentForm')[0]);

                $.ajax({
                    url: $('#contentForm').attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success('Content saved successfully!');
                        // Clear localStorage after successful save
                        localStorage.removeItem('pageContent_{{ $page->id }}');
                    },
                    error: function(xhr) {
                        toastr.error('Error saving content. Please try again.');
                        console.error(xhr.responseText);
                    }
                });
            }

            function saveToLocalStorage() {
                updateBlockOrder();
                localStorage.setItem('pageContent_{{ $page->id }}', JSON.stringify(contentBlocks));
            }

            function previewContent() {
                updateBlockOrder();

                let previewHtml = '';
                contentBlocks.forEach(block => {
                    const data = block.data;
                    let blockHtml = '';

                    switch (block.type) {
                        case 'paragraph':
                            blockHtml = `<p>${escapeHtml(data.content || '')}</p>`;
                            break;
                        case 'custom':
                            blockHtml = `<div class="custom-content">${data.content || ''}</div>`;
                            break;
                        case 'text':
                            blockHtml = `<p>${escapeHtml(data.content || '')}</p>`;
                            break;
                        case 'heading':
                            blockHtml = `<${data.level}>${escapeHtml(data.content || '')}</${data.level}>`;
                            break;
                        case 'image':
                            blockHtml =
                                `<figure><img src="${escapeHtml(data.url || '')}" alt="${escapeHtml(data.alt || '')}" class="img-fluid"><figcaption>${escapeHtml(data.caption || '')}</figcaption></figure>`;
                            break;
                        case 'video':
                            blockHtml =
                                `<div class="ratio ratio-16x9"><iframe src="${escapeHtml(data.url || '')}" allowfullscreen></iframe></div>`;
                            break;
                        case 'url':
                            blockHtml =
                                `<a href="${escapeHtml(data.url || '')}" target="_blank">${escapeHtml(data.text || data.url || '')}</a>`;
                            break;
                        case 'email':
                            blockHtml =
                                `<a href="mailto:${escapeHtml(data.email || '')}">${escapeHtml(data.email || '')}</a>`;
                            break;
                        case 'number':
                            blockHtml = `<span class="number-value">${data.value || 0}</span>`;
                            break;
                        case 'date':
                            blockHtml = `<time datetime="${data.date || ''}">${data.date || ''}</time>`;
                            break;
                        case 'boolean':
                            blockHtml =
                                `<div class="form-check"><input type="checkbox" ${data.value ? 'checked' : ''} disabled><label>${escapeHtml(data.label || '')}</label></div>`;
                            break;
                        case 'list':
                            blockHtml =
                                `<ul>${(data.items || []).map(item => `<li>${escapeHtml(item)}</li>`).join('')}</ul>`;
                            break;
                        case 'select':
                            blockHtml =
                                `<select class="form-select">${(data.options || []).map(opt => `<option ${opt === data.value ? 'selected' : ''}>${escapeHtml(opt)}</option>`).join('')}</select>`;
                            break;
                        case 'button':
                            blockHtml =
                                `<a href="${escapeHtml(data.url || '#')}" class="btn btn-${data.style || 'primary'}">${escapeHtml(data.text || 'Button')}</a>`;
                            break;
                        case 'table':
                            const headers = data.headers || [];
                            const rows = data.rows || [];
                            blockHtml =
                                `<table class="table table-bordered"><thead><tr>${headers.map(h => `<th>${escapeHtml(h)}</th>`).join('')}</tr></thead><tbody>${rows.map(row => `<tr>${row.map(cell => `<td>${escapeHtml(cell)}</td>`).join('')}</tr>`).join('')}</tbody></table>`;
                            break;
                        case 'component':
                            const children = data.children || [];
                            let childrenHtml = '';
                            if (children.length > 0) {
                                childrenHtml = children.map(child => {
                                    let childContent = '';
                                    switch (child.type) {
                                        case 'paragraph':
                                        case 'text':
                                            childContent = `<p>${escapeHtml(child.data.content || '')}</p>`;
                                            break;
                                        case 'heading':
                                            childContent =
                                                `<${child.data.level || 'h3'}>${escapeHtml(child.data.content || '')}</${child.data.level || 'h3'}>`;
                                            break;
                                        case 'image':
                                            childContent =
                                                `<img src="${escapeHtml(child.data.url || '')}" alt="${escapeHtml(child.data.alt || '')}" class="img-fluid">`;
                                            break;
                                        case 'button':
                                            childContent =
                                                `<a href="${escapeHtml(child.data.url || '#')}" class="btn btn-primary">${escapeHtml(child.data.text || 'Button')}</a>`;
                                            break;
                                        default:
                                            childContent = `<div>${child.type}</div>`;
                                    }
                                    return childContent;
                                }).join('');
                            }
                            blockHtml =
                                `<div class="mb-4 component-group"><h5>${escapeHtml(data.name || 'Component')}</h5><div class="component-content">${childrenHtml || '<p class="text-muted">No content</p>'}</div></div>`;
                            break;
                    }

                    previewHtml +=
                        `<div class="mb-5"><h6 class="mb-2 text-muted">${block.label}</h6>${blockHtml}</div>`;
                });

                $('#previewContent').html(previewHtml || '<p class="text-muted">No content to preview</p>');
                $('#previewModal').modal('show');
            }

            // CKEditor instances storage
            const editorInstances = {};

            function initCKEditor(blockId, initialContent) {
                const editorElementId = `ckeditor-${blockId}`;

                // Check if element exists
                if (!document.getElementById(editorElementId)) {
                    setTimeout(() => initCKEditor(blockId, initialContent), 200);
                    return;
                }

                // Destroy existing instance if any
                if (editorInstances[blockId]) {
                    editorInstances[blockId].destroy().catch(error => console.error(error));
                }

                ClassicEditor
                    .create(document.getElementById(editorElementId), {
                        toolbar: {
                            items: [
                                'heading', '|',
                                'bold', 'italic', 'underline', 'strikethrough', '|',
                                'link', 'bulletedList', 'numberedList', '|',
                                'alignment', '|',
                                'imageUpload', 'blockQuote', 'insertTable', 'mediaEmbed', '|',
                                'undo', 'redo', '|',
                                'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                                'code', 'codeBlock', 'horizontalLine', '|',
                                'indent', 'outdent'
                            ],
                            shouldNotGroupWhenFull: true
                        },
                        image: {
                            toolbar: [
                                'imageTextAlternative', 'imageStyle:full', 'imageStyle:side', 'linkImage'
                            ]
                        },
                        table: {
                            contentToolbar: [
                                'tableColumn', 'tableRow', 'mergeTableCells', 'tableCellProperties', 'tableProperties'
                            ]
                        }
                    })
                    .then(editor => {
                        editorInstances[blockId] = editor;

                        // Set initial content
                        if (initialContent) {
                            editor.setData(initialContent);
                        }

                        // Listen for changes
                        editor.model.document.on('change:data', () => {
                            const data = editor.getData();
                            updateBlockData(blockId, 'content', data);
                        });
                    })
                    .catch(error => {
                        console.error('CKEditor initialization error:', error);
                    });
            }

            function addChildBlock(parentBlockId) {
                const parentBlock = contentBlocks.find(b => b.id === parentBlockId);
                if (!parentBlock) return;

                // Show modal to select child block type
                $('#addChildBlockModal').data('parent-id', parentBlockId).modal('show');
            }

            function addChildBlockType(parentBlockId, type) {
                const parentBlock = contentBlocks.find(b => b.id === parentBlockId);
                if (!parentBlock) return;

                const childBlock = {
                    id: 'child_' + Date.now() + '_' + (++blockCounter),
                    type: type,
                    label: type.charAt(0).toUpperCase() + type.slice(1),
                    data: getDefaultData(type)
                };

                if (!parentBlock.data.children) {
                    parentBlock.data.children = [];
                }
                parentBlock.data.children.push(childBlock);

                // Refresh the parent block
                refreshBlock(parentBlockId);

                $('#addChildBlockModal').modal('hide');
                toastr.success('Child block added successfully');
            }

            function renderChildBlock(parentBlockId, child, index) {
                const childId = child.id;
                return `
                    <div class="mb-3 card child-block" data-child-id="${childId}">
                        <div class="py-3 card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="gap-2 d-flex align-items-center">
                                    <span class="badge badge-light-info">${child.type.toUpperCase()}</span>
                                    <input type="text" class="form-control form-control-sm w-150px" 
                                           value="${escapeHtml(child.label)}" 
                                           onchange="updateChildBlockLabel('${parentBlockId}', ${index}, this.value)"
                                           placeholder="Child Label">
                                </div>
                                <button type="button" class="btn btn-sm btn-icon btn-light-danger" 
                                        onclick="removeChildBlock('${parentBlockId}', ${index})">
                                    <i class="ki-duotone ki-trash fs-2"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            ${renderChildBlockContent(parentBlockId, child, index)}
                        </div>
                    </div>
                `;
            }

            function renderChildBlockContent(parentBlockId, child, index) {
                const childId = child.id;
                const data = child.data;
                let html = '';

                switch (child.type) {
                    case 'paragraph':
                        html = `
                            <textarea class="form-control" rows="3" 
                                      onchange="updateChildBlockData('${parentBlockId}', ${index}, 'content', this.value)"
                                      placeholder="Enter paragraph...">${escapeHtml(data.content || '')}</textarea>
                        `;
                        break;
                    case 'text':
                        html = `
                            <textarea class="form-control" rows="4" 
                                      onchange="updateChildBlockData('${parentBlockId}', ${index}, 'content', this.value)"
                                      placeholder="Enter text...">${escapeHtml(data.content || '')}</textarea>
                        `;
                        break;
                    case 'heading':
                        html = `
                            <div class="row">
                                <div class="col-md-8">
                                    <input type="text" class="form-control" 
                                           value="${escapeHtml(data.content || '')}"
                                           onchange="updateChildBlockData('${parentBlockId}', ${index}, 'content', this.value)"
                                           placeholder="Heading text...">
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select" 
                                            onchange="updateChildBlockData('${parentBlockId}', ${index}, 'level', this.value)">
                                        <option value="h1" ${data.level === 'h1' ? 'selected' : ''}>H1</option>
                                        <option value="h2" ${data.level === 'h2' ? 'selected' : ''}>H2</option>
                                        <option value="h3" ${data.level === 'h3' ? 'selected' : ''}>H3</option>
                                        <option value="h4" ${data.level === 'h4' ? 'selected' : ''}>H4</option>
                                        <option value="h5" ${data.level === 'h5' ? 'selected' : ''}>H5</option>
                                        <option value="h6" ${data.level === 'h6' ? 'selected' : ''}>H6</option>
                                    </select>
                                </div>
                            </div>
                        `;
                        break;
                    case 'image':
                        html = `
                            <input type="url" class="mb-2 form-control" 
                                   value="${escapeHtml(data.url || '')}"
                                   onchange="updateChildBlockData('${parentBlockId}', ${index}, 'url', this.value)"
                                   placeholder="Image URL">
                            <input type="text" class="form-control" 
                                   value="${escapeHtml(data.alt || '')}"
                                   onchange="updateChildBlockData('${parentBlockId}', ${index}, 'alt', this.value)"
                                   placeholder="Alt text">
                        `;
                        break;
                    case 'button':
                        html = `
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" 
                                           value="${escapeHtml(data.text || '')}"
                                           onchange="updateChildBlockData('${parentBlockId}', ${index}, 'text', this.value)"
                                           placeholder="Button text">
                                </div>
                                <div class="col-md-6">
                                    <input type="url" class="form-control" 
                                           value="${escapeHtml(data.url || '')}"
                                           onchange="updateChildBlockData('${parentBlockId}', ${index}, 'url', this.value)"
                                           placeholder="Button URL">
                                </div>
                            </div>
                        `;
                        break;
                    default:
                        html = `<div class="alert alert-light">Content type: ${child.type}</div>`;
                }

                return html;
            }

            function updateChildBlockLabel(parentBlockId, index, label) {
                const parentBlock = contentBlocks.find(b => b.id === parentBlockId);
                if (parentBlock && parentBlock.data.children && parentBlock.data.children[index]) {
                    parentBlock.data.children[index].label = label;
                }
            }

            function updateChildBlockData(parentBlockId, index, field, value) {
                const parentBlock = contentBlocks.find(b => b.id === parentBlockId);
                if (parentBlock && parentBlock.data.children && parentBlock.data.children[index]) {
                    parentBlock.data.children[index].data[field] = value;
                }
            }

            function removeChildBlock(parentBlockId, index) {
                Swal.fire({
                    text: "Are you sure you want to remove this child block?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel",
                    customClass: {
                        confirmButton: "btn btn-danger",
                        cancelButton: "btn btn-active-light"
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const parentBlock = contentBlocks.find(b => b.id === parentBlockId);
                        if (parentBlock && parentBlock.data.children) {
                            parentBlock.data.children.splice(index, 1);
                            refreshBlock(parentBlockId);
                            toastr.success('Child block removed successfully');
                        }
                    }
                });
            }

            function escapeHtml(text) {
                if (!text) return '';
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.toString().replace(/[&<>"']/g, m => map[m]);
            }
        </script>
    @endpush
</x-default-layout>
