<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Extended Task Manager - Task Links Section -->
<div class="task-links-wrapper" style="margin-bottom: 20px;">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h4 class="panel-title">
                <i class="fa fa-link mright5"></i>
                <?php echo _l('task_links'); ?>
            </h4>
        </div>
        <div class="panel-body">
            <p class="text-muted mbot15"><?php echo _l('task_links_info'); ?></p>

<?php
// Get links for this task (database + demo fallback)
$links = links_for_perfex_get_links('task', isset($task) ? $task->id : 1);
?>

<div class="task-links-section">
    <?php if (count($links) > 0): ?>
        <div class="links-list">
            <?php foreach ($links as $link): ?>
                <div class="panel panel-default link-item-panel mbot10" data-link-id="<?php echo $link->id; ?>">
                    <div class="panel-body">
                        <div class="link-header">
                            <h6 class="no-margin">
                                <i class="fa <?php echo links_for_perfex_get_link_icon($link->url); ?> text-primary mright5"></i>
                                <?php
                                $display_title = !empty($link->title) ? htmlspecialchars($link->title) : htmlspecialchars($link->url);
                                $url = htmlspecialchars($link->url);
                                ?>
                                <a href="<?php echo $url; ?>" target="_blank" rel="noopener noreferrer" class="link-url">
                                    <?php echo $display_title; ?>
                                </a>
                                <div class="pull-right">
                                    <small class="text-muted"><?php echo _dt($link->dateadded); ?></small>
                                </div>
                                <div class="clearfix"></div>
                            </h6>
                        </div>

                        <?php if (!empty($link->description)): ?>
                            <div class="link-description mtop5">
                                <small class="text-muted"><?php echo htmlspecialchars($link->description); ?></small>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($link->title) && $link->title !== $link->url): ?>
                            <div class="link-original-url mtop5">
                                <small class="text-muted">
                                    <i class="fa fa-external-link mright5"></i>
                                    <code><?php echo $url; ?></code>
                                </small>
                            </div>
                        <?php endif; ?>

                        <!-- Action buttons -->
                        <div class="link-actions mtop10">
                            <div class="btn-group btn-group-xs pull-right">
                                <?php if (isset($link->is_demo)): ?>
                                    <span class="label label-info">Demo</span>
                                <?php else: ?>
                                    <button type="button" class="btn btn-default btn-xs"
                                            onclick="edit_link(<?php echo $link->id; ?>);"
                                            data-toggle="tooltip" title="<?php echo _l('edit_link'); ?>">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-xs"
                                            onclick="delete_link(<?php echo $link->id; ?>);"
                                            data-toggle="tooltip" title="<?php echo _l('delete_link'); ?>">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-links-found">
            <p class="text-muted"><?php echo _l('no_links_found'); ?></p>
        </div>
    <?php endif; ?>

            <!-- Add Link Button -->
            <div class="task-links-actions mtop15">
                <button type="button" class="btn btn-info btn-sm" onclick="add_task_link(<?php echo isset($task) ? $task->id : 1; ?>);">
                    <i class="fa fa-plus"></i> <?php echo _l('add_link'); ?>
                </button>
                <button type="button" class="btn btn-default btn-sm" onclick="refresh_task_links();">
                    <i class="fa fa-refresh"></i> <?php echo _l('refresh_links'); ?>
                </button>
            </div>

        </div> <!-- End panel-body -->
    </div> <!-- End panel -->
</div> <!-- End task-links-wrapper -->

