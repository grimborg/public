<?php

class Admin_GuestbookController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $guestbook = new Admin_Model_Guestbook();
                $this->view->entries = $guestbook->fetchAll();
    }

    public function signAction()
    {
        $request = $this->getRequest();
        $form    = new Admin_Form_Guestbook();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $model = new Admin_Model_Guestbook($form->getValues());
                $model->save();
                return $this->_helper->redirector('index');
            }
        }
        
        $this->view->form = $form;
    }



}



