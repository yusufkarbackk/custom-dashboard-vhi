<!-- Add Domain Modal -->
<div class="modal fade" id="addDomainModal" tabindex="-1" aria-labelledby="addDomainModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('domain.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDomainModalLabel">Add New Domain</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Domain Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description (optional)</label>
                        <input type="text" name="description" id="description" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="enabled" class="form-label">Enabled</label>
                        <select name="enabled" id="enabled" class="form-select">
                            <option value="true" selected>True</option>
                            <option value="false">False</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="options" class="form-label">Options (JSON, optional)</label>
                        <textarea name="options" id="options" class="form-control" rows="3"
                            placeholder='e.g. {"key": "value"}'></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Create Domain</button>
                </div>
            </div>
        </form>
    </div>
</div>