<!-- Integrated form for add/edit links -->
<div id="link-form-section" style="display: none;" class="mtop15">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">
                <i class="fa fa-plus mright5"></i>
                <span id="link-form-title">Add Link</span>
                <button type="button" class="close" onclick="hide_link_form();" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </h5>
        </div>
        <div class="panel-body">
            <form id="link-form" method="post">
                <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                <input type="hidden" id="link_id" name="id" value="">
                <input type="hidden" name="relation" value="task">
                <input type="hidden" name="relation_id" value="<?php echo isset($task) ? $task->id : 1; ?>">

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="url"><?php echo _l('link_url'); ?> <span class="text-danger">*</span></label>
                            <input type="url" class="form-control" id="url" name="url" required
                                   placeholder="<?php echo _l('enter_valid_url'); ?>">
                            <small class="help-block"><?php echo _l('enter_valid_url'); ?></small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="title"><?php echo _l('link_title'); ?> <span class="text-muted">(<?php echo _l('optional'); ?>)</span></label>
                            <input type="text" class="form-control" id="title" name="title"
                                   placeholder="<?php echo _l('link_title_placeholder'); ?>">
                            <small class="help-block"><?php echo _l('link_title_help'); ?></small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description"><?php echo _l('link_description'); ?> <span class="text-muted">(<?php echo _l('optional'); ?>)</span></label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                      placeholder="<?php echo _l('link_description_placeholder'); ?>"></textarea>
                            <small class="help-block"><?php echo _l('link_description_help'); ?></small>
                        </div>
                    </div>
                </div>

                <div class="form-group text-right">
                    <button type="button" class="btn btn-default" onclick="hide_link_form();">
                        <?php echo _l('close'); ?>
                    </button>
                    <button type="submit" class="btn btn-primary" id="link-form-submit">
                        <span id="submit-text"><?php echo _l('save'); ?></span>
                        <span id="submit-loading" style="display: none;">
                            <i class="fa fa-spinner fa-spin"></i> <?php echo _l('saving'); ?>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<script>
// Task Links JavaScript Functions
function add_task_link(task_id) {
    // Reset form for adding new link
    $('#link-form')[0].reset();
    $('#link_id').val('');
    $('#link-form-title').text('Add Link');
    $('#submit-text').text('<?php echo _l('save'); ?>');
    $('input[name="relation_id"]').val(task_id);

    // Show the form
    $('#link-form-section').slideDown();
    $('#url').focus();
}

function edit_link(link_id) {
    // Load link data and populate form
    $.get(admin_url + 'links_for_perfex/get_link/' + link_id)
        .done(function(response) {
            if (response.success) {
                var link = response.link;
                $('#link_id').val(link.id);
                $('#url').val(link.url);
                $('#title').val(link.title);
                $('#description').val(link.description);
                $('#link-form-title').text('Edit Link');
                $('#submit-text').text('<?php echo _l('update'); ?>');

                // Show the form
                $('#link-form-section').slideDown();
                $('#url').focus();
            } else {
                alert_float('danger', 'Failed to load link data');
            }
        })
        .fail(function() {
            alert_float('danger', 'Failed to load link data');
        });
}

function delete_link(link_id) {
    if (confirm('<?php echo _l('confirm_delete_link'); ?>')) {
        $.post(admin_url + 'links_for_perfex/delete_link/' + link_id, {}, 'json')
            .done(function(response) {
                // Parse JSON if response is a string
                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        console.error('Failed to parse JSON response:', response);
                        alert_float('danger', 'Invalid server response');
                        return;
                    }
                }

                if (response.success) {
                    alert_float('success', response.message);
                    refresh_task_links();
                } else {
                    alert_float('danger', response.message);
                }
            })
            .fail(function() {
                alert_float('danger', 'Failed to delete link');
            });
    }
}

function refresh_task_links() {
    var task_id = $('input[name="relation_id"]').val();
    if (!task_id) {
        task_id = '<?php echo isset($task) ? $task->id : 1; ?>';
    }

    // Get fresh links data and update the display
    $.get(admin_url + 'links_for_perfex/get_links', {
        relation: 'task',
        relation_id: task_id
    }, 'json')
        .done(function(response) {
            if (response.success) {
                updateLinksDisplay(response.links);
            } else {
                alert_float('warning', 'Could not refresh links');
            }
        })
        .fail(function() {
            alert_float('warning', 'Could not refresh links');
        });
}

function updateLinksDisplay(links) {
    var $linksList = $('.links-list');
    var $noLinksFound = $('.no-links-found');
    var $taskLinksSection = $('.task-links-section');

    if (links.length > 0) {
        // Hide "no links" message
        $noLinksFound.hide();

        // Create links list container if it doesn't exist
        if ($linksList.length === 0) {
            $taskLinksSection.prepend('<div class="links-list"></div>');
            $linksList = $('.links-list');
        }

        // Clear existing links and show
        $linksList.empty().show();

        // Add each link
        links.forEach(function(link) {
            var linkHtml = buildLinkItemHtml(link);
            $linksList.append(linkHtml);
        });
    } else {
        // Show "no links" message and hide links list
        if ($linksList.length > 0) {
            $linksList.hide();
        }
        $noLinksFound.show();
    }
}

