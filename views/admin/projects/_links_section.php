<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Project Links Section -->
<div class="panel_s">
    <div class="panel-body">
        <h4 class="bold mbot15"><i class="fa fa-link"></i> <?php echo _l('project_links'); ?></h4>
        <p class="mbot15 text-muted"><?php echo _l('project_links_info'); ?></p>

        <?php
        // Get demo links for this project
        $demo_links = links_for_perfex_get_demo_links('project', isset($project) ? $project->id : 1);
        ?>

        <div class="project-links-section">
            <?php if (count($demo_links) > 0): ?>
                <div class="links-list">
                    <?php foreach ($demo_links as $link): ?>
                        <div class="link-item row mbot15" data-link-id="<?php echo $link->id; ?>">
                            <div class="col-md-12">
                                <div class="link-header">
                                    <i class="fa <?php echo links_for_perfex_get_link_icon($link->url); ?> text-primary mright10"></i>
                                    <?php
                                    $display_title = !empty($link->title) ? htmlspecialchars($link->title) : htmlspecialchars($link->url);
                                    $url = htmlspecialchars($link->url);
                                    ?>
                                    <a href="<?php echo $url; ?>" target="_blank" rel="noopener noreferrer" class="link-url">
                                        <strong><?php echo $display_title; ?></strong>
                                    </a>
                                    <div class="pull-right">
                                        <small class="text-muted"><?php echo _dt($link->dateadded); ?></small>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>

                                <?php if (!empty($link->description)): ?>
                                    <div class="link-description text-muted mtop5 mleft25">
                                        <?php echo htmlspecialchars($link->description); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($link->title) && $link->title !== $link->url): ?>
                                    <div class="link-original-url mtop5 mleft25">
                                        <small class="text-muted">
                                            <i class="fa fa-external-link mright5"></i>
                                            <?php echo $url; ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <hr class="no-margin" />
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-links-found text-center">
                    <i class="fa fa-link fa-3x text-muted mbot15"></i>
                    <p class="text-muted"><?php echo _l('no_links_found'); ?></p>
                </div>
            <?php endif; ?>

            <!-- Add Link Button (Demo) -->
            <div class="project-links-actions mtop20 text-center">
                <button type="button" class="btn btn-info" onclick="alert('Add Project Link functionality will be implemented in Phase 3');">
                    <i class="fa fa-plus"></i> <?php echo _l('add_project_link'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS for project links styling -->
<style>
.project-links-section .link-item {
    padding: 15px 0;
}

.project-links-section .link-url {
    font-size: 16px;
    text-decoration: none;
}

.project-links-section .link-url:hover {
    text-decoration: underline;
}

.project-links-section .link-description {
    font-size: 14px;
    line-height: 1.4;
}

.project-links-section .link-original-url {
    word-break: break-all;
    font-size: 12px;
}

.project-links-section .no-links-found {
    padding: 40px 0;
}

.project-links-actions {
    border-top: 1px dashed #ddd;
    padding-top: 15px;
    margin-top: 15px;
}
</style>