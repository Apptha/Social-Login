<?php
/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Sociallogin
 * @version     0.2.0
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2015 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 * */
/**
 * Social Login Login / Account Create Controller
 *
 * In this class contains the login and create account and  forget password operations.
 * Also it will connects social networks such as Google, Twitter, Yahoo and Facebook oAuth connections.
 *
 */
class Apptha_Sociallogin_IndexController extends Mage_Core_Controller_Front_Action {
	/**
	 * Render Apptha sociallogin pop-up layout
	 *
	 * @return void
	 */
	public function indexAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
	/**
	 * Customer Register Action
	 *
	 * @param string $firstname
	 * @param string $lastname
	 * @param string $email
	 * @param string $provider
	 *
	 * @return string
	 */
	public function customerAction($firstname, $lastname, $email, $provider) {
		/**
		 * Get Customer Model
		 *
		 * @return Mage_Customer_Model_Customer
		 */
		$customer = Mage::getModel('customer/customer');
        /**
         * Setting email id which is retreived from form.
         */
	    $standardInfo['email'] = $email;
		/**
		 * Retrieving the customer form posted values. @param array $standardInfo
		 * array values such as	@first_name,@last_name and @email
		 */
		$standardInfo['first_name'] = $firstname;
		$standardInfo['last_name'] = $lastname;
		$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
		->loadByEmail($standardInfo['email']);
		/**
		 * Check if Already registered customer.
		 */
		if ($customer->getId()) {
			/**
			 * Initiates the customer account session and logged into the site
			 */
			$this->_getCustomerSession()->setCustomerAsLoggedIn($customer);
			$this->_getCustomerSession()->addSuccess($this->__('Your account has been successfully connected through' . ' ' . $provider));
			/**
			 * Get customer current URL from customer session.
			 *
			 * @return string $link
			 */
			$this->loggedInSuccessfully();
			return;
				
		}
		/**
		 * Generate Random Password .
		 * @return string $randomPassword
		 */
		$randomPassword = $customer->generatePassword(8);
		/**
		 * Set the customer attributes into the customer collection for register the customer.
		 * Also sets the user used login provider like FB,Google,Twiter and Yahoo.
		 */
		$customer->setId(null)
		->setSkipConfirmationIfEmail($standardInfo['email'])
		->setFirstname($standardInfo['first_name'])
		->setLastname($standardInfo['last_name'])
		->setEmail($standardInfo['email'])
		->setPassword($randomPassword)
		->setConfirmation($randomPassword)
		->setLoginProvider($provider);
		/**
		 * Validate customer data and return errors if they are
		 * @param Mage_Customer_Model_Customer $validationCustomer
		 * @return array|string
		 */
		$errors = array();
		$validationCustomer = $customer->validate();
		$validationResult = true;
		/**
		 * If validated customer data is true then save the customer data into the Mage_Customer_Model_Customer $customer
		 */
		
		$customer->save();
		$this->_getCustomerSession()->addSuccess(
		$this->__('Thank you for registering with %s', Mage::app()->getStore()->getFrontendName()) .
                    '. ' .
		$this->__('You will receive welcome email with registration info in a moment.')
		);
			/**
			 * Send Account Notification success mail
			 */
		$customer->sendNewAccountEmail();
			/**
			 * Set the cutomer login session
			 */
		$this->_getCustomerSession()->setCustomerAsLoggedIn($customer);

		/**
		 * Get customer current URL from customer session.
		 *
		 * @return string $link
		 */
		$this->loggedInSuccessfully();
		return;
	}
	/**
	 * Function for logged in successfully redirect the the url.
	 * 
	 * return URL
	 */
	public function loggedInSuccessfully () {
		/**
		 * Get customer current URL from customer session.
		 *
		 * @return string $link
		 */
		$link = Mage::getSingleton('customer/session')->getLink();
		/**
		 * If customer session link is empty, set the request path URL
		 *
		 * @return string $requestpath
		 *
		*/
		$requestPath = trim($link, '/');
		
		/**
		 * Check if customer current URL is checkout URL for redirecting purpose.
		 */
		if (strstr($requestPath,'onepage')) { 
			$this->_redirectUrl($requestPath);
		} else { 
			$redirect = $this->redirection();
			/**
			 * This session is set for twitter and google since the referer url will be missing. 
			 */
			$authReferer = Mage::getSingleton('customer/session')->getBeforeAuthUrl();
			$redirect = ($redirect) ? $redirect : $authReferer;
			$this->_redirectUrl($redirect);
		}
	}
	/**
	 * Function to enable redirection to account dashboard or referer link
	 */
	public function redirection () {
		$enable_redirect_status = Mage::getStoreConfig('sociallogin/general/enable_redirect');
		/**
		 * Condition to check the enable redirect is set to yes.
		*/
		if ($enable_redirect_status) {
			$redirect = Mage::helper('customer')->getAccountUrl();
		} else {
			/**
			 * Redirect to the referer page.
			 */
		$redirect = Mage::getSingleton('customer/session')->getLink();
		    		}
		return  $redirect;
		
	}
	
