<?php
namespace Gabs\Auth;

use Phalcon\Mvc\User\Component;

use Gabs\Models\Users;
use Gabs\Models\Personas;

use Gabs\Models\RememberTokens;
use Gabs\Models\SuccessLogins;
use Gabs\Models\FailedLogins;
use Gabs\Models\WebServiceClient;

/**
 * Gabs\Auth\Auth
 * Manages Authentication/Identity Management in Gabs
 */

class Auth extends Component
{
    /**
     * Checks the user credentials
     *
     * @param array $credentials
     * @return boolean
     * @throws Exception
     */
    public function check($credentials)
    {
		if($this->configLdap->ldapValida){
			$ldap = ldap_connect($this->configLdap->ldapHost);
			ldap_set_option ($ldap, LDAP_OPT_REFERRALS, 0);
			ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);			
			
            if(!$ldap)
            {
                throw new Exception('Error de conexión a LDAP','usuario');
            }
			
            if($bind = @ldap_bind($ldap, $credentials['usuario'].$this->configLdap->ldapUserDom, $credentials['password'])) {
                // valid
                ldap_unbind($ldap);
            } else {
				throw new Exception('Combinación Usuario/Password Erronea','usuario');
            }			
		}
		$ws = new WebServiceClient();
		$user = $ws->getUsername($credentials['usuario']);
		
		if($user==false){
			throw new Exception('Usuario no Encontradodo en SM','usuario');
		}elseif($user==null){
			throw new Exception('Error de comunicacion con SM','usuario');
		}
		
        $this->createRememberEnvironment($user);
		
        $this->session->set('auth-identity', array(
            'id'	=> $user,
			'name'	=> $user
        ));

    }

    /**
     * Creates the remember me environment settings the related cookies and generating tokens
     *
     * @param \Gabs\Models\Users $user
     * @throws Exception
     */
    public function saveSuccessLogin($user)
    {
        $successLogin = new SuccessLogins();
        $successLogin->usersId = $user->id;
        $successLogin->ipAddress = $this->request->getClientAddress();
        $successLogin->userAgent = $this->request->getUserAgent();
        if (!$successLogin->save()) {
            $messages = $successLogin->getMessages();
            throw new Exception($messages[0]);
        }
    }

    /**
     * Implements login throttling
     * Reduces the effectiveness of brute force attacks
     *
     * @param int $userId
     */
    public function registerUserThrottling($userId)
    {
        $failedLogin = new FailedLogins();
        $failedLogin->usersId = $userId;
        $failedLogin->ipAddress = $this->request->getClientAddress();
        $failedLogin->attempted = time();
        $failedLogin->save();

        $attempts = FailedLogins::count(array(
            'ipAddress = ?0 AND attempted >= ?1',
            'bind' => array(
                $this->request->getClientAddress(),
                time() - 3600 * 6
            )
        ));

        switch ($attempts) {
            case 1:
            case 2:
                // no delay
                break;
            case 3:
            case 4:
                sleep(2);
                break;
            default:
                sleep(4);
                break;
        }
    }

    /**
     * Creates the remember me environment settings the related cookies and generating tokens
     *
     * @param \Gabs\Models\Users $user
     */
    public function createRememberEnvironment($user)
    {
		$expire = time() + 86400 * 365;
		$this->cookies->set('RMU', $user, $expire);
    }

    /**
     * Check if the session has a remember me cookie
     *
     * @return boolean
     */
    public function hasRememberMe()
    {
        return $this->cookies->has('RMU');
    }

    /**
     * Logs on using the information in the cookies
     *
     * @return \Phalcon\Http\Response
     */
    public function loginWithRememberMe()
    {
        $userId = $this->cookies->get('RMU')->getValue();
		$this->session->set('auth-identity', array(
			'id'	=> $userId,
			'name'	=> $userId
		));
		return $this->response->redirect('');
    }

    /**
     * Checks if the user is banned/inactive/suspended
     *
     * @param \Gabs\Models\Users $user
     * @throws Exception
     */
    public function checkUserFlags(Users $user)
    {
        if ($user->active != 'Y') {
            throw new Exception('The user is inactive');
        }

        if ($user->banned != 'N') {
            throw new Exception('The user is banned');
        }

        if ($user->suspended != 'N') {
            throw new Exception('The user is suspended');
        }
    }

    /**
     * Returns the current identity
     *
     * @return array
     */
    public function getIdentity()
    {
	
        return $this->session->get('auth-identity');
    }

    /**
     * Returns the current identity
     *
     * @return string
     */
    public function getName()
    {
        $identity = $this->session->get('auth-identity');
        return $identity['name'];
    }

    /**
     * Removes the user identity information from session
     */
    public function remove()
    {
        if ($this->cookies->has('RMU')) {
            $this->cookies->get('RMU')->delete();
        }

        $this->session->remove('auth-identity');
    }

    /**
     * Auths the user by his/her id
     *
     * @param int $id
     * @throws Exception
     */
    public function authUserById($id)
    {
        $user = Users::findFirstById($id);
        if ($user == false) {
            throw new Exception('The user does not exist');
        }

        $this->checkUserFlags($user);

        $this->session->set('auth-identity', array(
            'id' => $user->id,
            'name' => $user->name,
            'profile' => $user->profile->name
        ));
    }

    /**
     * Get the entity related to user in the active identity
     *
     * @return \Gabs\Models\Users
     * @throws Exception
     */
    public function getUser()
    {
        $identity = $this->session->get('auth-identity');
        if (isset($identity['id'])) {

            $user = Users::findFirstById($identity['id']);
            if ($user == false) {
                throw new Exception('The user does not exist');
            }

            return $user;
        }

        return false;
    }
}
