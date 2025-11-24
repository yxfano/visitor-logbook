<h2><?php echo $title; ?></h2>

<div class="row">
    <div class="col-md-6">
        <table class="table">
            <tr>
                <th>Name:</th>
                <td><?php echo htmlspecialchars($visitor->name); ?></td>
            </tr>
            <tr>
                <th>Visit Date:</th>
                <td><?php echo $visitor->visit_date; ?></td>
            </tr>
            <tr>
                <th>Visit Time:</th>
                <td><?php echo $visitor->visit_time; ?></td>
            </tr>
            <tr>
                <th>Reason:</th>
                <td><?php echo htmlspecialchars($visitor->reason); ?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <?php if ($visitor->face_photo): ?>
        <div class="mb-3">
            <h4>Face Photo</h4>
            <img src="<?php echo base_url('uploads/faces/'.$visitor->face_photo); ?>" 
                 alt="Visitor Face Photo" class="img-fluid">
        </div>
        <?php endif; ?>

        <?php if ($visitor->id_photo): ?>
        <div class="mb-3">
            <h4>ID Photo</h4>
            <img src="<?php echo base_url('uploads/ids/'.$visitor->id_photo); ?>" 
                 alt="Visitor ID Photo" class="img-fluid">
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="mt-3">
    <a href="<?php echo site_url('visitors'); ?>" class="btn btn-secondary">Back to List</a>
    <button type="button" class="btn btn-danger delete-visitor" data-id="<?php echo $visitor->id; ?>">Delete Record</button>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModalVisitor" tabindex="-1" aria-hidden="true">
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
                <a href="#" class="btn btn-danger" id="confirmDeleteVisitor">Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $(document).on('click', '.delete-visitor', function(){
        var id = $(this).data('id');
        $('#confirmDeleteVisitor').attr('href', '<?php echo site_url('visitors/delete/'); ?>' + id);
        var modal = new bootstrap.Modal(document.getElementById('deleteModalVisitor'));
        modal.show();
    });
});
</script>