<?php

use Admin\Etc\Controller;

/**
 * 
 */
class Admin_Controller_Index extends Controller
{

    /**
     * @before _secured, _admin
     */
    public function index()
    {
        $view = $this->getActionView();
        
        
        $attend = App_Model_Training::fetchPercentAttendance();
        
        $latestNews = App_Model_News::all(
                array('active = ?' => true), 
                array('title', 'shortBody', 'created'), 
                array('created' => 'desc'), 5);
        
        $view->set('attendance', $attend)
                ->set('latestnews', $latestNews);
    }

}
