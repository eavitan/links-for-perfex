<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
$is_edit = isset($link);
$form_id = $is_edit ? 'edit-link-form' : 'add-link-form';
$modal_title = $is_edit ? _l('edit_link') : _l('add_link');
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <h4 class="modal-title">
        <i class="fa fa-link"></i> <?php echo $modal_title; ?>
    </h4>
</div>

<div class="modal-body">
    <?php echo form_open('', ['id' => $form_id, 'class' => 'form-link']); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="url" class="control-label">
                    <span class="text-danger">*</span> <?php echo _l('link_url'); ?>
                </label>
                <input type="url"
                       id="url"
                       name="url"
                       class="form-control"
                       placeholder="https://example.com"
                       value="<?php echo $is_edit ? htmlspecialchars($link->url) : ''; ?>"
                       required>
                <small class="help-block"><?php echo _l('enter_valid_url'); ?></small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="title" class="control-label">
                    <?php echo _l('link_title'); ?>
                    <small class="text-muted">(<?php echo _l('optional'); ?>)</small>
                </label>
                <input type="text"
                       id="title"
                       name="title"
                       class="form-control"
                       placeholder="<?php echo _l('link_title_placeholder'); ?>"
                       value="<?php echo $is_edit ? htmlspecialchars($link->title) : ''; ?>">
                <small class="help-block"><?php echo _l('link_title_help'); ?></small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="description" class="control-label">
                    <?php echo _l('link_description'); ?>
                    <small class="text-muted">(<?php echo _l('optional'); ?>)</small>
                </label>
                <textarea name="description"
                          id="description"
                          class="form-control"
                          rows="3"
                          placeholder="<?php echo _l('link_description_placeholder'); ?>"><?php echo $is_edit ? htmlspecialchars($link->description) : ''; ?></textarea>
                <small class="help-block"><?php echo _l('link_description_help'); ?></small>
            </div>
        </div>
    </div>

    <!-- Hidden fields -->
    <?php if (!$is_edit): ?>
        <input type="hidden" name="relation" value="<?php echo isset($relation) ? $relation : ''; ?>">
        <input type="hidden" name="relation_id" value="<?php echo isset($relation_id) ? $relation_id : ''; ?>">
    <?php endif; ?>

    <?php echo form_close(); ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">
        <?php echo _l('close'); ?>
    </button>
    <button type="button" class="btn btn-primary" onclick="save_link_form();">
        <i class="fa fa-save"></i>
        <?php echo $is_edit ? _l('update') : _l('save'); ?>
    </button>
</div>

<script>
function save_link_form() {
    var form = $('#<?php echo $form_id; ?>');
    var submit_btn = $('.modal-footer .btn-primary');
    var original_text = submit_btn.html();

    // Validate form
    if (!form[0].checkValidity()) {
        form[0].reportValidity();
        return;
    }

    // Show loading state
    submit_btn.html('<i class="fa fa-spinner fa-spin"></i> <?php echo _l('saving'); ?>').prop('disabled', true);

    var url = '<?php echo admin_url('links_for_perfex/' . ($is_edit ? 'edit_link/' . $link->id : 'add_link')); ?>';

    $.ajax({
        url: url,
        type: 'POST',
        data: form.serialize(),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Show success message
                alert_float('success', response.message);

                // Close modal
                $('#link-modal').modal('hide');

                // Refresh the links display
                refresh_links_section();

            } else {
                alert_float('danger', response.message);
            }
        },
        error: function() {
            alert_float('danger', '<?php echo _l('something_went_wrong'); ?>');
        },
        complete: function() {
            // Restore button state
            submit_btn.html(original_text).prop('disabled', false);
        }
    });
}

// Auto-generate title from URL if title is empty
$('#url').on('blur', function() {
    var url = $(this).val();
    var title = $('#title').val();

    if (url && !title) {
        try {
            var urlObj = new URL(url);
            var hostname = urlObj.hostname.replace(/^www\./, '');
            var pathname = urlObj.pathname;

            // Generate a simple title from hostname
            var suggestedTitle = hostname.charAt(0).toUpperCase() + hostname.slice(1);

            // Add path info if meaningful
            if (pathname && pathname !== '/' && pathname.split('/').length > 2) {
                var pathParts = pathname.split('/').filter(function(part) {
                    return part.length > 0;
                });
                if (pathParts.length > 0) {
                    suggestedTitle += ' - ' + pathParts[0].charAt(0).toUpperCase() + pathParts[0].slice(1);
                }
            }

            $('#title').attr('placeholder', suggestedTitle + ' (auto-generated)');

        } catch (e) {
            // Invalid URL, ignore
        }
    }
});

// URL validation and formatting
$('#url').on('blur', function() {
    var url = $(this).val().trim();

    if (url && !url.match(/^https?:\/\//)) {
        // Auto-add protocol if missing
        $(this).val('https://' + url);
    }
});
</script>