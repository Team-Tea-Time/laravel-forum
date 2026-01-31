@extends ('forum::layouts.main', ['breadcrumbs_append' => [trans('forum::threads.pending_approval')]])

@section ('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ trans('forum::threads.pending_approval') }}</h1>

        @if ($threads->count() > 0)
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="select-all">
                <label class="form-check-label" for="select-all">
                    {{ trans('forum::threads.select_all') }}
                </label>
            </div>
        @endif
    </div>

    <div class="mb-4">
        @forelse ($threads as $thread)
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input class="form-check-input thread-checkbox" type="checkbox" value="{{ $thread->id }}">
                    </div>
                    <div class="ms-2">
                        <a href="{{ Forum::route('thread.show', $thread) }}" class="text-decoration-none">
                            <h5 class="mb-0">{{ $thread->title }}</h5>
                        </a>
                        <div class="text-muted small">
                            {{ trans('forum::threads.posted_by') }}: {{ $thread->authorName }}
                            <span class="mx-1">•</span>
                            {{ $thread->created_at->diffForHumans() }}
                            <span class="mx-1">•</span>
                            {{ trans('forum::categories.category') }}: {{ $thread->category->title }}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-success approve-btn" data-thread-id="{{ $thread->id }}">
                                {{ trans('forum::general.approve') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-thread-id="{{ $thread->id }}">
                                {{ trans('forum::general.delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body text-center text-muted">
                    {{ trans('forum::threads.none_found') }}
                </div>
            </div>
        @endforelse
    </div>

    @if ($threads->hasPages())
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
                const threadCheckboxes = document.querySelectorAll('.thread-checkbox');
                const deleteSelectedBtn = document.getElementById('delete-selected');
                const approveSelectedBtn = document.getElementById('approve-selected');
                const deleteForm = document.getElementById('delete-form');
                const approveForm = document.getElementById('approve-form');

                // Handle select all
                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', function() {
                        threadCheckboxes.forEach(checkbox => {
                            checkbox.checked = this.checked;
                        });
                        updateActionButtons();
                    });
                }

                // Handle individual checkbox changes
                threadCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', updateActionButtons);
                });

                // Update action buttons based on selection
                function updateActionButtons() {
                    const hasSelection = Array.from(threadCheckboxes).some(checkbox => checkbox.checked);
                    deleteSelectedBtn.disabled = !hasSelection;
                    approveSelectedBtn.disabled = !hasSelection;
                }

                // Handle delete button clicks
                document.querySelectorAll('.delete-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const threadId = this.dataset.threadId;
                        deleteForm.action = '{{ Forum::route('forum.thread.delete', $thread) }}';
                        new bootstrap.Modal(document.getElementById('delete-modal')).show();
                    });
                });

                // Handle approve button clicks
                document.querySelectorAll('.approve-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const threadId = this.dataset.threadId;
                        approveForm.action = '{{ Forum::route('forum.thread.approve', $thread) }}';
                        new bootstrap.Modal(document.getElementById('approve-modal')).show();
                    });
                });

                // Handle bulk delete
                deleteSelectedBtn.addEventListener('click', function() {
                    const selectedIds = Array.from(threadCheckboxes)
                        .filter(checkbox => checkbox.checked)
                        .map(checkbox => checkbox.value);

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ Forum::route("forum.bulk.thread.delete") }}';
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
                    const selectedIds = Array.from(threadCheckboxes)
                        .filter(checkbox => checkbox.checked)
                        .map(checkbox => checkbox.value);

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ Forum::route("forum.bulk.thread.approve") }}';
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
