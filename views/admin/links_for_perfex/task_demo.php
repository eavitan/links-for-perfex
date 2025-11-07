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

                                <div class="task-demo-header">
                                    <h3 class="bold"><?php echo htmlspecialchars($task->name); ?></h3>
                                    <div class="task-info mbot15">
                                        <span class="label label-info">Task ID: <?php echo $task->id; ?></span>
                                        <span class="label label-success">Demo Mode</span>
                                    </div>
                                </div>

                                <div class="task-demo-content">
                                    <h4><?php echo _l('task_view_description'); ?></h4>
                                    <p class="text-muted"><?php echo htmlspecialchars($task->description); ?></p>

                                    <!-- This is where our links section will be displayed -->
                                    <?php $this->load->view('extended_task_manager/admin/tasks/_links_section', ['task' => $task]); ?>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h4>Demo Information</h4>
                                        <p class="text-muted">This page demonstrates the Extended Task Manager module's link functionality for tasks.</p>

                                        <div class="alert alert-info">
                                            <strong>Phase 1:</strong> Hardcoded demo links showing UI/UX design
                                        </div>

                                        <div class="demo-actions">
                                            <a href="<?php echo admin_url('extended_task_manager/project_demo'); ?>"
                                               class="btn btn-primary btn-block">
                                                <i class="fa fa-folder-open"></i> View Project Demo
                                            </a>

                                            <a href="<?php echo admin_url('extended_task_manager/settings'); ?>"
                                               class="btn btn-default btn-block mtop10">
                                                <i class="fa fa-cog"></i> Module Settings
                                            </a>
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