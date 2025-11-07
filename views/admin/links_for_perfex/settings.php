<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h1 class="mtop5"><?php echo $title; ?></h1>
                        <hr />

                        <div class="row">
                            <div class="col-md-8">
                                <div class="alert alert-success">
                                    <strong>Extended Task Manager Module v1.0.0</strong><br>
                                    Module successfully installed and active.
                                </div>

                                <h3>Current Phase: Demo Implementation</h3>
                                <p class="text-muted">The module is currently in Phase 1 with hardcoded demo links to test the UI/UX integration.</p>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="panel panel-info">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">Phase 1: Demo Links</h4>
                                            </div>
                                            <div class="panel-body">
                                                <p>✅ Basic module structure<br>
                                                ✅ Hardcoded demo links<br>
                                                ✅ Task links UI<br>
                                                ✅ Project links UI</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="panel panel-warning">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">Phase 2: Database Integration</h4>
                                            </div>
                                            <div class="panel-body">
                                                <p>⏳ Database migration<br>
                                                ⏳ Links model<br>
                                                ⏳ Data persistence<br>
                                                ⏳ CRUD operations</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="demo-links mtop20">
                                    <h4>Test Demo Pages</h4>
                                    <div class="btn-group">
                                        <a href="<?php echo admin_url('extended_task_manager/task_demo/1'); ?>" class="btn btn-primary">
                                            <i class="fa fa-tasks"></i> Task Demo
                                        </a>
                                        <a href="<?php echo admin_url('extended_task_manager/project_demo/1'); ?>" class="btn btn-info">
                                            <i class="fa fa-folder-open"></i> Project Demo
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h4>Module Information</h4>
                                        <table class="table table-bordered">
                                            <tr>
                                                <td><strong>Version:</strong></td>
                                                <td>1.0.0</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Status:</strong></td>
                                                <td><span class="label label-success">Active</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Phase:</strong></td>
                                                <td>Demo Implementation</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Database:</strong></td>
                                                <td><span class="label label-warning">Phase 2</span></td>
                                            </tr>
                                        </table>

                                        <div class="alert alert-info">
                                            <strong>Next Steps:</strong><br>
                                            1. Test demo functionality<br>
                                            2. Implement database schema<br>
                                            3. Add CRUD operations<br>
                                            4. Integrate with real tasks/projects
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