<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Links_for_perfex extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tasks_model');
        $this->load->model('projects_model');
        $this->load->model('links_for_perfex/links_model');
    }

    /**
     * Demo page to show task links functionality
     */
    public function task_demo($task_id = 1)
    {
        if (!has_permission('tasks', '', 'view')) {
            access_denied('Extended Task Manager');
        }

        // Get task data (use demo task ID if no real task exists)
        $task = $this->tasks_model->get($task_id);

        if (!$task) {
            // Create a demo task object for demonstration
            $task = (object) [
                'id' => $task_id,
                'name' => 'Demo Task - Extended Task Manager',
                'description' => 'This is a demonstration of the Extended Task Manager links functionality.',
                'priority' => 2,
                'status' => 1,
                'startdate' => date('Y-m-d'),
                'duedate' => date('Y-m-d', strtotime('+7 days')),
                'dateadded' => date('Y-m-d H:i:s'),
                'rel_type' => 'project',
                'rel_id' => 1,
                'assignees' => [],
                'comments' => [],
                'checklist_items' => []
            ];
        }

        $data['task'] = $task;
        $data['title'] = 'Extended Task Manager - Demo';

        $this->load->view('admin/links_for_perfex/task_demo', $data);
    }

    /**
     * Demo page to show project links functionality
     */
    public function project_demo($project_id = 1)
    {
        if (!has_permission('projects', '', 'view')) {
            access_denied('Extended Task Manager');
        }

        // Get project data (use demo project ID if no real project exists)
        $project = $this->projects_model->get($project_id);

        if (!$project) {
            // Create a demo project object for demonstration
            $project = (object) [
                'id' => $project_id,
                'name' => 'Demo Project - Extended Task Manager',
                'description' => 'This is a demonstration of the Extended Task Manager project links functionality.',
                'status' => 2,
                'start_date' => date('Y-m-d'),
                'deadline' => date('Y-m-d', strtotime('+30 days')),
                'date_created' => date('Y-m-d H:i:s'),
                'clientid' => 1
            ];
        }

        $data['project'] = $project;
        $data['title'] = 'Extended Task Manager - Project Demo';

        $this->load->view('admin/links_for_perfex/project_demo', $data);
    }

    /**
     * Settings page for the module
     */
    public function settings()
    {
        if (!is_admin()) {
            access_denied('Extended Task Manager Settings');
        }

        $data['title'] = 'Extended Task Manager Settings';
        $this->load->view('admin/links_for_perfex/settings', $data);
    }

    /**
     * Add a new link (AJAX)
     */
    public function add_link()
    {
        if ($this->input->post()) {
            header('Content-Type: application/json');
            $data = $this->input->post();

            // Validate required fields
            if (empty($data['url']) || empty($data['relation']) || empty($data['relation_id'])) {
                echo json_encode(['success' => false, 'message' => _l('invalid_url')]);
                return;
            }

            $link_id = $this->links_model->add($data);

            if ($link_id) {
                echo json_encode([
                    'success' => true,
                    'message' => _l('link_added_successfully'),
                    'link_id' => $link_id
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => _l('invalid_url')
                ]);
            }
        } else {
            // Return the form
            $relation = $this->input->get('relation');
            $relation_id = $this->input->get('relation_id');

            $data['relation'] = $relation;
            $data['relation_id'] = $relation_id;

            $this->load->view('links_for_perfex/admin/forms/link_form', $data);
        }
    }

    /**
     * Edit an existing link (AJAX)
     */
    public function edit_link($link_id)
    {
        if ($this->input->post()) {
            header('Content-Type: application/json');
            $data = $this->input->post();

            $success = $this->links_model->update($link_id, $data);

            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => _l('link_updated_successfully')
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => _l('link_not_found')
                ]);
            }
        } else {
            // Return the form with existing data
            $link = $this->links_model->get($link_id);

            if (!$link) {
                show_404();
            }

            $data['link'] = $link;
            $this->load->view('links_for_perfex/admin/forms/link_form', $data);
        }
    }

    /**
     * Delete a link (AJAX)
     */
    public function delete_link($link_id)
    {
        header('Content-Type: application/json');
        $success = $this->links_model->delete($link_id);

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => _l('link_deleted_successfully')
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => _l('link_not_found')
            ]);
        }
    }

    /**
     * Get links for a specific relation (AJAX)
     */
    public function get_links()
    {
        header('Content-Type: application/json');
        $relation = $this->input->get('relation');
        $relation_id = $this->input->get('relation_id');

        if ($relation && $relation_id) {
            $links = $this->links_model->get_links($relation, $relation_id);
            echo json_encode(['success' => true, 'links' => $links]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
        }
    }

    /**
     * Get a single link by ID (AJAX)
     */
    public function get_link($link_id)
    {
        header('Content-Type: application/json');
        $link = $this->links_model->get($link_id);

        if ($link) {
            echo json_encode(['success' => true, 'link' => $link]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('link_not_found')]);
        }
    }

    /**
     * Get fresh CSRF token (AJAX)
     */
    public function get_csrf_token()
    {
        header('Content-Type: application/json');
        echo json_encode([
            'csrf_name' => $this->security->get_csrf_token_name(),
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
    }
}