<?php

use App\Etc\Controller as Controller;
use THCFrame\Request\RequestMethods;

/**
 * Description of App_Controller_Chat
 *
 * @author Tomy
 */
class App_Controller_Chat extends Controller
{
    
    /**
     * 
     * @param type $key
     * @return boolean
     */
    private function _checkUrlKey($key)
    {
        $status = App_Model_ChatTopic::first(array('urlKey = ?' => $key));

        if ($status === null) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * @before _secured
     */
    public function index()
    {
        $view = $this->getActionView();
        $host = RequestMethods::server('HTTP_HOST');
        
        $topics = App_Model_ChatTopic::fetchAllActive();
        
        $canonical = 'http://' . $host . '/kecarna';

        $this->getLayoutView()->set('metatitle', 'Peďák - Kecárna')
                ->set('canonical', $canonical)
                ->set('activemenu', 'chat');
        
        $view->set('topics', $topics)
                ->set('submstoken', $this->mutliSubmissionProtectionToken());
        
        if(RequestMethods::post('submitAddTopic')){
            if($this->checkCSRFToken() !== true && 
                    $this->checkMutliSubmissionProtectionToken(RequestMethods::post('submstoken')) !== true){
                self::redirect('/kecarna');
            }
            
            $errors = array();
            $urlKey = $this->_createUrlKey(RequestMethods::post('title'));

            if (!$this->_checkUrlKey($urlKey)) {
                $errors['title'] = array('Toto téma již existuje');
            }
            
            $topic = new App_Model_ChatTopic(array(
                'userId' => $this->getUser()->getId(),
                'status' => 0,
                'title' => RequestMethods::post('title'),
                'urlKey' => $urlKey
            ));
            
            if(empty($errors) && $topic->validate()){
                $topic->save();
                
                $view->successMessage('Téma'.self::SUCCESS_MESSAGE_1);
                self::redirect('/kecarna');
            }else{
                $view->set('topic', $topic)
                        ->set('submstoken', $this->revalidateMutliSubmissionProtectionToken())
                        ->set('errors', $errors + $topic->getErrors());
            }
        }
    }
    
    /**
     * @before _secured
     */
    public function topicDetail($urlKey)
    {
        $view = $this->getActionView();
        $host = RequestMethods::server('HTTP_HOST');

        $topic = App_Model_ChatTopic::first(array('urlKey = ?' => $urlKey, 'status = ?' => true));
        
        if($topic === null){
            $view->warningMessage(self::ERROR_MESSAGE_2);
            self::redirect('/kecarna');
        }
        
        $canonical = 'http://' . $host . '/kecarna/'.$urlKey;

        $this->getLayoutView()->set('metatitle', 'Peďák - Kecárna')
                ->set('canonical', $canonical)
                ->set('activemenu', 'chat');

        $messages = App_Model_Chat::all(array(
                    'active = ?' => true,
                    'topicId = ?' => $topic->getId(),
                    'reply = ?' => 0
                        ), array('*'), array('created' => 'asc'));

        $view->set('messages', $messages)
                ->set('topic', $topic);

        if (RequestMethods::post('sendMessage')) {
            $chat = new App_Model_Chat(array(
                'author' => $this->getUser()->getWholeName(),
                'topicId' => $topic->getId(),
                'title' => RequestMethods::post('title'),
                'body' => RequestMethods::post('body'),
                'reply' => RequestMethods::post('reply', 0)
            ));

            if ($chat->validate()) {
                $chat->save();
                self::redirect('/kecarna/'.$urlKey);
            }
            $view->set('errors', $chat->getErrors());
        }
    }
}
