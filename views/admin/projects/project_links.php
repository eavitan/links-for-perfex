<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
// Get links for this project (database + demo fallback) - moved to top for count badge
$links = links_for_perfex_get_links('project', $project->id);
?>
<!-- Project Links Tab -->
<div class="tab-pane project-tab-content" role="tabpanel" id="project_links">
    <div class="row">
        <div class="col-md-12">
            <div class="project-links-wrapper">
                <h4 class="bold mtop15 mbot15">
                    <a data-toggle="collapse" href="#project-links-collapse" role="button" aria-expanded="true" aria-controls="project-links-collapse" class="text-primary" style="text-decoration: none;">
                        <i class="fa fa-link mright5"></i>
                        <?php echo _l('project_links'); ?>
                        <span id="project-links-count" class="badge badge-primary mleft5"><?php echo count($links); ?></span>
                        <i class="fa fa-chevron-up pull-right mtop5" id="project-collapse-icon"></i>
                    </a>
                </h4>

                <div class="collapse in" id="project-links-collapse">
                    <p class="text-muted mbot20"><?php echo _l('project_links_info'); ?></p>

                    <div class="project-links-section">
                    <?php if (count($links) > 0): ?>
                        <div class="links-list">
                            <?php foreach ($links as $link): ?>
                                <div class="panel panel-default link-item-panel" data-link-id="<?php echo $link->id; ?>">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="link-header">
                                                    <h5 class="no-margin">
                                                        <i class="fa <?php echo links_for_perfex_get_link_icon($link->url); ?> text-primary mright10"></i>
                                                        <?php
                                                        $display_title = !empty($link->title) ? htmlspecialchars($link->title) : htmlspecialchars($link->url);
                                                        $url = htmlspecialchars($link->url);
                                                        ?>
                                                        <a href="<?php echo $url; ?>" target="_blank" rel="noopener noreferrer" class="link-url">
                                                            <strong><?php echo $display_title; ?></strong>
                                                        </a>
                                                        <div class="pull-right">
                                                            <small class="text-muted">
                                                                <?php echo _dt($link->dateadded); ?>
                                                            </small>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </h5>
                                                </div>

                                                <?php if (!empty($link->description)): ?>
                                                    <div class="link-description mtop10">
                                                        <p class="text-muted"><?php echo htmlspecialchars($link->description); ?></p>
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
                                                    <div class="btn-group btn-group-xs">
                                                        <?php if (isset($link->is_demo)): ?>
                                                            <span class="label label-info">Demo</span>
                                                        <?php else: ?>
                                                            <button type="button" class="btn btn-default"
                                                                    onclick="edit_project_link(<?php echo $link->id; ?>);"
                                                                    data-toggle="tooltip" title="<?php echo _l('edit_link'); ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-danger"
                                                                    onclick="delete_project_link(<?php echo $link->id; ?>);"
                                                                    data-toggle="tooltip" title="<?php echo _l('delete_link'); ?>">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-links-found text-center">
                            <div class="empty-state mtop30 mbot30">
                                <i class="fa fa-link fa-4x text-muted mbot15"></i>
                                <h3 class="text-muted"><?php echo _l('no_links_found'); ?></h3>
                                <p class="text-muted">Add relevant links and resources to keep everything organized.</p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Add Link Button -->
                    <div class="project-links-actions mtop20">
                        <?php if (has_permission('projects', '', 'edit') || $project->addedfrom == get_staff_user_id()): ?>
                            <button type="button" class="btn btn-primary"
                                    onclick="add_project_link(<?php echo $project->id; ?>);">
                                <i class="fa fa-plus mright5"></i>
                                <?php echo _l('add_project_link'); ?>
                            </button>
                        <?php endif; ?>

                        <button type="button" class="btn btn-default" onclick="refresh_project_links();">
                            <i class="fa fa-refresh mright5"></i>
                            <?php echo _l('refresh_links'); ?>
                        </button>
                    </div>

                    <!-- Demo Information -->
                    <div class="alert alert-info mtop20">
                        <strong><i class="fa fa-info-circle mright5"></i>Phase 2 Active:</strong>
                        Database functionality is now active! Demo links show fallback behavior when no real links exist.
                        Add your first real link using the button above.
                    </div>
                </div> <!-- End collapse -->
            </div>
        </div>
    </div>
</div>

<!-- Modal for add/edit links -->
<div class="modal fade" id="project-link-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <!-- Content will be loaded via AJAX -->
        </div>
    </div>
</div>

<script>
// Project Links JavaScript Functions
$(document).ready(function() {
    // Handle collapse/expand icon changes
    $('#project-links-collapse').on('show.bs.collapse', function () {
        $('#project-collapse-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    });

    $('#project-links-collapse').on('hide.bs.collapse', function () {
        $('#project-collapse-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });
});

function updateProjectLinksCount(count) {
    $('#project-links-count').text(count);
}
function add_project_link(project_id) {
    $('#project-link-modal .modal-content').html('<div class="modal-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
    $('#project-link-modal').modal('show');

    $.get(admin_url + 'links_for_perfex/add_link?relation=project&relation_id=' + project_id)
        .done(function(data) {
            $('#project-link-modal .modal-content').html(data);
        })
        .fail(function() {
            $('#project-link-modal .modal-content').html('<div class="modal-body"><div class="alert alert-danger">Failed to load form</div></div>');
        });
}

function edit_project_link(link_id) {
    $('#project-link-modal .modal-content').html('<div class="modal-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
    $('#project-link-modal').modal('show');

    $.get(admin_url + 'links_for_perfex/edit_link/' + link_id)
        .done(function(data) {
            $('#project-link-modal .modal-content').html(data);
        })
        .fail(function() {
            $('#project-link-modal .modal-content').html('<div class="modal-body"><div class="alert alert-danger">Failed to load form</div></div>');
        });
}

function delete_project_link(link_id) {
    if (confirm('<?php echo _l('confirm_delete_link'); ?>')) {
        $.post(admin_url + 'links_for_perfex/delete_link/' + link_id)
            .done(function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    refresh_project_links();
                } else {
                    alert_float('danger', response.message);
                }
            })
            .fail(function() {
                alert_float('danger', 'Failed to delete link');
            });
    }
}

function refresh_project_links() {
    location.reload(); // Simple refresh for now
}

// Override global refresh function for this context
function refresh_links_section() {
    refresh_project_links();
}
</script>

<!-- Custom CSS for project links styling -->
<style>
.project-links-wrapper .link-item-panel {
    border-left: 4px solid #3498db;
    transition: all 0.3s ease;
}

.project-links-wrapper .link-item-panel:hover {
    border-left-color: #2980b9;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.project-links-wrapper .link-url {
    font-size: 16px;
    text-decoration: none;
    color: #3498db;
}

.project-links-wrapper .link-url:hover {
    text-decoration: underline;
    color: #2980b9;
}

.project-links-wrapper .link-description {
    font-size: 14px;
    line-height: 1.5;
}

.project-links-wrapper .link-original-url {
    word-break: break-all;
    font-size: 12px;
}

.project-links-wrapper .link-original-url code {
    background-color: #f8f9fa;
    color: #6c757d;
    font-size: 11px;
}

.project-links-wrapper .empty-state {
    padding: 40px 20px;
}

.project-links-wrapper .project-links-actions {
    border-top: 1px dashed #ddd;
    padding-top: 20px;
}

.project-links-wrapper .link-actions {
    border-top: 1px solid #eee;
    padding-top: 10px;
}
</style>