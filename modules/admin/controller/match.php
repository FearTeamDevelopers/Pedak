<?php

use Admin\Etc\Controller;
use THCFrame\Request\RequestMethods;
use THCFrame\Events\Events as Event;

/**
 * Description of Admin_Controller_Match
 *
 * @author Tomy
 */
class Admin_Controller_Match extends Controller
{

    /**
     * @before _secured, _admin
     */
    public function index()
    {
        $view = $this->getActionView();
        $matches = App_Model_Match::all();
        $view->set('matches', $matches);
    }

    /**
     * @before _secured, _admin
     */
    public function add()
    {
        $view = $this->getActionView();
        
        $view->set('submstoken', $this->mutliSubmissionProtectionToken());
        
        if (RequestMethods::post('submitAddMatch')) {
            if($this->checkToken() !== true && 
                    $this->checkMutliSubmissionProtectionToken(RequestMethods::post('submstoken')) !== true){
                self::redirect('/admin/match/');
            }

            $match = new App_Model_Match(array(
                'home' => RequestMethods::post('home'),
                'away' => RequestMethods::post('host'),
                'startDate' => RequestMethods::post('startdate'),
                'startTime' => RequestMethods::post('starttime'),
                'hall' => RequestMethods::post('hall'),
                'scoreHome' => RequestMethods::post('scoreHome', -1),
                'scoreAway' => RequestMethods::post('scoreHost', -1),
                'season' => RequestMethods::post('season'),
                'team' => RequestMethods::post('team'),
                'report' => RequestMethods::post('report')
            ));

            if ($match->validate()) {
                $id = $match->save();

                Event::fire('admin.log', array('success', 'Match id: ' . $id));
                $view->successMessage('Match'.self::SUCCESS_MESSAGE_1);
                self::redirect('/admin/match/');
            } else {
                Event::fire('admin.log', array('fail'));
                $view->set('errors', $match->getErrors())
                        ->set('submstoken', $this->revalidateMutliSubmissionProtectionToken())
                        ->set('match', $match);
            }
        }
    }

    /**
     * @before _secured, _admin
     */
    public function edit($id)
    {
        $view = $this->getActionView();

        $match = App_Model_Match::first(array('id = ?' => (int) $id));
        
        if (NULL === $match) {
            $view->warningMessage(self::ERROR_MESSAGE_2);
            self::redirect('/admin/match/');
        }
        $view->set('match', $match);

        if (RequestMethods::post('submitEditMatch')) {
            if($this->checkToken() !== true){
                self::redirect('/admin/match/');
            }
            
            $match->home = RequestMethods::post('home');
            $match->away = RequestMethods::post('host');
            $match->startDate = RequestMethods::post('startdate');
            $match->startTime = RequestMethods::post('starttime');
            $match->hall = RequestMethods::post('hall');
            $match->scoreHome = RequestMethods::post('scoreHome');
            $match->scoreAway = RequestMethods::post('scoreHost');
            $match->season = RequestMethods::post('season');
            $match->team = RequestMethods::post('team');
            $match->report = RequestMethods::post('report');
            $match->active = RequestMethods::post('active');

            if ($match->validate()) {
                $match->save();

                Event::fire('admin.log', array('success', 'Match id: ' . $match->getId()));
                $view->successMessage(self::SUCCESS_MESSAGE_2);
                self::redirect('/admin/match/');
            } else {
                Event::fire('admin.log', array('fail', 'Match id: ' . $match->getId()));
                $view->set('errors', $match->getErrors());
            }
        }
    }

    /**
     * @before _secured, _admin
     */
    public function delete($id)
    {
        $this->willRenderActionView = false;
        $this->willRenderLayoutView = false;

        if ($this->checkToken()) {
            $match = App_Model_Match::first(array('id = ?' => (int)$id));

            if (NULL === $match) {
                echo self::ERROR_MESSAGE_2;
            } else {
                if ($match->delete()) {
                    Event::fire('admin.log', array('success', 'Match id: ' . $id));
                    echo 'success';
                } else {
                    Event::fire('admin.log', array('fail', 'Match id: ' . $id));
                    echo self::ERROR_MESSAGE_1;
                }
            }
        } else {
            echo self::ERROR_MESSAGE_1;
        }
    }

    /**
     * @before _secured, _admin
     */
    public function massAction()
    {
        $view = $this->getActionView();
        $errors = array();

        if (RequestMethods::post('performMatchAction')) {
            if($this->checkToken() !== true){
                self::redirect('/admin/match/');
            }
            
            $ids = RequestMethods::post('matchids');
            $action = RequestMethods::post('action');

            switch ($action) {
                case 'delete':
                    $matches = App_Model_Match::all(array(
                                'id IN ?' => $ids
                    ));
                    if (NULL !== $matches) {
                        foreach ($matches as $match) {
                            if (!$match->delete()) {
                                $errors[] = 'An error occured while deleting ' . $match->getTitle();
                            }
                        }
                    }

                    if (empty($errors)) {
                        Event::fire('admin.log', array('delete success', 'Match ids: ' . join(',', $ids)));
                        $view->successMessage(self::SUCCESS_MESSAGE_6);
                    } else {
                        Event::fire('admin.log', array('delete fail', 'Error count:' . count($errors)));
                        $message = join(PHP_EOL, $errors);
                        $view->longFlashMessage($message);
                    }

                    self::redirect('/admin/match/');

                    break;
                case 'activate':
                    $matches = App_Model_Match::all(array(
                                'id IN ?' => $ids
                    ));
                    
                    if (NULL !== $matches) {
                        foreach ($matches as $match) {
                            $match->active = true;

                            if ($match->validate()) {
                                $match->save();
                            } else {
                                $errors[] = "Match id {$match->getId()} - {$match->getTitle()} errors: "
                                        . join(', ', $match->getErrors());
                            }
                        }
                    }

                    if (empty($errors)) {
                        Event::fire('admin.log', array('activate success', 'Match ids: ' . join(',', $ids)));
                        $view->successMessage(self::SUCCESS_MESSAGE_4);
                    } else {
                        Event::fire('admin.log', array('activate fail', 'Error count:' . count($errors)));
                        $message = join(PHP_EOL, $errors);
                        $view->longFlashMessage($message);
                    }

                    self::redirect('/admin/match/');

                    break;
                case 'deactivate':
                    $matches = App_Model_Match::all(array(
                                'id IN ?' => $ids
                    ));
                    
                    if (NULL !== $matches) {
                        foreach ($matches as $match) {
                            $match->active = false;

                            if ($match->validate()) {
                                $match->save();
                            } else {
                                $errors[] = "Match id {$match->getId()} - {$match->getTitle()} errors: "
                                        . join(', ', $match->getErrors());
                            }
                        }
                    }

                    if (empty($errors)) {
                        Event::fire('admin.log', array('deactivate success', 'Match ids: ' . join(',', $ids)));
                        $view->successMessage(self::SUCCESS_MESSAGE_5);
                    } else {
                        Event::fire('admin.log', array('deactivate fail', 'Error count:' . count($errors)));
                        $message = join(PHP_EOL, $errors);
                        $view->longFlashMessage($message);
                    }

                    self::redirect('/admin/match/');
                    break;
                default:
                    self::redirect('/admin/match/');
                    break;
            }
        }
    }

}