function buildLinkItemHtml(link) {
    var displayTitle = link.title || link.url;
    var icon = getIconForUrl(link.url);
    var description = link.description ? '<div class="link-description mtop5"><small class="text-muted">' + escapeHtml(link.description) + '</small></div>' : '';
    var originalUrl = (link.title && link.title !== link.url) ? '<div class="link-original-url mtop5"><small class="text-muted"><i class="fa fa-external-link mright5"></i><code>' + escapeHtml(link.url) + '</code></small></div>' : '';

    return '<div class="panel panel-default link-item-panel mbot10" data-link-id="' + link.id + '">' +
        '<div class="panel-body">' +
        '<div class="link-header">' +
        '<h6 class="no-margin">' +
        '<i class="fa ' + icon + ' text-primary mright5"></i>' +
        '<a href="' + escapeHtml(link.url) + '" target="_blank" rel="noopener noreferrer" class="link-url">' + escapeHtml(displayTitle) + '</a>' +
        '<div class="pull-right"><small class="text-muted">' + link.dateadded + '</small></div>' +
        '<div class="clearfix"></div>' +
        '</h6>' +
        '</div>' +
        description +
        originalUrl +
        '<div class="link-actions mtop10">' +
        '<div class="btn-group btn-group-xs pull-right">' +
        '<button type="button" class="btn btn-default btn-xs" onclick="edit_link(' + link.id + ');" data-toggle="tooltip" title="Edit Link"><i class="fa fa-pencil"></i></button>' +
        '<button type="button" class="btn btn-danger btn-xs" onclick="delete_link(' + link.id + ');" data-toggle="tooltip" title="Delete Link"><i class="fa fa-trash"></i></button>' +
        '</div>' +
        '<div class="clearfix"></div>' +
        '</div>' +
        '</div>' +
        '</div>';
}

function getIconForUrl(url) {
    if (url.includes('github.com')) return 'fa-github';
    if (url.includes('google.com') || url.includes('drive.google.com')) return 'fa-google';
    if (url.includes('figma.com')) return 'fa-paint-brush';
    if (url.includes('youtube.com') || url.includes('youtu.be')) return 'fa-youtube';
    return 'fa-external-link';
}

function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function refresh_links_section() {
    location.reload(); // Simple refresh for now
}

function hide_link_form() {
    $('#link-form-section').slideUp();
    $('#link-form')[0].reset();
    $('#link_id').val('');
}

