<?php

use Admin\Etc\Controller;
use THCFrame\Registry\Registry;
use THCFrame\Request\RequestMethods;
use THCFrame\Events\Events as Event;
use THCFrame\Filesystem\FileManager;
use THCFrame\Security\PasswordManager;

/**
 * Description of Admin_Controller_User
 *
 * @author Tomy
 */
class Admin_Controller_User extends Controller
{

    /**
     * 
     */
    public function login()
    {
        $this->willRenderLayoutView = false;
        $view = $this->getActionView();

        if (RequestMethods::post('submitLogin')) {

            $email = RequestMethods::post('email');
            $password = RequestMethods::post('password');
            $error = false;

            if (empty($email)) {
                $view->set('email_error', 'Email not provided');
                $error = true;
            }

            if (empty($password)) {
                $view->set('password_error', 'Password not provided');
                $error = true;
            }

            if (!$error) {
                try {
                    $security = Registry::get('security');
                    $status = $security->authenticate($email, $password);

                    if ($status === true) {
                        self::redirect('/admin/');
                    } else {
                        $view->set('account_error', 'Email and/or password is wrong');
                    }
                } catch (\Exception $e) {
                    if (ENV == 'dev') {
                        $view->set('account_error', $e->getMessage());
                    } else {
                        $view->set('account_error', 'Email and/or password is wrong');
                    }
                }
            }
        }
    }

    /**
     * 
     */
    public function logout()
    {
        $security = Registry::get('security');
        $security->logout();
        self::redirect('/admin');
    }

    /**
     * @before _secured, _admin
     */
    public function index()
    {
        $view = $this->getActionView();
        $security = Registry::get('security');

        $superAdmin = $security->isGranted('role_superadmin');

        $users = App_Model_User::all(
                    array('role <> ?' => 'role_superadmin'), 
                    array('id', 'firstname', 'lastname', 'email', 'role', 
                        'active', 'created', 'team', 'photoThumb'), 
                    array('id' => 'asc')
        );

        $view->set('users', $users)
                ->set('superadmin', $superAdmin);
    }

    /**
     * @before _secured, _admin
     */
    public function add()
    {
        $view = $this->getActionView();
        
        $view->set('submstoken', $this->mutliSubmissionProtectionToken());
        
        if (RequestMethods::post('submitAddUser')) {
            if($this->checkCSRFToken() !== true && 
                    $this->checkMutliSubmissionProtectionToken(RequestMethods::post('submstoken')) !== true){
                self::redirect('/admin/user/');
            }
            $errors = array();
            
            $fileManager = new FileManager(array(
                'thumbWidth' => $this->loadConfigFromDb('thumb_width'),
                'thumbHeight' => $this->loadConfigFromDb('thumb_height'),
                'thumbResizeBy' => $this->loadConfigFromDb('thumb_resizeby'),
                'maxImageWidth' => $this->loadConfigFromDb('photo_maxwidth'),
                'maxImageHeight' => $this->loadConfigFromDb('photo_maxheight')
            ));

            $fileErrors = $fileManager->upload('photo', 'team', time().'_')->getUploadErrors();
            $files = $fileManager->getUploadedFiles();

            if (!empty($files)) {
                foreach ($files as $i => $file) {
                    if ($file instanceof \THCFrame\Filesystem\Image) {
                        $photoMain = trim($file->getFilename(), '.');
                        $photoThumb = trim($file->getThumbname(), '.');
                        break;
                    }
                }
            }else{
                $errors['photo'] = $fileErrors;
            }

            if (RequestMethods::post('password') !== RequestMethods::post('password2')) {
                $errors['password2'] = array('Paswords doesnt match');
            }

            $email = App_Model_User::first(array('email = ?' => RequestMethods::post('email')), array('email'));

            if ($email) {
                $errors['email'] = array('Email is already used');
            }

            $salt = PasswordManager::createSalt();
            $hash = PasswordManager::hashPassword(RequestMethods::post('password'), $salt);

            $user = new App_Model_User(array(
                'firstname' => RequestMethods::post('firstname'),
                'lastname' => RequestMethods::post('lastname'),
                'email' => RequestMethods::post('email'),
                'password' => $hash,
                'salt' => $salt,
                'role' => RequestMethods::post('role', 'role_member'),
                'dob' => RequestMethods::post('dob'),
                'playerNum' => RequestMethods::post('playerNum'),
                'cfbuPersonalNum' => RequestMethods::post('cfbuPersonalNum'),
                'team' => RequestMethods::post('team'),
                'nickname' => RequestMethods::post('nickname'),
                'photoMain' => $photoMain,
                'photoThumb' => $photoThumb,
                'position' => RequestMethods::post('position'),
                'grip' => RequestMethods::post('grip'),
                'other' => RequestMethods::post('other')
            ));

            if (empty($errors) && $user->validate()) {
                $id = $user->save();

                Event::fire('admin.log', array('success', 'User id: ' . $id));
                $view->successMessage('User'.self::SUCCESS_MESSAGE_1);
                self::redirect('/admin/user/');
            } else {
                Event::fire('admin.log', array('fail'));
                $view->set('errors', $errors + $user->getErrors())
                        ->set('submstoken', $this->revalidateMutliSubmissionProtectionToken())
                        ->set('user', $user);
            }
        }
    }

