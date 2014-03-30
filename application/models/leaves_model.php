<?php
/*
 * This file is part of lms.
 *
 * lms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * lms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 */

class Leaves_model extends CI_Model {

    /**
     * Default constructor
     */
    public function __construct() {
        
    }

    /**
     * Get the list of all leave requests or one leave
     * @param int $id Id of the leave request
     * @return array list of records
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_leaves($id = 0) {
        if ($id === 0) {
            $query = $this->db->get('leaves');
            return $query->result_array();
        }
        $query = $this->db->get_where('leaves', array('id' => $id));
        return $query->row_array();
    }

    /**
     * Get the the list of leaves requested by a givane employee
     * @param int $id ID of the employee
     * @return array list of records
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_user_leaves($id) {
        $query = $this->db->get_where('leaves', array('employee' => $id));
        return $query->result_array();
    }
    
    /**
     * Create a leave request
     * @return int id of the leave request into the db
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function set_leaves() {
        $data = array(
            'startdate' => $this->input->post('startdate'),
            'startdatetype' => $this->input->post('startdatetype'),
            'enddate' => $this->input->post('enddate'),
            'enddatetype' => $this->input->post('enddatetype'),
            'duration' => $this->input->post('duration'),
            'type' => $this->input->post('type'),
            'cause' => $this->input->post('cause'),
            'status' => $this->input->post('status'),
            'employee' => $this->session->userdata('id')
        );
        $this->db->insert('leaves', $data);
        return $this->db->insert_id();
    }

    /**
     * Accept a leave request
     * @param int $id leave request identifier
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function accept_leave($id) {
        $data = array(
            'status' => 3
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('leaves', $data);
    }

    /**
     * Reject a leave request
     * @param int $id leave request identifier
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function reject_leave($id) {
        $data = array(
            'status' => 4
        );
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('leaves', $data);
    }
    
    /**
     * Delete a leave from the database
     * @param int $id leave request identifier
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete_leave($id) {
        return $this->db->delete('leaves', array('id' => $id));
    }
    
    /**
     * All leave request of the user
     * @param type $id
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function individual($id) {
        $this->db->where('employee', $id);
        $this->db->order_by('startdate', 'desc');
        $this->db->limit(70);
        $events = $this->db->get('leaves')->result();

        $jsonevents = array();
        foreach ($events as $entry) {
            $jsonevents[] = array(
                'id' => $entry->id,
                'title' => 'Leave',
                'start' => $entry->startdate,
                'allDay' => false,
                'end' => $entry->enddate
            );
        }
        return json_encode($jsonevents);
    }

    /**
     * All users having the same manager
     * @param type $id
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function team($id) {
        $this->db->join('users', 'users.id = leaves.employee');
        $this->db->where('users.manager', $id);
        $this->db->order_by('startdate', 'desc');
        $this->db->limit(70);
        $events = $this->db->get('leaves')->result();

        $jsonevents = array();
        foreach ($events as $entry) {
            $jsonevents[] = array(
                'id' => $entry->id,
                'title' => 'Leave',
                'start' => $entry->startdate,
                'allDay' => false,
                'end' => $entry->enddate
            );
        }
        return json_encode($jsonevents);
    }

}