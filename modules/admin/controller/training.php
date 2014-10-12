<?php

use Admin\Etc\Controller;
use THCFrame\Request\RequestMethods;
use THCFrame\Events\Events as Event;

/**
 * Description of Admin_Controller_Training
 *
 * @author Tomy
 */
class Admin_Controller_Training extends Controller
{

    /**
     * @before _secured, _admin
     */
    public function index()
    {
        $view = $this->getActionView();
        $trainings = App_Model_Training::all(array(), array('*'), array('startDate' => 'asc'));
        $view->set('trainings', $trainings);
    }

    /**
     * @before _secured, _admin
     */
    public function add()
    {
        $view = $this->getActionView();

        $view->set('submstoken', $this->mutliSubmissionProtectionToken());

        if (RequestMethods::post('submitAddTraining')) {
            if ($this->checkToken() !== true &&
                    $this->checkMutliSubmissionProtectionToken(RequestMethods::post('submstoken')) !== true) {
                self::redirect('/admin/training/');
            }

            $errors = array();
            $trainingCount = RequestMethods::post('generateNext', 0);

            if ($trainingCount == 0) {
                $training = new App_Model_Training(array(
                    'title' => RequestMethods::post('title'),
                    'location' => RequestMethods::post('location'),
                    'startDate' => RequestMethods::post('date'),
                    'startTime' => RequestMethods::post('time')
                ));
                if ($training->validate()) {
                    $id = $training->save();

                    Event::fire('admin.log', array('success', 'Training id: ' . $id));
                } else {
                    Event::fire('admin.log', array('fail'));
                    $errors = $errors + $training->getErrors();
                }
            } else {
                $training = new App_Model_Training(array(
                    'title' => RequestMethods::post('title'),
                    'location' => RequestMethods::post('location'),
                    'startDate' => RequestMethods::post('date'),
                    'startTime' => RequestMethods::post('time')
                ));

                if ($training->validate()) {
                    $id = $training->save();

                    Event::fire('admin.log', array('success', 'Training id: ' . $id));
                } else {
                    Event::fire('admin.log', array('fail'));
                    $errors = $errors + $training->getErrors();
                }

                for ($i = 1; $i <= $trainingCount; $i++) {
                    $date = DateTime::createFromFormat('Y-m-d', RequestMethods::post('date'));
                    $date->modify('+' . $i . ' week');

                    $training = new App_Model_Training(array(
                        'title' => RequestMethods::post('title'),
                        'location' => RequestMethods::post('location'),
                        'startDate' => $date->format('Y-m-d'),
                        'startTime' => RequestMethods::post('time')
                    ));

                    if ($training->validate()) {
                        $id = $training->save();

                        Event::fire('admin.log', array('success', 'Training id: ' . $id));
                    } else {
                        Event::fire('admin.log', array('fail'));
                        $errors = $errors + $training->getErrors();
                    }
                }
            }

            if (empty($errors)) {
                $view->successMessage('Trainings' . self::SUCCESS_MESSAGE_1);
                self::redirect('/admin/training/');
            } else {
                $view->set('errors', $training->getErrors())
                        ->set('submstoken', $this->revalidateMutliSubmissionProtectionToken())
                        ->set('training', $training);
            }
        }
    }

    /**
     * @before _secured, _admin
     */
    public function edit($id)
    {
        $view = $this->getActionView();

        $training = App_Model_Training::first(array('id = ?' => (int) $id));
        
        if (NULL === $training) {
            $view->warningMessage(self::ERROR_MESSAGE_2);
            self::redirect('/admin/training/');
        }
        $view->set('training', $training);

        if (RequestMethods::post('submitEditTraining')) {
            if($this->checkToken() !== true){
                self::redirect('/admin/training/');
            }

            $training->title = RequestMethods::post('title');
            $training->location = RequestMethods::post('location');
            $training->startDate = RequestMethods::post('date');
            $training->startTime = RequestMethods::post('time');
            $training->active = RequestMethods::post('active');

            if ($training->validate()) {
                $training->save();

                Event::fire('admin.log', array('success', 'Training id: ' . $training->getId()));
                $view->successMessage(self::SUCCESS_MESSAGE_2);
                self::redirect('/admin/training/');
            } else {
                Event::fire('admin.log', array('fail', 'Training id: ' . $training->getId()));
                $view->set('errors', $training->getErrors());
            }
        }
    }

