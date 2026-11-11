<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
// The project's own links, then its tasks' (Eyal, 2026-11-11). The cards are the task popup's
// markup, so slick_ui draws both with the same rules.
$CI = &get_instance();
$CI->load->model('links_for_perfex/links_model');
$links = $CI->links_model->get_project_all_links($project->id);

// The filters list only the tasks and milestones that have links, milestones in the project's order
$filter_tasks      = [];
$filter_milestones = [];
foreach ($links as $link) {
    if (isset($link->task_name)) {
        $filter_tasks[$link->relation_id]      = $link->task_name;
        $filter_milestones[$link->milestone] = [(int) $link->milestone_order, $link->milestone_name ?: _l('links_filter_no_milestone')];
    }
}
asort($filter_tasks);
asort($filter_milestones);
?>
<!-- Project Links Tab -->
<div class="tab-pane project-tab-content" role="tabpanel" id="project_links">
    <div class="project-links-wrapper">
        <h4 class="project-links-heading bold mtop15 mbot15">
            <?php echo _l('links'); ?>
            <span id="project-links-count" class="badge mleft5"><?php echo count($links); ?></span>
        </h4>

        <?php if ($filter_tasks): ?>
            <div class="project-links-filters">
                <select id="project-links-milestone" class="selectpicker" data-width="240px" data-live-search="true">
                    <option value=""><?php echo _l('links_filter_all_milestones'); ?></option>
                    <?php foreach ($filter_milestones as $id => $milestone): ?>
                        <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($milestone[1]); ?></option>
                    <?php endforeach; ?>
                </select>
                <select id="project-links-task" class="selectpicker" data-width="240px" data-live-search="true">
                    <option value=""><?php echo _l('links_filter_all_tasks'); ?></option>
                    <?php foreach ($filter_tasks as $id => $name): ?>
                        <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <div class="project-links-list">
            <?php foreach ($links as $link): ?>
                <?php
                $display_title = !empty($link->title) ? htmlspecialchars($link->title) : htmlspecialchars($link->url);
                $url = htmlspecialchars($link->url);
                ?>
                <div class="panel panel-default link-item-panel mbot10" data-link-id="<?php echo $link->id; ?>"
                     data-task="<?php echo isset($link->task_name) ? (int) $link->relation_id : ''; ?>"
                     data-milestone="<?php echo isset($link->task_name) ? $link->milestone : ''; ?>" style="border: 0px; box-shadow: 2px 2px 7px #afafafb0;">
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

        <p class="no-links-found text-muted"<?php echo count($links) ? ' hidden' : ''; ?>><?php echo _l('no_links_found'); ?></p>

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

// The filters: both apply together; the project's own links show only with neither set.
// jQuery loads in the page's footer, after this tab: bind once the page has parsed.
document.addEventListener('DOMContentLoaded', function () {
    $('#project-links-milestone, #project-links-task').on('change', function () {
        var milestone = $('#project-links-milestone').val();
        var task = $('#project-links-task').val();
        var shown = 0;

        $('#project_links .link-item-panel').each(function () {
            var on = (milestone === '' || $(this).attr('data-milestone') === milestone)
                && (task === '' || $(this).attr('data-task') === task);
            $(this).toggle(on);
            shown += on ? 1 : 0;
        });

        updateProjectLinksCount(shown);
        $('#project_links .no-links-found').prop('hidden', shown > 0);
    });
});
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
.project-links-filters { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 14px; }
.project-links-wrapper .link-url { font-weight: bold; text-decoration: none; }
.project-links-wrapper .link-original-url { word-break: break-all; }
.project-links-actions { border-top: 1px dashed #ddd; padding-top: 10px; }
</style>