	/**
	 * Retrieve customer session model object
	 *
	 * @return Mage_Customer_Model_Session
	 */
	private function _getCustomerSession() {
		return Mage::getSingleton('customer/session');
	}

	/**
	 * @Twitter login action
	 *
	 */
	public function twitterloginAction() {
		/**
		 * Include Twitter files for oAuth connection
		 */
		require 'sociallogin/twitter/twitteroauth.php';
		require 'sociallogin/config/twconfig.php';
		/**
		 * Retrives @Twitter consumer key and secret key from core session
		 */
		$tw_oauth_token = Mage::getSingleton('customer/session')->getTwToken();
		$tw_oauth_token_secret = Mage::getSingleton('customer/session')->getTwSecret();
		$twitteroauth = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET, $tw_oauth_token, $tw_oauth_token_secret);
		/**
		 * Get Accesss token from @Twitter oAuth
		 */
		$oauth_verifier = $this->getRequest()->getParam('oauth_verifier');
		$access_token = $twitteroauth->getAccessToken($oauth_verifier);
		/**
		 * Get @Twitter User Details from twitter account
		 *
		 * @return string Redirect URL or Customer save action
		 */
		$user_info = $twitteroauth->get('account/verify_credentials');
		/**
		 * Retrieve the user details into twitter profile info. @var $user_info array
		 *
		 */
		$firstname = $user_info->name;
		$email = Mage::getSingleton('customer/session')->getTwemail();
		$lastname = ' ';
		/**
		 * Retrieve the user details into twitter profile info. @var $user_info array
		 *
		 * If @user_info contains error means throws the error message.
		 */
		if (isset($user_info->error) || $email == '' || $firstname == '') {
			Mage::getSingleton('customer/session')->addError($this->__('Twitter Login connection failed'));
			$url = Mage::helper('customer')->getAccountUrl();
			return $this->_redirectUrl($url);
		} else {
            /**
             * If the email and firstname is not retreived print the error.
             */
				$this->customerAction($firstname, $lastname, $email, 'Twitter');
		}
	}
	/**
	 * @Twitter post action
	 *
	 * @return string Returns Twitter page URL for Authendication
	 */
	public function twitterpostAction() {
		/**
		 * Retrieve the customer posted email to authendicate @Twitter account
		 *
		 * @param string email_value
		 */
		$twitter_email = (string) $this->getRequest()->getPost('email_value');
		/**
		 * Set the $twitter_email into customer session
		 *
		 */
		Mage::getSingleton('customer/session')->setTwemail($twitter_email);
		/**
		 * Send the response to the customer request for twitter action
		 *
		 * @return string $url
		 */
		$url = Mage::helper('sociallogin')->getTwitterUrl();
		$url = ($url) ? $url : 'Twitter consumer key or secret key is invalid';
		$this->referalSession();
		$this->getResponse()->setBody($url);
	}

	/**
	 * @facebook login action
	 *
	 * Connect facebook Using oAuth coonection.
	 *
	 * @return string redirect URL
	 *
	 */
   public function fbloginAction() {
 	/**
 	 * Condition to check the email is retrieved from the user.
 	 */
       if($this->getRequest()->getParam('email')) {
	      $email = $this->getRequest()->getParam('email');
	      $firstName = $this->getRequest()->getParam('fname');
	      $lastName = $this->getRequest()->getParam('lname');
	      $data = $this->getRequest()->getParam('fb');
	      $this->customerAction($firstName, $lastName, $email, 'Facebook',$data);
	   } else {
	      Mage::getSingleton('customer/session')->addError($this->__('Facebook Login connection failed'));
	      $this->_redirect();
	   }
    }
	/**
	 * @Google login action
	 *
	 * Connect Google Using oAuth coonection.
	 *
	 * @return string redirect URL either customer save and loggedin or an error if any occurs
	 */
	public function googlepostAction() { 
	     /**
		 * Include @Google library files for oAuth connection
		 */
		require_once 'sociallogin/src/Google_Client.php';
		require_once 'sociallogin/src/contrib/Google_Oauth2Service.php';

		/**
		 * Retrieves the @google_client_id, @google_client_secret
		 */
		$google_client_id = Mage::getStoreConfig('sociallogin/google/google_id');
		$google_client_secret = Mage::getStoreConfig('sociallogin/google/google_secret');
		$google_redirect_url = Mage::getUrl() . 'sociallogin/index/googlepost/';

		/**
		 * Create the object @var $gClient from google client
		 */
		$gClient = new Google_Client();
		$gClient->setApplicationName('login');
		$gClient->setClientId($google_client_id);
		$gClient->setClientSecret($google_client_secret);
		$gClient->setRedirectUri($google_redirect_url);

		/**
		 * Create the object @var $google_oauthV2 from Google_Oauth2Service
		 */
		$google_oauthV2 = new Google_Oauth2Service($gClient);
		$token = Mage::getSingleton('core/session')->getGoogleToken();
		$reset = $this->getRequest()->getParam('reset');
		if ($reset) {
			unset($token);
			$gClient->revokeToken();
			$this->_redirectUrl(filter_var($google_redirect_url, FILTER_SANITIZE_URL));
		}

		/**
		 * If retrieve the param in request array
		 *
		 * @param string code
		 */
		$code = $this->getRequest()->getParam('code');

		if (isset($code)) {
			$gClient->authenticate($code);
			Mage::getSingleton('core/session')->setGoogleToken($gClient->getAccessToken());
			$this->_redirectUrl(filter_var($google_redirect_url, FILTER_SANITIZE_URL));
			$this->_redirectUrl($google_redirect_url);
			return;
		}

		/**
		 * If $token is non-empty set the access token
		 *
		 */
		if (isset($token)) {
			$gClient->setAccessToken($token);
		}
		if ($gClient->getAccessToken()) {

			/**
			 * Retrieve user details If user succesfully in Google
			 */
			$user = $google_oauthV2->userinfo->get();
			$user_id = $user['id'];
			$user_name = filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
			$email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
			$profile_url = filter_var($user['link'], FILTER_VALIDATE_URL);
			$token = $gClient->getAccessToken();
			Mage::getSingleton('core/session')->setGoogleToken($token);
		} else {

			/**
			 * get google Authendication URL
			 */
			$authUrl = $gClient->createAuthUrl();
		}

		/**
		 * If user doesn't logged-in redirects the login URL
		 */
		if (isset($authUrl)) {
			$this->_redirectUrl($authUrl);
		} else {

			/**
			 * Fetching user infor from google array $user
			 *
			 * @var string $firstname, ,  general info for users from @google account.
			 * @var string $familyname
			 * @var string $email
			 * @var string $id
			 */
			$firstname = $user['given_name'];
			$lastname = $user['family_name'];

			$email = $user['email'];
			$google_user_id = $user['id'];

			/**
			 * If @var $email is empty throws failure message.
			 */
			if ($email == '') {
				Mage::getSingleton('customer/session')->addError($this->__('Google Login connection failed'));
				$url = Mage::helper('customer')->getAccountUrl();
				
				return $this->_redirectUrl($url);
			} else {

				/**
				 * Do the customer account action with the login provider as Google
				 */
				/* $enable_redirect_status = Mage::getStoreConfig('sociallogin/general/enable_redirect');
				if(!($enable_redirect_status)){
					Mage::getSingleton('core/session')->addSuccess('Your account has been successfully connected through Google');
				} */
				$this->customerAction($firstname, $lastname, $email, 'Google');
			}
		}
	}
	/**
	 * 
	 */
	public function referalSession () {
		$redirect = Mage::app()->getRequest()->getServer('HTTP_REFERER');
		Mage::getSingleton('customer/session')->setBeforeAuthUrl($redirect);
	}
	
	/**
	 * Validation for Tax/Vat field for current store
	 *
	 * @return boolean true|false
	 */
	public function _isVatValidationEnabled($store = null) {
		return Mage::helper('customer/address')->isVatValidationEnabled($store);
	}
	/**
	 * Customer welcome function
	 *
	 * Its used for print welcome message once successfully logged in
	 *
	 * @return string customer success page URL.
	 */
	public function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false) {
		/**
		 * Throws the welcome success message when customer registered successfully
		 */
		$this->_getCustomerSession()->addSuccess(
		$this->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName())
		);
		/**
		 * Send the welcome mail to the customer registered email
		 */
		$customer->sendNewAccountEmail(
		$isJustConfirmed ? 'confirmed' : 'registered', '', Mage::app()->getStore()->getId()
		);
		$successUrl = $this->redirection();
		return $successUrl;
	}
	/**
	 * Customer login Action
	 * validate the social login form posted values if the user is registered user or not
	 * @return string Redirect URL.
	 */
	public function customerloginpostAction() {
		$session = $this->_getCustomerSession();
		/**
		 * @param array $login contains email and password
		 */
		$login['username'] = $this->getRequest()->getParam('email');
		$login['password'] = $this->getRequest()->getParam('password');
		/**
		 * Check customer already logged in or not using the customer session
		 * @return string $message
		 */
		if ($session->isLoggedIn()) {
			$message = 'Already loggedin';
			$this->getResponse()->setBody($message);
			return;
		}
		/**
		 * If Login data has been posted with @param array $login
		 * @param username
		 * @param password
		 * @return string $messge
		 */
			if (($this->getRequest()->isPost()) && !empty($login['username']) && !empty($login['password'])) {
				try {
					$session->login($login['username'], $login['password']);
					if ($session->getCustomer()->getIsJustConfirmed()) {
						$this->getResponse()->setBody($this->_welcomeCustomer($session->getCustomer(), true));
					}
				} catch (Mage_Core_Exception $e) {
	   /**
		 * Exception warning message when invalid email id used.
		 */
		switch ($e->getCode()) {
			case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
				$value = Mage::helper('customer')->getEmailConfirmationUrl($login['username']);
				$message = Mage::helper('customer')->__('Account Not Confirmed', $value);
				$this->getResponse()->setBody($message);
				break;
			case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
				$message = $this->__('Invalid Email Address or Password');
				$this->getResponse()->setBody($message);
				break;
			default:
				$message = $e->getMessage();
				/**
				 * Send the response message for customer login request
				 * @return string $message
				*/
				$this->getResponse()->setBody($message);
		        }
					/**
					 * Set customer username @param username in customer session
					 */
					$session->setUsername($login['username']);
				} catch (Exception $e) {
					/**
					 *  @throws Exception message
					 */
					return $e;
				}
		
	    /**
		 * After successful logged-in, its redirect to the respective page.
		 */
		if ($session->getCustomer()->getId()) {
			$link = Mage::getSingleton('customer/session')->getLink();
			$requestPath = '';
			/**
			 * Condition to check the link is not empty
			 */
			$requestPath = trim($link, '/');
		
			if (strstr($requestPath,'onepage')) {
				$this->getResponse()->setBody($requestPath);
			} else {
				$redirect = $this->redirection();
				/**
				 * Sends the response for referer url
				 *  @return string URL
				*/
				$this->getResponse()->setBody($redirect);
			}
		}
      }
	}
	
	/**
	 * Customer Register Action
	 * validate the social regiter form posted values
	 * @return string Redirect URL.
	 */
	public function createPostAction() {
		$customer = Mage::getModel('customer/customer');
		$session = $this->_getCustomerSession();
		/**
		 * Condition to check customer is logged in.
		 */
		if ($session->isLoggedIn()) {
			$this->_redirect('*/*/');
			return;
		}
		/**
		 * Validate the captcha code if captcha is enabled
		 * @return string if incorrect capatcha it will return error message
		 */
		$enable_captcha = Mage::getStoreConfig('customer/captcha/enable');
        /**
         * Condition to check whether captcha is enabled.
         */
		if ($enable_captcha == '1') {
			$newcaptch = $this->getRequest()->getPost('captcha');
			$_captcha = Mage::getModel('customer/session')->getData('user_create_word');
			$captcha_img_data = $_captcha['data'];
			if ($newcaptch['user_create'] != $captcha_img_data) {
				$this->getResponse()->setBody($this->__('Incorrect CAPTCHA.'));
				return;
			}
		}
		/**
		 * Preventing the Cross-site Scripting (XSS) injection from an user inputs
		 */
		$session->setEscapeMessages(true);
		if ($this->getRequest()->isPost()) {
			$errors = array();
			if (!$customer = Mage::registry('current_customer')) {
				$customer = Mage::getModel('customer/customer')->setId(null);
			}
			/**
			 * @var $customerForm Mage_Customer_Model_Form
			 */
			$customerForm = Mage::getModel('customer/form');
			$customerForm->setFormCode('customer_account_create')
			->setEntity($customer);
			$customerData = $customerForm->extractData($this->getRequest());
			$is_subscribed = $this->getRequest()->getParam('is_subscribed', false);
			$customer = $this->isSubscribedtrue($is_subscribed, $customer);
			/**
			 * Get customer group id from customer collection
			 */
			$customer->getGroupId();
            /**
             * Condition to check the create address field is present.
             */
			if ($this->getRequest()->getPost('create_address')) {
				/**
				 * @var $address Mage_Customer_Model_Address
				 */
				$address = Mage::getModel('customer/address');
				/**
				 * @var $addressForm Mage_Customer_Model_Form
				 */
				$addressForm = Mage::getModel('customer/form');
				$addressForm->setFormCode('customer_register_address')
				->setEntity($address);
				/**
				 * Extracting the address Data from array  $addressData
				 */
				$addressData = $addressForm->extractData($this->getRequest(), 'address', false);
				/**
				 * validate the address data
				 * @return boolean True|False
				 */
				$addressErrors = $addressForm->validateData($addressData);
				if ($addressErrors === true) {
					$address->setId(null)
					->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
					->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
					$addressForm->compactData($addressData);
					$customer->addAddress($address);

					$addressErrors = $address->validate();
					if (is_array($addressErrors)) {
						$errors = array_merge($errors, $addressErrors);
					}
				} else {
					$errors = array_merge($errors, $addressErrors);
				}
			}
			try {
				$customerErrors = $customerForm->validateData($customerData);
				if ($customerErrors !== true) {
					$errors = array_merge($customerErrors, $errors);
				}
					$customerForm->compactData($customerData);
					$customer->setPassword($this->getRequest()->getPost('password'));
					/**
					 * check magento version 1.9.1 and above
					 */
					$customer->setPasswordConfirmation($this->getRequest()->getPost('confirmation'));
					$customerErrors = $customer->validate();
				/**
				 * If @var $validationResult is true dispatching the event into customer_register_success
				 * @return string URL
				 */
				$validationResult = count($errors) == 0;
				if (true === $validationResult) {
					$customer->save();
					Mage::dispatchEvent('customer_register_success', array('account_controller' => $this, 'customer' => $customer)
					);
					if ($customer->isConfirmationRequired()) {
						$customer->sendNewAccountEmail(
                                'confirmation', $session->getBeforeAuthUrl(), Mage::app()->getStore()->getId()
						);
						$session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));
						$this->getResponse()->setBody(Mage::getUrl('/index', array('_secure' => true)));
						return;
					} else {
						$session->setCustomerAsLoggedIn($customer);
						$url = $this->_welcomeCustomer($customer);
						$this->getResponse()->setBody($url);
						return;
					}
				} else {
					/**
					 * If @var $validationResult is false throws the error message
					 * @return string $errorMessage
					 */
					$session->setCustomerFormData($this->getRequest()->getPost());
					/**
					 * Add error if customer data is incorrect
					 * @return string
					 */
					$session->addError($this->__('Invalid customer data'));
					
				}
			} catch (Mage_Core_Exception $e) {
				/**
				 * Throws the exception if email already exists
				 * @return string $message
				 */
				$session->setCustomerFormData($this->getRequest()->getPost());
				if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
					$message = $this->__('Email already exists');
					$this->getResponse()->setBody($message);
					$session->setEscapeMessages(false);
					return;
				} else {
					$message = $e->getMessage();
					$this->getResponse()->setBody($message);
					return;
				}
				$session->addError($message);
			} catch (Exception $e) {
				/**
				 * Throws the exception when customer data can't save
				 * @return string
				 */
				$session->setCustomerFormData($this->getRequest()->getPost())
				->addException($e, $this->__('Cannot save the customer.'));
			}
		}
		if (!empty($message)) {
			$this->getResponse()->setBody($message);
		}
		/**
		 * Sends the reponse for customer request
		 * @return string URL
		 */
		$this->getResponse()->setBody(Mage::getUrl('*/index', array('_secure' => true)));
	}
	/**
	 * Function to check subscrption is set.
	 */
	public function isSubscribedtrue ($is_subscribed, $customer) {
		
		if ($is_subscribed) {
			$customer->setIsSubscribed(1);
		}
		return $customer;
	}
	/**
	 * ForgetPassword Action
	 * @param string $email Forget password action for forget password form
	 * @return string $message.
	 */
	public function forgotPasswordPostAction() {
		$email = (string) $this->getRequest()->getParam('forget_password');
		/**
		 * @var $customer Mage_Customer_Model_Customer
		 */
		$customer = Mage::getModel('customer/customer')
		->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
		->loadByEmail($email);
		if ($customer->getId()) {
			try {
				/**
				 * Generate password link with help of core mage customer helper function
				 *
				 * @return string URL
				 */
				$newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
				$customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
				/**
				 * Send the mail to customer requested mail
				 */
				$customer->sendPasswordResetConfirmationEmail();
			} catch (Exception $exception) {
				$this->_getCustomerSession()->addError($exception->getMessage());
				return;
			}
			$message = $this->__('You will receive an email ').$email.' ';
            $message = $message.$this->__('with a link to reset your password');
		} else {
			$message = $this->__('If there is no account associated with this email please enter your correct email-id');
		}
		
		/**
		 * Send the confimation message to the customer
		 */
		$this->getResponse()->setBody($message);
	}
}