    /**
     * @before _secured, _admin
     */
    public function detail($id)
    {
        $view = $this->getActionView();

        $training = App_Model_Training::first(array('id = ?' => (int) $id));
        
        if (NULL === $training) {
            $view->warningMessage(self::ERROR_MESSAGE_2);
            self::redirect('/admin/training/');
        }
        
        $training->attendance = App_Model_Attendance::fetchAttendanceByTrainingId($training->getId());
        
        $view->set('training', $training);
    }

    /**
     * @before _secured, _admin
     */
    public function delete($id)
    {
        $this->willRenderActionView = false;
        $this->willRenderLayoutView = false;

        if ($this->checkToken()) {
            $training = App_Model_Training::first(array('id = ?' => (int)$id));

            if (NULL === $training) {
                echo self::ERROR_MESSAGE_2;
            } else {
                if ($training->delete()) {
                    Event::fire('admin.log', array('success', 'Training id: ' . $id));
                    echo 'success';
                } else {
                    Event::fire('admin.log', array('fail', 'Training id: ' . $id));
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
    public function attendance()
    {
        $view = $this->getActionView();
        
        $attend = App_Model_Training::fetchPercentAttendance();
        $view->set('attendance', $attend);
    }

    /**
     * @before _secured, _admin
     */
    public function massAction()
    {
        $view = $this->getActionView();
        $errors = array();

        if (RequestMethods::post('performTrainingAction')) {
            if($this->checkToken() !== true){
                self::redirect('/admin/training/');
            }
            
            $ids = RequestMethods::post('trainingids');
            $action = RequestMethods::post('action');

            switch ($action) {
                case 'delete':
                    $trainings = App_Model_Training::all(array(
                                'id IN ?' => $ids
                    ));
                    if (NULL !== $trainings) {
                        foreach ($trainings as $training) {
                            if (!$training->delete()) {
                                $errors[] = 'An error occured while deleting ' . $training->getTitle();
                            }
                        }
                    }

                    if (empty($errors)) {
                        Event::fire('admin.log', array('delete success', 'Training ids: ' . join(',', $ids)));
                        $view->successMessage(self::SUCCESS_MESSAGE_6);
                    } else {
                        Event::fire('admin.log', array('delete fail', 'Error count:' . count($errors)));
                        $message = join(PHP_EOL, $errors);
                        $view->longFlashMessage($message);
                    }

                    self::redirect('/admin/training/');

                    break;
                case 'activate':
                    $trainings = App_Model_Training::all(array(
                                'id IN ?' => $ids
                    ));
                    if (NULL !== $trainings) {
                        foreach ($trainings as $training) {
                            $training->active = true;

                            if ($training->validate()) {
                                $training->save();
                            } else {
                                $errors[] = "Training id {$training->getId()} - {$training->getTitle()} errors: "
                                        . join(', ', $training->getErrors());
                            }
                        }
                    }

                    if (empty($errors)) {
                        Event::fire('admin.log', array('activate success', 'Training ids: ' . join(',', $ids)));
                        $view->successMessage(self::SUCCESS_MESSAGE_4);
                    } else {
                        Event::fire('admin.log', array('activate fail', 'Error count:' . count($errors)));
                        $message = join(PHP_EOL, $errors);
                        $view->longFlashMessage($message);
                    }

                    self::redirect('/admin/training/');

                    break;
                case 'deactivate':
                    $trainings = App_Model_Training::all(array(
                                'id IN ?' => $ids
                    ));
                    if (NULL !== $trainings) {
                        foreach ($trainings as $training) {
                            $training->active = false;

                            if ($training->validate()) {
                                $training->save();
                            } else {
                                $errors[] = "Training id {$training->getId()} - {$training->getTitle()} errors: "
                                        . join(', ', $training->getErrors());
                            }
                        }
                    }

                    if (empty($errors)) {
                        Event::fire('admin.log', array('deactivate success', 'Training ids: ' . join(',', $ids)));
                        $view->successMessage(self::SUCCESS_MESSAGE_5);
                    } else {
                        Event::fire('admin.log', array('deactivate fail', 'Error count:' . count($errors)));
                        $message = join(PHP_EOL, $errors);
                        $view->longFlashMessage($message);
                    }

                    self::redirect('/admin/training/');
                    break;
                default:
                    self::redirect('/admin/training/');
                    break;
            }
        }
    }

}
