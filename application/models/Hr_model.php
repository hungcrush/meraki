<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Hr_model extends TINY_Model
{   
    public function __construct(){
        parent::__construct();

    }
    
    public function Punch($type)
    {
        $dataInsert = array(
            'user_id'       => $this->tiny->userData['user_id'],
            'ip_address'    => $this->input->ip_address()
        );
        if($type == 'in')
        {
            $dataInsert['in_time'] = time();
            $this->insert($dataInsert);
        }
        else
        {
            //-- makesure this user punched
            $check = $this->checkPunchIn();
            if(!empty($check))
            {
                $dataInsert['out_time'] = time();
                $this->update($check['hr_id'], $dataInsert);
            }
        }

    }

    public function checkPunchIn()
    {
        return $this->get_by(array(
            'user_id' => $this->tiny->userData['user_id'],
            'in_time >' => strtotime(date('d-m-Y 00:00:00', time()))
        ));
    }
    
}