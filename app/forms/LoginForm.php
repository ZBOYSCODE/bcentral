<?php
namespace Gabs\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\Identical;

class LoginForm extends Form
{

    public function getCsrf()
    {
        return $this->security->getToken();
    }

    public function initialize()
    {
        // Email
        $email = new Text('email', array(
            'placeholder'	=> 'Email',
			"class"			=> "form-control input-lg"
        ));

        $email->addValidators(array(
            new PresenceOf(array(
                'message' => 'E-mail Requerido'
            )),
            new Email(array(
                'message' => 'E-mail no Valido'
            ))
        ));

        $this->add($email);

        // Password
        $password = new Password('password', array(
            'placeholder' => 'Password',
			"class"			=> "form-control input-lg"
        ));

        $password->addValidator(new PresenceOf(array(
            'message' => 'Password Requerido'
        )));

        $password->clear();

        $this->add($password);

        // Remember
        $remember = new Check('remember', array(
            'value' => 'yes',
			"checked" => "checked"
        ));

        $remember->setLabel('Remember me');

        $this->add($remember);

        // CSRF
        $csrf = new Hidden('csrf');

        $csrf->addValidator(new Identical(array(
            'value' => $this->security->getSessionToken(),
            'message' => 'CSRF validation failed'
        )));

        $csrf->clear();

        $this->add($csrf);

        $this->add(new Submit('go', array(
            'class' => 'btn btn-success'
        )));
    }
}
