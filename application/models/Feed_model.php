<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Feed_model extends TINY_Model
{   
    public function __construct(){
        parent::__construct();

    }
    /*
        Save new feed to database
        @params: $data @array
    */
    public function Save($data){
        $feed = array(
            'content'       => $this->lib->escape($data['content']),
            'full_name'     => $this->tiny->userData['full_name'],
            'user_id'       => $this->tiny->userData['user_id'],
            'created_at'    => time(),
            'policy'        => 1
        );

        $this->insert($feed);

        return 'OK';
    }

    public function Load()
    {
        $user_id = $this->tiny->userData['user_id'];

        $this->order_by('feed_id', 'DESC');
        $data = $this->get_many_by('user_id', $user_id);

        $this->fileds_output_process($data, array('time_since'));

        return $data;
    }
    
}