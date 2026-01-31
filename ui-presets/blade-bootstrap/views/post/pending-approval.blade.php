@extends ('forum::layouts.main', ['breadcrumbs_append' => [trans('forum::posts.pending_approval')]])

@section ('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ trans('forum::posts.pending_approval') }}</h1>
        
        @if ($posts->count() > 0)
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="select-all">
                <label class="form-check-label" for="select-all">
                    {{ trans('forum::posts.select_all') }}
                </label>
            </div>
        @endif
    </div>

    <div class="mb-4">
        @forelse ($posts as $post)
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input class="form-check-input post-checkbox" type="checkbox" value="{{ $post->id }}">
                    </div>
                    <div class="ms-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <a href="{{ Forum::route('thread.show', $post->thread) }}?post={{ $post->id }}" class="text-decoration-none">
                                    {{ $post->thread->title }}
                                </a>
                            </h5>
                            <div class="text-muted small">
                                {{ $post->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="text-muted small">
                            {{ trans('forum::posts.posted_by') }}: {{ $post->authorName }}
                            <span class="mx-1">•</span>
                            {{ trans('forum::categories.category') }}: {{ $post->thread->category->title }}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="post-content mb-3">
                        {!! $post->content !!}
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-success approve-btn" data-post-id="{{ $post->id }}">
                                {{ trans('forum::general.approve') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-post-id="{{ $post->id }}">
                                {{ trans('forum::general.delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body text-center text-muted">
                    {{ trans('forum::posts.none_found') }}
                </div>
            </div>
        @endforelse
    </div>

    @if ($posts->hasPages())
        <div class="d-flex justify-content-between">
            <div>
                <button type="button" class="btn btn-danger" id="delete-selected" disabled>
                    {{ trans('forum::general.delete_selection') }}
                </button>
            </div>
            <div>
                <button type="button" class="btn btn-success" id="approve-selected" disabled>
                    {{ trans('forum::general.approve_selection') }}
                </button>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="delete-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('forum::general.delete') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ trans('forum::general.generic_confirm') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('forum::general.cancel') }}</button>
                    <form id="delete-form" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">{{ trans('forum::general.proceed') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Confirmation Modal -->
    <div class="modal fade" id="approve-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('forum::general.approve') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ trans('forum::general.generic_confirm') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('forum::general.cancel') }}</button>
                    <form id="approve-form" method="POST" style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success">{{ trans('forum::general.proceed') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const selectAllCheckbox = document.getElementById('select-all');
                const postCheckboxes = document.querySelectorAll('.post-checkbox');
                const deleteSelectedBtn = document.getElementById('delete-selected');
                const approveSelectedBtn = document.getElementById('approve-selected');
                const deleteForm = document.getElementById('delete-form');
                const approveForm = document.getElementById('approve-form');

                // Handle select all
                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', function() {
                        postCheckboxes.forEach(checkbox => {
                            checkbox.checked = this.checked;
                        });
                        updateActionButtons();
                    });
                }

                // Handle individual checkbox changes
                postCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', updateActionButtons);
                });

                // Update action buttons based on selection
                function updateActionButtons() {
                    const hasSelection = Array.from(postCheckboxes).some(checkbox => checkbox.checked);
                    deleteSelectedBtn.disabled = !hasSelection;
                    approveSelectedBtn.disabled = !hasSelection;
                }

                // Handle delete button clicks
                document.querySelectorAll('.delete-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const postId = this.dataset.postId;
                        deleteForm.action = '{{ Forum::route('forum.post.delete', $post) }}';
                        new bootstrap.Modal(document.getElementById('delete-modal')).show();
                    });
                });

                // Handle approve button clicks
                document.querySelectorAll('.approve-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const postId = this.dataset.postId;
                        approveForm.action = '{{ Forum::route('forum.post.approve', $post) }}';
                        new bootstrap.Modal(document.getElementById('approve-modal')).show();
                    });
                });

                // Handle bulk delete
                deleteSelectedBtn.addEventListener('click', function() {
                    const selectedIds = Array.from(postCheckboxes)
                        .filter(checkbox => checkbox.checked)
                        .map(checkbox => checkbox.value);
                    
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ Forum::route("forum.bulk.post.delete") }}';
                    form.innerHTML = `
                        @csrf
                        @method('DELETE')
                        ${selectedIds.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('')}
                    `;
                    document.body.appendChild(form);
                    form.submit();
                });

                // Handle bulk approve
                approveSelectedBtn.addEventListener('click', function() {
                    const selectedIds = Array.from(postCheckboxes)
                        .filter(checkbox => checkbox.checked)
                        .map(checkbox => checkbox.value);
                    
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ Forum::route("forum.bulk.post.approve") }}';
                    form.innerHTML = `
                        @csrf
                        @method('PATCH')
                        ${selectedIds.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('')}
                    `;
                    document.body.appendChild(form);
                    form.submit();
                });
            });
        </script>
    @endpush
@stop
