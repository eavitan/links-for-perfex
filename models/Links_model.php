<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Links_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get links by relation (task or project)
     * @param string $relation 'task' or 'project'
     * @param int $relation_id ID of the task or project
     * @return array Array of link objects
     */
    public function get_links($relation, $relation_id)
    {
        $this->db->where('relation', $relation);
        $this->db->where('relation_id', $relation_id);
        $this->db->order_by('dateadded', 'DESC');

        return $this->db->get(db_prefix() . 'links_for_perfex')->result();
    }

    /**
     * Get a single link by ID
     * @param int $id Link ID
     * @return object|null Link object or null if not found
     */
    public function get($id)
    {
        $this->db->where('id', $id);
        $link = $this->db->get(db_prefix() . 'links_for_perfex')->row();

        return $link;
    }

    /**
     * Add a new link
     * @param array $data Link data
     * @return int|false Link ID if successful, false if failed
     */
    public function add($data)
    {
        // Validate required fields
        if (empty($data['url']) || empty($data['relation']) || empty($data['relation_id'])) {
            return false;
        }

        // Prepare data
        $insert_data = [
            'url' => $data['url'],
            'title' => isset($data['title']) ? $data['title'] : null,
            'description' => isset($data['description']) ? $data['description'] : null,
            'relation' => $data['relation'],
            'relation_id' => (int) $data['relation_id'],
            'dateadded' => date('Y-m-d H:i:s'),
            'addedfrom' => get_staff_user_id()
        ];

        // Validate URL format
        if (!filter_var($insert_data['url'], FILTER_VALIDATE_URL)) {
            return false;
        }

        // Validate relation type
        if (!in_array($insert_data['relation'], ['task', 'project'])) {
            return false;
        }

        $this->db->insert(db_prefix() . 'links_for_perfex', $insert_data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            // Log activity
            $relation_name = $this->get_relation_name($data['relation'], $data['relation_id']);
            log_activity('Link Added [' . $insert_data['url'] . '] to ' . ucfirst($data['relation']) . ': ' . $relation_name);

            // Hook for other modules
            hooks()->do_action('link_created', $insert_id);

            return $insert_id;
        }

        return false;
    }

    /**
     * Update an existing link
     * @param int $id Link ID
     * @param array $data Updated link data
     * @return boolean Success status
     */
    public function update($id, $data)
    {
        // Get existing link to validate ownership/permissions
        $existing_link = $this->get($id);
        if (!$existing_link) {
            return false;
        }

        // Check if user has permission to edit
        if (!$this->can_user_modify_link($existing_link)) {
            return false;
        }

        // Prepare update data
        $update_data = [];

        if (isset($data['url'])) {
            if (!filter_var($data['url'], FILTER_VALIDATE_URL)) {
                return false;
            }
            $update_data['url'] = $data['url'];
        }

        if (isset($data['title'])) {
            $update_data['title'] = $data['title'] ?: null;
        }

        if (isset($data['description'])) {
            $update_data['description'] = $data['description'] ?: null;
        }

        if (empty($update_data)) {
            return false;
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'links_for_perfex', $update_data);

        $affected_rows = $this->db->affected_rows();

        if ($affected_rows > 0) {
            // Log activity
            $relation_name = $this->get_relation_name($existing_link->relation, $existing_link->relation_id);
            log_activity('Link Updated [' . $existing_link->url . '] in ' . ucfirst($existing_link->relation) . ': ' . $relation_name);

            // Hook for other modules
            hooks()->do_action('link_updated', $id);

            return true;
        }

        return false;
    }

    /**
     * Delete a link
     * @param int $id Link ID
     * @return boolean Success status
     */
    public function delete($id)
    {
        // Get existing link to validate ownership/permissions
        $existing_link = $this->get($id);
        if (!$existing_link) {
            return false;
        }

        // Check if user has permission to delete
        if (!$this->can_user_modify_link($existing_link)) {
            return false;
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'links_for_perfex');

        $affected_rows = $this->db->affected_rows();

        if ($affected_rows > 0) {
            // Log activity
            $relation_name = $this->get_relation_name($existing_link->relation, $existing_link->relation_id);
            log_activity('Link Deleted [' . $existing_link->url . '] from ' . ucfirst($existing_link->relation) . ': ' . $relation_name);

            // Hook for other modules
            hooks()->do_action('link_deleted', $id);

            return true;
        }

        return false;
    }

    /**
     * Get total count of links for a relation
     * @param string $relation 'task' or 'project'
     * @param int $relation_id ID of the task or project
     * @return int Count of links
     */
    public function get_links_count($relation, $relation_id)
    {
        $this->db->where('relation', $relation);
        $this->db->where('relation_id', $relation_id);

        return $this->db->count_all_results(db_prefix() . 'links_for_perfex');
    }

    /**
     * Check if current user can modify a link
     * @param object $link Link object
     * @return boolean Permission status
     */
    private function can_user_modify_link($link)
    {
        // Admin can modify any link
        if (is_admin()) {
            return true;
        }

        $current_user_id = get_staff_user_id();

        // User can modify their own links
        if ($link->addedfrom == $current_user_id) {
            return true;
        }

        // Check relation-specific permissions
        if ($link->relation == 'task') {
            // Can modify if user can edit tasks and has access to this task
            if (has_permission('tasks', '', 'edit')) {
                $CI = &get_instance();
                $CI->load->model('tasks_model');
                $task = $CI->tasks_model->get($link->relation_id);

                if ($task && ($task->current_user_is_assigned || $task->current_user_is_creator)) {
                    return true;
                }
            }
        } elseif ($link->relation == 'project') {
            // Can modify if user can edit projects and has access to this project
            if (has_permission('projects', '', 'edit')) {
                $CI = &get_instance();
                $CI->load->model('projects_model');
                $project = $CI->projects_model->get($link->relation_id);

                if ($project) {
                    // Check if user is project member or creator
                    $is_member = $CI->projects_model->is_member($link->relation_id, $current_user_id);
                    if ($is_member || $project->addedfrom == $current_user_id) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get the name of the related task or project
     * @param string $relation 'task' or 'project'
     * @param int $relation_id ID of the task or project
     * @return string Name of the related item
     */
    private function get_relation_name($relation, $relation_id)
    {
        $CI = &get_instance();

        if ($relation == 'task') {
            $CI->load->model('tasks_model');
            $task = $CI->tasks_model->get($relation_id);
            return $task ? $task->name : 'Unknown Task';
        } elseif ($relation == 'project') {
            $CI->load->model('projects_model');
            $project = $CI->projects_model->get($relation_id);
            return $project ? $project->name : 'Unknown Project';
        }

        return 'Unknown';
    }

    /**
     * Search links across all relations
     * @param string $search_term Search term
     * @param array $filters Additional filters
     * @return array Array of link objects
     */
    public function search_links($search_term = '', $filters = [])
    {
        if (!empty($search_term)) {
            $this->db->group_start();
            $this->db->like('url', $search_term);
            $this->db->or_like('title', $search_term);
            $this->db->or_like('description', $search_term);
            $this->db->group_end();
        }

        if (!empty($filters['relation'])) {
            $this->db->where('relation', $filters['relation']);
        }

        if (!empty($filters['relation_id'])) {
            $this->db->where('relation_id', $filters['relation_id']);
        }

        if (!empty($filters['addedfrom'])) {
            $this->db->where('addedfrom', $filters['addedfrom']);
        }

        $this->db->order_by('dateadded', 'DESC');

        return $this->db->get(db_prefix() . 'links_for_perfex')->result();
    }
}