<?php

use App\Etc\Controller as Controller;
use THCFrame\Registry\Registry;
use THCFrame\Request\RequestMethods;
use THCFrame\Filesystem\FileManager;
use THCFrame\Security\PasswordManager;

/**
 * Description of UserController
 *
 * @author Tomy
 */
class App_Controller_User extends Controller
{

    /**
     * 
     */
    public function login()
    {
        $view = $this->getActionView();
        
        $canonical = 'http://' . $this->getServerHost() . '/login';

        $this->getLayoutView()->set('metatitle', 'Peďák - Přihlásit se')
                ->set('canonical', $canonical)
                ->set('activemenu', 'login');

        if (RequestMethods::post('submitLogin')) {

            $email = RequestMethods::post('email');
            $password = RequestMethods::post('password');
            $error = false;

            if (empty($email)) {
                $view->set('email_error', 'Email není vyplňen');
                $error = true;
            }

            if (empty($password)) {
                $view->set('password_error', 'Heslo není vyplněno');
                $error = true;
            }

            if (!$error) {
                try {
                    $security = Registry::get('security');
                    $status = $security->authenticate($email, $password);

                    if ($status === true) {
                        self::redirect('/');
                    } else {
                        $view->set('account_error', 'Email a/nebo heslo je špatně');
                    }
                } catch (\Exception $e) {
                    if (ENV == 'dev') {
                        $view->set('account_error', $e->getMessage());
                    } else {
                        $view->set('account_error', 'Email a/nebo heslo je špatně');
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
        $this->_willRenderActionView = false;
        $this->_willRenderLayoutView = false;
        
        $security = Registry::get('security');
        $security->logout();
        self::redirect('/');
    }

    /**
     * 
     */
    public function register($key)
    {
        if ($key === '8406c6ad864195ed144ab5c87621b6c233b548baeae6956df3463876aed6bc22d4a') {
            $view = $this->getActionView();
        
            $canonical = 'http://' . $this->getServerHost() . '/registrace';
            
            $this->getLayoutView()->set('metatitle', 'Peďák - Registrace')
                    ->set('canonical', $canonical);

            if (RequestMethods::post('register')) {
                $security = Registry::get('security');
                $errors = array();

                if (RequestMethods::post('password') !== RequestMethods::post('password2')) {
                    $errors['password2'] = array('Hesla se neshodují');
                }

                $email = App_Model_User::first(
                                array('email = ?' => RequestMethods::post('email')), array('email')
                );

                if ($email) {
                    $errors['email'] = array('Tento email je již použit');
                }

                $fileManager = new FileManager(array(
                    'thumbWidth' => $this->loadConfigFromDb('thumb_width'),
                    'thumbHeight' => $this->loadConfigFromDb('thumb_height'),
                    'thumbResizeBy' => $this->loadConfigFromDb('thumb_resizeby'),
                    'maxImageWidth' => $this->loadConfigFromDb('photo_maxwidth'),
                    'maxImageHeight' => $this->loadConfigFromDb('photo_maxheight')
                ));

                $fileErrors = $fileManager->upload('photo', 'team', time() . '_')->getUploadErrors();
                $files = $fileManager->getUploadedFiles();

                if (!empty($files)) {
                    foreach ($files as $i => $file) {
                        if ($file instanceof \THCFrame\Filesystem\Image) {
                            $photoMain = trim($file->getFilename(), '.');
                            $photoThumb = trim($file->getThumbname(), '.');
                            break;
                        }
                    }
                } else {
                    $errors['photo'] = $fileErrors;
                }

                $salt = PasswordManager::createSalt();
                $hash = PasswordManager::hashPassword(RequestMethods::post('password'), $salt);
                
                $user = new App_Model_User(array(
                    'firstname' => RequestMethods::post('firstname'),
                    'lastname' => RequestMethods::post('lastname'),
                    'email' => RequestMethods::post('email'),
                    'password' => $hash,
                    'salt' => $salt,
                    'role' => 'role_member',
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
                    $user->save();

                    $view->successMessage('Registrace byla úspěšná');
                    self::redirect('/');
                } else {
                    $view->set('errors', $errors + $user->getErrors())
                            ->set('user', $user);
                }
            }
        } else {
            self::redirect('/');
        }
    }

    /**
     * @before _secured, _member
     */
    public function profile()
    {
        $view = $this->getActionView();
        
        $canonical = 'http://' . $this->getServerHost() . '/profil';

        $errors = array();

        $user = App_Model_User::first(array(
                    'id = ?' => $this->getUser()->getId()
        ));

        $this->getLayoutView()->set('metatile', 'Peďák - Můj profil')
                ->set('canonical', $canonical)
                ->set('activemenu', 'profile');
        $view->set('user', $user);

        if (RequestMethods::post('editProfile')) {
            $security = Registry::get('security');

            if (RequestMethods::post('password') !== RequestMethods::post('password2')) {
                $errors['password2'] = array('Hesla se neshodují');
            }

            if (RequestMethods::post('email') != $user->email) {
                $email = App_Model_User::first(
                                array('email = ?' => RequestMethods::post('email', $user->email)), array('email')
                );

                if ($email) {
                    $errors['email'] = array('Tento email je již použit');
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

            if ($user->photoMain == '') {
                $fileManager = new FileManager(array(
                    'thumbWidth' => $this->loadConfigFromDb('thumb_width'),
                    'thumbHeight' => $this->loadConfigFromDb('thumb_height'),
                    'thumbResizeBy' => $this->loadConfigFromDb('thumb_resizeby'),
                    'maxImageWidth' => $this->loadConfigFromDb('photo_maxwidth'),
                    'maxImageHeight' => $this->loadConfigFromDb('photo_maxheight')
                ));

                $fileErrors = $fileManager->upload('photo', 'team', time() . '_')->getUploadErrors();
                $files = $fileManager->getUploadedFiles();

                if (!empty($files)) {
                    foreach ($files as $i => $file) {
                        if ($file instanceof \THCFrame\Filesystem\Image) {
                            $photoMain = trim($file->getFilename(), '.');
                            $photoThumb = trim($file->getThumbname(), '.');
                            break;
                        }
                    }
                } else {
                    $errors['photo'] = $fileErrors;
                }
            } else {
                $photoMain = $user->photoMain;
                $photoThumb = $user->photoThumb;
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
                $security->setUser($user);

                $view->successMessage(self::SUCCESS_MESSAGE_2);
                self::redirect('/profil');
            } else {
                $view->set('errors', $errors + $user->getErrors());
            }
        }
    }

    /**
     * @before _secured, _member
     */
    public function deleteUserMainPhoto()
    {
        $this->willRenderActionView = false;
        $this->willRenderLayoutView = false;

        if ($this->checkCSRFToken()) {
            $user = App_Model_User::first(array('id = ?' => (int) $this->getUser()->getId()));

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

                    echo 'success';
                } else {
                    echo self::ERROR_MESSAGE_1;
                }
            }
        } else {
            echo self::ERROR_MESSAGE_1;
        }
    }
}