// Form submission handler
$(document).ready(function() {
    $('#link-form').on('submit', function(e) {
        e.preventDefault();

        var $submitBtn = $('#link-form-submit');
        var $submitText = $('#submit-text');
        var $submitLoading = $('#submit-loading');

        // Show loading state
        $submitBtn.prop('disabled', true);
        $submitText.hide();
        $submitLoading.show();

        // Get fresh CSRF token
        $.get(admin_url + 'links_for_perfex/get_csrf_token', {}, 'json')
            .done(function(tokenResponse) {
                var formData = $('#link-form').serialize();

                // Update CSRF token in form data
                if (tokenResponse.csrf_name && tokenResponse.csrf_hash) {
                    formData = formData.replace(
                        new RegExp('(' + tokenResponse.csrf_name + '=)[^&]*'),
                        '$1' + tokenResponse.csrf_hash
                    );
                }

                var linkId = $('#link_id').val();
                var url = linkId ?
                    admin_url + 'links_for_perfex/edit_link/' + linkId :
                    admin_url + 'links_for_perfex/add_link';

                $.post(url, formData, 'json')
                    .done(function(response) {
                        // Parse JSON if response is a string
                        if (typeof response === 'string') {
                            try {
                                response = JSON.parse(response);
                            } catch (e) {
                                console.error('Failed to parse JSON response:', response);
                                alert_float('danger', 'Invalid server response');
                                return;
                            }
                        }

                        if (response.success) {
                            alert_float('success', response.message);
                            hide_link_form();
                            refresh_task_links();
                        } else {
                            alert_float('danger', response.message);
                        }
                    })
                    .fail(function(xhr) {
                        if (xhr.status === 403) {
                            alert_float('danger', 'Security token expired. Please refresh the page.');
                        } else {
                            alert_float('danger', 'Failed to save link');
                        }
                    })
                    .always(function() {
                        // Reset button state
                        $submitBtn.prop('disabled', false);
                        $submitText.show();
                        $submitLoading.hide();
                    });
            })
            .fail(function() {
                alert_float('danger', 'Failed to get security token');
                // Reset button state
                $submitBtn.prop('disabled', false);
                $submitText.show();
                $submitLoading.hide();
            });
    });

    // Refresh CSRF token when showing the form
    function refresh_csrf_token() {
        $.get(admin_url + 'links_for_perfex/get_csrf_token', {}, 'json')
            .done(function(response) {
                if (response.csrf_name && response.csrf_hash) {
                    // Update only the CSRF token field, don't reset the form
                    var existingToken = $('input[name="' + response.csrf_name + '"]');
                    if (existingToken.length > 0) {
                        existingToken.val(response.csrf_hash);
                    } else {
                        // Add CSRF token if it doesn't exist
                        $('#link-form').prepend('<input type="hidden" name="' + response.csrf_name + '" value="' + response.csrf_hash + '">');
                    }
                    console.log('CSRF token refreshed:', response.csrf_hash);
                }
            })
            .fail(function() {
                console.log('Failed to refresh CSRF token');
            });
    }

    // Override add_task_link to refresh token
    window.add_task_link = function(task_id) {
        // Reset form for adding new link
        $('#link-form')[0].reset();
        $('#link_id').val('');
        $('#link-form-title').text('Add Link');
        $('#submit-text').text('<?php echo _l('save'); ?>');
        $('input[name="relation_id"]').val(task_id);

        // Refresh CSRF token
        refresh_csrf_token();

        // Show the form
        $('#link-form-section').slideDown();
        $('#url').focus();
    };

    // Override edit_link to refresh token
    window.edit_link = function(link_id) {
        // Load link data and populate form
        $.get(admin_url + 'links_for_perfex/get_link/' + link_id, {}, 'json')
            .done(function(response) {
                if (response.success) {
                    var link = response.link;
                    console.log('Full response:', response); // Debug full response
                    console.log('Link object:', link); // Debug link object
                    console.log('Description value:', link.description); // Debug description specifically
                    console.log('Description type:', typeof link.description); // Check type

                    $('#link_id').val(link.id);
                    $('#url').val(link.url || '');
                    $('#title').val(link.title || '');

                    // Force textarea update for description with multiple methods
                    var $description = $('#description');
                    var descValue = link.description || '';
                    console.log('Setting description to:', descValue);

                    $description.val(descValue);
                    $description.text(descValue); // Try text() method too
                    $description.html(descValue); // Try html() method too
                    $description.attr('value', descValue); // Try attr() method too

                    // Trigger multiple events
                    $description.trigger('change');
                    $description.trigger('input');
                    $description.trigger('keyup');

                    console.log('Description field value after setting:', $description.val());

                    $('#link-form-title').text('Edit Link');
                    $('#submit-text').text('<?php echo _l('update'); ?>');

                    // Show the form first
                    $('#link-form-section').slideDown();

                    // Refresh CSRF token after form is shown
                    refresh_csrf_token();

                    // Re-populate fields after CSRF refresh (in case it resets the form)
                    setTimeout(function() {
                        $('#link_id').val(link.id);
                        $('#url').val(link.url || '');
                        $('#title').val(link.title || '');
                        $('#description').val(descValue);
                        console.log('Re-populated description after timeout:', $('#description').val());
                        $('#url').focus();
                    }, 100);
                } else {
                    alert_float('danger', 'Failed to load link data');
                }
            })
            .fail(function() {
                alert_float('danger', 'Failed to load link data');
            });
    };
});
</script>

<!-- Custom CSS for links styling -->
<style>
.link-item {
    padding: 10px;
    border: 1px solid #e6e6e6;
    border-radius: 4px;
    background-color: #f9f9f9;
}

.link-item:hover {
    background-color: #f4f4f4;
}

.link-url {
    font-weight: bold;
    text-decoration: none;
}

.link-url:hover {
    text-decoration: underline;
}

.link-description {
    margin-top: 5px;
    font-style: italic;
}

.link-original-url {
    margin-top: 3px;
    word-break: break-all;
}

.task-links-actions {
    border-top: 1px dashed #ddd;
    padding-top: 10px;
}
</style>