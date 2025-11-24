<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0"><?php echo $title; ?></h2>
    <?php if ($this->session->userdata('logged_in')): ?>
    <div>
        <a href="<?php echo site_url('visitors/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Visitor Entry
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- DataTables server-side table -->
<div class="table-responsive">
    <table id="visitors_table" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th data-data="name">Name</th>
                <th data-data="visit_date">Date</th>
                <th data-data="visit_time">Time</th>
                <th data-data="location_name">Location</th>
                <th data-data="photos">Photos</th>
                <th data-data="actions">Actions</th>
            </tr>
        </thead>
    </table>
</div>

<!-- DataTables assets -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function(){
    $('#visitors_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?php echo site_url('visitors/ajax_list'); ?>',
            type: 'POST'
        },
        columns: [
            { data: 'name' },
            { data: 'visit_date' },
            { data: 'visit_time' },
            { data: 'location_name' },
            { data: 'photos', orderable: false, searchable: false },
            { data: 'actions', orderable: false, searchable: false }
        ],
        order: [[1, 'desc']],
        pageLength: 10,
        lengthMenu: [ [10, 25, 50, 100], [10, 25, 50, 100] ]
    });
});
</script>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this visitor record?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" class="btn btn-danger" id="confirmDelete">Delete</a>
            </div>
        </div>
    </div>
</div>

<!-- Image preview modal -->
<div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="photoModalImg" src="" class="img-fluid" alt="Photo Preview">
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    // Handle delete button click
    $(document).on('click', '.delete-visitor', function() {
        var id = $(this).data('id');
        $('#confirmDelete').attr('href', '<?php echo site_url('visitors/delete/'); ?>' + id);
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    });

    // Handle photo preview
    $(document).on('click', '.photo-btn', function(e){
        e.preventDefault();
        var src = $(this).data('img');
        if (!src) return;
        $('#photoModalImg').attr('src', src);
        var modal = new bootstrap.Modal(document.getElementById('photoModal'));
        modal.show();
    });
});
</script>