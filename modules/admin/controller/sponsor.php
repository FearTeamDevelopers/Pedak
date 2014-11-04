<?php

use Admin\Etc\Controller;
use THCFrame\Request\RequestMethods;
use THCFrame\Events\Events as Event;
use THCFrame\Filesystem\FileManager;

/**
 * Description of Admin_Controller_Sponsor
 *
 * @author Tomy
 */
class Admin_Controller_Sponsor extends Controller
{

    /**
     * @before _secured, _admin
     */
    public function index()
    {
        $view = $this->getActionView();
        $sponsors = App_Model_Sponsor::all();
        $view->set('sponsors', $sponsors);
    }

    /**
     * @before _secured, _admin
     */
    public function add()
    {
        $view = $this->getActionView();
        
        $view->set('submstoken', $this->mutliSubmissionProtectionToken());
        
        if (RequestMethods::post('submitAddSponsor')) {
            if($this->checkCSRFToken() !== true && 
                    $this->checkMutliSubmissionProtectionToken(RequestMethods::post('submstoken')) !== true){
                self::redirect('/admin/sponsor/');
            }
            $errors = array();

            $fileManager = new FileManager(array(
                'thumbWidth' => $this->loadConfigFromDb('thumb_width'),
                'thumbHeight' => $this->loadConfigFromDb('thumb_height'),
                'thumbResizeBy' => $this->loadConfigFromDb('thumb_resizeby'),
                'maxImageWidth' => $this->loadConfigFromDb('photo_maxwidth'),
                'maxImageHeight' => $this->loadConfigFromDb('photo_maxheight')
            ));

            $fileErrors = $fileManager->upload('logo', 'sponsors', time().'_', false)->getUploadErrors();
            $files = $fileManager->getUploadedFiles();

            if (!empty($files)) {
                foreach ($files as $i => $file) {
                    if ($file instanceof \THCFrame\Filesystem\Image) {
                        $partner = new App_Model_Sponsor(array(
                            'title' => RequestMethods::post('title'),
                            'web' => RequestMethods::post('web'),
                            'logo' => trim($file->getFilename(), '.')
                        ));

                        if ($partner->validate()) {
                            $id = $partner->save();

                            Event::fire('admin.log', array('success', 'Partner id: ' . $id));
                            $view->successMessage('Partner' . self::SUCCESS_MESSAGE_1);
                            self::redirect('/admin/partner/');
                        } else {
                            Event::fire('admin.log', array('fail'));
                            $view->set('errors', $partner->getErrors())
                                    ->set('submstoken', $this->revalidateMutliSubmissionProtectionToken())
                                    ->set('partner', $partner);
                        }

                        break;
                    }
                }
            } else {
                $errors['logo'] = $fileErrors;
                Event::fire('admin.log', array('fail'));
                $view->set('errors', $errors)
                        ->set('submstoken', $this->revalidateMutliSubmissionProtectionToken());
            }
        }
    }

    /**
     * @before _secured, _admin
     */
    public function edit($id)
    {
        $view = $this->getActionView();

        $sponsor = App_Model_Sponsor::first(array('id = ?' => (int) $id));
        
        if (NULL === $sponsor) {
            $view->warningMessage(self::ERROR_MESSAGE_2);
            self::redirect('/admin/sponsor/');
        }
        $view->set('sponsor', $sponsor);

        if (RequestMethods::post('submitEditSponsor')) {
            if($this->checkCSRFToken() !== true){
                self::redirect('/admin/sponsor/');
            }
            $errors = array();

            if ($sponsor->logo == '') {
                $fileManager = new FileManager(array(
                    'thumbWidth' => $this->loadConfigFromDb('thumb_width'),
                    'thumbHeight' => $this->loadConfigFromDb('thumb_height'),
                    'thumbResizeBy' => $this->loadConfigFromDb('thumb_resizeby'),
                    'maxImageWidth' => $this->loadConfigFromDb('photo_maxwidth'),
                    'maxImageHeight' => $this->loadConfigFromDb('photo_maxheight')
                ));

                $fileErrors = $fileManager->upload('logo', 'sponsors', time().'_', false)->getUploadErrors();
                $files = $fileManager->getUploadedFiles();

                if (!empty($files)) {
                    foreach ($files as $i => $filemain) {
                        if ($filemain instanceof \THCFrame\Filesystem\Image) {
                            $file = $filemain;
                            break;
                        }
                    }

                    $logo = trim($file->getFilename(), '.');
                }else{
                    $errors['logo'] = $fileErrors;
                }
            } else {
                $logo = $sponsor->logo;
            }

            $sponsor->title = RequestMethods::post('title');
            $sponsor->logo = $logo;
            $sponsor->web = RequestMethods::post('web');

            if (empty($errors) && $sponsor->validate()) {
                $sponsor->save();

                Event::fire('admin.log', array('success', 'Sponsor id: ' . $sponsor->getId()));
                $view->successMessage(self::SUCCESS_MESSAGE_2);
                self::redirect('/admin/sponsor/');
            } else {
                Event::fire('admin.log', array('fail', 'Sponsor id: ' . $sponsor->getId()));
                $view->set('errors', $errors + $sponsor->getErrors());
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

        if ($this->checkCSRFToken()) {
            $sponsor = App_Model_Sponsor::first(array('id = ?' => (int)$id));

            if (NULL === $sponsor) {
                echo self::ERROR_MESSAGE_2;
            } else {
                $path = $sponsor->getUnlinkLogoPath();
                
                if ($sponsor->delete()) {
                    @unlink($path);
                    Event::fire('admin.log', array('success', 'Sponsor id: ' . $id));
                    echo 'success';
                } else {
                    Event::fire('admin.log', array('fail', 'Sponsor id: ' . $id));
                    echo self::ERROR_MESSAGE_1;
                }
            }
        } else {
            echo self::ERROR_MESSAGE_1;
        }
    }

    /**
     * @before _secured, _admin
     * @param type $id
     */
    public function deleteLogo($id)
    {
        $this->willRenderActionView = false;
        $this->willRenderLayoutView = false;

        if ($this->checkCSRFToken()) {
            $sponsor = App_Model_Sponsor::first(array('id = ?' => (int) $id));

            if (NULL === $sponsor) {
                echo self::ERROR_MESSAGE_2;
            } else {
                $path = $sponsor->getUnlinkLogoPath();
                $sponsor->logo = '';

                if ($sponsor->validate()) {
                    @unlink($path);
                    $sponsor->save();

                    Event::fire('admin.log', array('success', 'Sponsor Id: ' . $id));
                    echo 'success';
                } else {
                    Event::fire('admin.log', array('fail', 'Sponsor Id: ' . $id));
                    echo self::ERROR_MESSAGE_1;
                }
            }
        } else {
            echo self::ERROR_MESSAGE_1;
        }
    }
}