    /**
     * @before _secured, _admin
     */
    public function updateProfile()
    {
        $view = $this->getActionView();
        $loggedUser = $this->getUser();

        $user = App_Model_User::first(
                array('active = ?' => true, 'id = ?' => $loggedUser->getId()));

        if (NULL === $user) {
            $view->warningMessage(self::ERROR_MESSAGE_2);
            self::redirect('/admin/user/');
        }
        $view->set('user', $user);

        if (RequestMethods::post('submitUpdateProfile')) {
            if($this->checkCSRFToken() !== true){
                self::redirect('/admin/user/');
            }
            $errors = array();

            if ($user->photoMain == '') {
                $fileManager = new FileManager(array(
                    'thumbWidth' => $this->loadConfigFromDb('thumb_width'),
                    'thumbHeight' => $this->loadConfigFromDb('thumb_height'),
                    'thumbResizeBy' => $this->loadConfigFromDb('thumb_resizeby'),
                    'maxImageWidth' => $this->loadConfigFromDb('photo_maxwidth'),
                    'maxImageHeight' => $this->loadConfigFromDb('photo_maxheight')
                ));

                $fileErrors = $fileManager->upload('photo', 'team', time().'_')->getUploadErrors();
                $files = $fileManager->getUploadedFiles();

                if (!empty($files)) {
                    foreach ($files as $i => $file) {
                        if ($file instanceof \THCFrame\Filesystem\Image) {
                            $photoMain = trim($file->getFilename(), '.');
                            $photoThumb = trim($file->getThumbname(), '.');
                            break;
                        }
                    }
                }else{
                    $errors['photo'] = $fileErrors;
                }
            } else {
                $photoMain = $user->photoMain;
                $photoThumb = $user->photoThumb;
            }

            if (RequestMethods::post('password') !== RequestMethods::post('password2')) {
                $errors['password2'] = array('Paswords doesnt match');
            }

            if (RequestMethods::post('email') != $user->email) {
                $email = App_Model_User::first(
                                array('email = ?' => RequestMethods::post('email', $user->email)), 
                                array('email')
                );

                if ($email) {
                    $errors['email'] = array('Email is already used');
                }
            }

            $pass = RequestMethods::post('password');

            if ($pass === null || $pass == '') {
                $salt = $user->getSalt();
                $hash = $user->getPassword();
            } else {
                $salt = PasswordManager::createSalt();
                $hash = PasswordManager::hashPassword($pass, $salt);
            }

            $user->firstname = RequestMethods::post('firstname');
            $user->lastname = RequestMethods::post('lastname');
            $user->email = RequestMethods::post('email');
            $user->password = $hash;
            $user->salt = $salt;
            $user->role = $user->getRole();
            $user->active = $user->getActive();
            $user->dob = RequestMethods::post('dob');
            $user->playerNum = RequestMethods::post('playerNum');
            $user->cfbuPersonalNum = RequestMethods::post('cfbuPersonalNum');
            $user->team = RequestMethods::post('team');
            $user->nickname = RequestMethods::post('nickname');
            $user->position = RequestMethods::post('position');
            $user->grip = RequestMethods::post('grip');
            $user->other = RequestMethods::post('other');
            $user->photoMain = $photoMain;
            $user->photoThumb = $photoThumb;

            if (empty($errors) && $user->validate()) {
                $user->save();

                Event::fire('admin.log', array('success', 'User id: ' . $user->getId()));
                $view->successMessage(self::SUCCESS_MESSAGE_2);
                self::redirect('/admin/');
            } else {
                Event::fire('admin.log', array('fail', 'User id: ' . $user->getId()));
                $view->set('errors', $errors + $user->getErrors());
            }
        }
    }

