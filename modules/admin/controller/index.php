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
        
        $latestNews = array();
        
        $view->set('attendance', $attend)
                ->set('latestnews', $latestNews);
    }

}
