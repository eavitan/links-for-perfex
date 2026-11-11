<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
// The project's own links, then its tasks' (Eyal, 2026-11-11). The cards are the task popup's
// markup, so slick_ui draws both with the same rules.
$CI = &get_instance();
$CI->load->model('links_for_perfex/links_model');
$links = $CI->links_model->get_project_all_links($project->id);
?>
<!-- Project Links Tab -->
<div class="tab-pane project-tab-content" role="tabpanel" id="project_links">
    <div class="project-links-wrapper">
        <h4 class="project-links-heading bold mtop15 mbot15">
            <?php echo _l('links'); ?>
            <span id="project-links-count" class="badge mleft5"><?php echo count($links); ?></span>
        </h4>

        <div class="project-links-list">
            <?php foreach ($links as $link): ?>
                <?php
                $display_title = !empty($link->title) ? htmlspecialchars($link->title) : htmlspecialchars($link->url);
                $url = htmlspecialchars($link->url);
                ?>
                <div class="panel panel-default link-item-panel mbot10" data-link-id="<?php echo $link->id; ?>" style="border: 0px; box-shadow: 2px 2px 7px #afafafb0;">
                    <div class="panel-body" style="padding: 16px 11px 9px 9px;">
                        <div class="link-header">
                            <h6 class="no-margin">
                                <i class="fa <?php echo links_for_perfex_get_link_icon($link->url); ?> text-primary mright5"></i>
                                <a href="<?php echo $url; ?>" target="_blank" rel="noopener noreferrer" class="link-url" style="font-size: 142%;">
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
                                    <code style="background: transparent; color: #9b9b9b; font-size: 10px;"><?php echo $url; ?></code>
                                </small>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($link->task_name)): ?>
                            <div class="link-task mtop5">
                                <i class="fa-regular fa-circle-check mright5"></i>
                                <a href="<?php echo admin_url('tasks/view/' . $link->relation_id); ?>" onclick="init_task_modal(<?php echo (int) $link->relation_id; ?>); return false;"><?php echo htmlspecialchars($link->task_name); ?></a>
                            </div>
                        <?php endif; ?>

                        <div class="link-actions mtop10">
                            <div class="btn-group btn-group-xs pull-right" style="border-radius: 15px; overflow: hidden; border: 0px; box-shadow: 1px 1px 5px #b8b8b8db;">
                                <button type="button" class="btn btn-default btn-xs" style="border: 0px; padding: 3px 14px;"
                                        onclick="edit_project_link(<?php echo $link->id; ?>);"
                                        data-toggle="tooltip" title="<?php echo _l('edit_link'); ?>">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-xs" style="border: 0px; padding: 3px 14px;"
                                        onclick="delete_project_link(<?php echo $link->id; ?>);"
                                        data-toggle="tooltip" title="<?php echo _l('delete_link'); ?>">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (count($links) === 0): ?>
            <p class="no-links-found text-muted"><?php echo _l('no_links_found'); ?></p>
        <?php endif; ?>

        <div class="project-links-actions mtop15">
            <?php if (has_permission('projects', '', 'edit') || $project->addedfrom == get_staff_user_id()): ?>
                <button type="button" class="btn btn-info btn-sm" onclick="add_project_link(<?php echo $project->id; ?>);">
                    <i class="fa fa-plus"></i> <?php echo _l('add_project_link'); ?>
                </button>
            <?php endif; ?>
            <button type="button" class="btn btn-default btn-sm" onclick="refresh_project_links();">
                <i class="fa fa-refresh"></i> <?php echo _l('refresh_links'); ?>
            </button>
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

<style>
.project-links-wrapper .link-url { font-weight: bold; text-decoration: none; }
.project-links-wrapper .link-original-url { word-break: break-all; }
.project-links-actions { border-top: 1px dashed #ddd; padding-top: 10px; }
</style>