    /**
     * @before _secured, _admin
     * @param type $id
     */
    public function edit($id)
    {
        $view = $this->getActionView();

        $user = App_Model_User::first(array('id = ?' => (int)$id));

        if (NULL === $user) {
            $view->warningMessage(self::ERROR_MESSAGE_2);
            self::redirect('/admin/user/');
        } elseif ($user->role == 'role_superadmin' && $this->getUser()->getRole() != 'role_superadmin') {
            $view->errorMessage(self::ERROR_MESSAGE_4);
            self::redirect('/admin/user/');
        }

        $view->set('user', $user);

        if (RequestMethods::post('submitEditUser')) {
            if($this->checkCSRFToken() !== true){
                self::redirect('/admin/user/');
            }
            
            $errors = array();
         
            if ($user->photoMain == '') {
                $fileManager = new FileManager(array(
                    'thumbWidth' => $this->loadConfigFromDb('thumb_width'),
                    'thumbHeight' => $this->loadConfigFromDb('thumb_height'),
                    'thumbResizeBy' => $this->loadConfigFromDb('thumb_resizeby'),
                    'maxImageWidth' => $this->loadConfigFromDb('photo_maxwidth'),
                    'maxImageHeight' => $this->loadConfigFromDb('photo_maxheight')
                ));

                $fileErrors = $fileManager->upload('photo', 'team', time().'_')->getUploadErrors();
                $files = $fileManager->getUploadedFiles();

                if (!empty($files)) {
                    foreach ($files as $i => $file) {
                        if ($file instanceof \THCFrame\Filesystem\Image) {
                            $photoMain = trim($file->getFilename(), '.');
                            $photoThumb = trim($file->getThumbname(), '.');
                            break;
                        }
                    }
                }else{
                    $errors['photo'] = $fileErrors;
                }
            } else {
                $photoMain = $user->photoMain;
                $photoThumb = $user->photoThumb;
            }
            
            if (RequestMethods::post('password') !== RequestMethods::post('password2')) {
                $errors['password2'] = array('Paswords doesnt match');
            }

            if (RequestMethods::post('email') != $user->email) {
                $email = App_Model_User::first(
                                array('email = ?' => RequestMethods::post('email', $user->email)), 
                                array('email')
                );

                if ($email) {
                    $errors['email'] = array('Email is already used');
                }
            }

            $pass = RequestMethods::post('password');

            if ($pass === null || $pass == '') {
                $salt = $user->getSalt();
                $hash = $user->getPassword();
            } else {
                $salt = PasswordManager::createSalt();
                $hash = PasswordManager::hashPassword($pass, $salt);
            }

            $user->firstname = RequestMethods::post('firstname');
            $user->lastname = RequestMethods::post('lastname');
            $user->email = RequestMethods::post('email');
            $user->password = $hash;
            $user->salt = $salt;
            $user->role = RequestMethods::post('role', $user->getRole());
            $user->active = RequestMethods::post('active');
            $user->dob = RequestMethods::post('dob');
            $user->playerNum = RequestMethods::post('playerNum');
            $user->cfbuPersonalNum = RequestMethods::post('cfbuPersonalNum');
            $user->team = RequestMethods::post('team');
            $user->nickname = RequestMethods::post('nickname');
            $user->position = RequestMethods::post('position');
            $user->grip = RequestMethods::post('grip');
            $user->other = RequestMethods::post('other');
            $user->photoMain = $photoMain;
            $user->photoThumb = $photoThumb;

            if (empty($errors) && $user->validate()) {
                $user->save();

                Event::fire('admin.log', array('success', 'User id: ' . $id));
                $view->successMessage(self::SUCCESS_MESSAGE_2);
                self::redirect('/admin/user/');
            } else {
                Event::fire('admin.log', array('fail', 'User id: ' . $id));
                $view->set('errors', $errors + $user->getErrors());
            }
        }
    }

    /**
     * 
     * @before _secured, _superadmin
     * @param type $id
     */
    public function delete($id)
    {
        $this->willRenderActionView = false;
        $this->willRenderLayoutView = false;

        if ($this->checkCSRFToken()) {
            $user = App_Model_User::first(array('id = ?' => $id));

            if (NULL === $user) {
                echo self::ERROR_MESSAGE_2;
            } else {
                if ($user->delete()) {
                    Event::fire('admin.log', array('success', 'User id: ' . $id));
                    echo 'success';
                } else {
                    Event::fire('admin.log', array('fail', 'User id: ' . $id));
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
    public function deleteUserMainPhoto($id)
    {
        $this->willRenderActionView = false;
        $this->willRenderLayoutView = false;

        if ($this->checkCSRFToken()) {
            $user = App_Model_User::first(array('id = ?' => (int) $id));

            if ($user === null) {
                echo self::ERROR_MESSAGE_2;
            } else {
                $unlinkMainImg = $user->getUnlinkPath();
                $unlinkThumbImg = $user->getUnlinkThumbPath();
                $user->photoMain = '';
                $user->photoThumb = '';

                if ($user->validate()) {
                    $user->save();
                    @unlink($unlinkMainImg);
                    @unlink($unlinkThumbImg);

                    Event::fire('admin.log', array('success', 'User id: ' . $user->getId()));
                    echo 'success';
                } else {
                    Event::fire('admin.log', array('fail', 'User id: ' . $user->getId()));
                    echo self::ERROR_MESSAGE_1;
                }
            }
        } else {
            echo self::ERROR_MESSAGE_1;
        }
    }
}
