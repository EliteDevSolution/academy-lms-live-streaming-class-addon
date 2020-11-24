<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Liveclass_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function update_live_class($course_id) {
        if (!empty($this->input->post('live_class_schedule_date')) && !empty($this->input->post('live_class_schedule_time')) && !empty($this->input->post('zoom_meeting_id')) && !empty($this->input->post('zoom_meeting_password'))) {
            $data['date']                  = strtotime($this->input->post('live_class_schedule_date'));
            $data['time']                  = strtotime($this->input->post('live_class_schedule_time'));
            $zoom_meeting_id               = $this->input->post('zoom_meeting_id');
            $trimmed_meeting_id            = preg_replace('/\s+/', '', $zoom_meeting_id);
            $data['zoom_meeting_id']       = str_replace("-", "", $trimmed_meeting_id);
            $data['zoom_meeting_password'] = $this->input->post('zoom_meeting_password');
            $data['note_to_students']      = $this->input->post('note_to_students');
            $data['course_id']             = $course_id;
            $previous_data = $this->db->get_where('live_class', array('course_id' => $course_id))->num_rows();
            if ($previous_data > 0) {
                $this->db->where('course_id', $course_id);
                $this->db->update('live_class', $data);
            }else{
                $this->db->insert('live_class', $data);
            }
        }
    }

    public function update_settings() {
        if (empty($this->input->post('zoom_api_key')) || empty($this->input->post('zoom_secret_key'))) {
            $this->session->set_flashdata('error_message', get_phrase('nothing_can_be_empty'));
            redirect(site_url('addons/liveclass/settings'), 'refresh');
        }
        $data['value'] = $this->input->post('zoom_api_key');
        $this->db->where('key', 'zoom_api_key');
        $this->db->update('settings', $data);

        $data['value'] = $this->input->post('zoom_secret_key');
        $this->db->where('key', 'zoom_secret_key');
        $this->db->update('settings', $data);

        $this->session->set_flashdata('flash_message', get_phrase('zoom_account_has_been_updated'));
        redirect(site_url('addons/liveclass/settings'), 'refresh');
    }

    public function get_live_class_details($course_id = "") {
        return $this->db->get_where('live_class', array('course_id' => $course_id))->row_array();
    }
}
