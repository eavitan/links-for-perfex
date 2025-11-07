<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h1 class="mtop5"><?php echo $title; ?></h1>
                                <hr />

                                <div class="project-demo-header">
                                    <h3 class="bold"><?php echo htmlspecialchars($project->name); ?></h3>
                                    <div class="project-info mbot15">
                                        <span class="label label-info">Project ID: <?php echo $project->id; ?></span>
                                        <span class="label label-success">Demo Mode</span>
                                    </div>
                                </div>

                                <div class="project-demo-content">
                                    <h4>Project Description</h4>
                                    <p class="text-muted"><?php echo htmlspecialchars($project->description); ?></p>

                                    <!-- This is where our project links section will be displayed -->
                                    <?php $this->load->view('extended_task_manager/admin/projects/_links_section', ['project' => $project]); ?>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h4>Demo Information</h4>
                                        <p class="text-muted">This page demonstrates the Extended Task Manager module's link functionality for projects.</p>

                                        <div class="alert alert-info">
                                            <strong>Phase 1:</strong> Hardcoded demo links showing UI/UX design
                                        </div>

                                        <div class="demo-actions">
                                            <a href="<?php echo admin_url('extended_task_manager/task_demo'); ?>"
                                               class="btn btn-primary btn-block">
                                                <i class="fa fa-tasks"></i> View Task Demo
                                            </a>

                                            <a href="<?php echo admin_url('extended_task_manager/settings'); ?>"
                                               class="btn btn-default btn-block mtop10">
                                                <i class="fa fa-cog"></i> Module Settings
                                            </a>
                                        </div>

                                        <div class="mtop20">
                                            <h5>Future Integration Points:</h5>
                                            <ul class="list-unstyled text-muted">
                                                <li><i class="fa fa-check text-success"></i> Project overview tab</li>
                                                <li><i class="fa fa-check text-success"></i> Project sidebar</li>
                                                <li><i class="fa fa-clock-o text-warning"></i> Project activity feed</li>
                                                <li><i class="fa fa-clock-o text-warning"></i> Project reports</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>