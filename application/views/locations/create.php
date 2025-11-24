<h2><?php echo $title; ?></h2>

<?php echo validation_errors(); ?>

<?php echo form_open('locations/create'); ?>
    <div class="mb-3">
        <label for="location_name" class="form-label">Location Name</label>
        <input type="text" class="form-control" name="location_name" id="location_name" required>
    </div>

    <div class="mb-3">
        <button type="submit" class="btn btn-success">Save Location</button>
    <a href="<?php echo site_url('locations'); ?>" class="btn btn-secondary">Cancel</a>
    </div>
</form>