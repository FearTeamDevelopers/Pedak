<?php

use Admin\Etc\Controller;
use THCFrame\Events\Events as Event;

/**
 * 
 */
class Admin_Controller_Chat extends Controller
{

    /**
     * @before _secured, _admin
     */
    public function index()
    {
        $view = $this->getActionView();
        $topics = App_Model_ChatTopic::all();
        $view->set('topics', $topics);
    }

    /**
     * @before _secured, _admin
     */
    public function delete($id)
    {
        $this->willRenderActionView = false;
        $this->willRenderLayoutView = false;

        if ($this->checkToken()) {
            $topic = App_Model_ChatTopic::first(array('id = ?' => (int)$id));

            if (NULL === $topic) {
                echo self::ERROR_MESSAGE_2;
            } else {
                if ($topic->delete()) {
                    Event::fire('admin.log', array('success', 'Topic id: ' . $id));
                    echo 'success';
                } else {
                    Event::fire('admin.log', array('fail', 'Topic id: ' . $id));
                    echo self::ERROR_MESSAGE_1;
                }
            }
        } else {
            echo self::ERROR_MESSAGE_1;
        }
    }
}