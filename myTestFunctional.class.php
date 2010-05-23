<?php

/**
 * Testing class to help with testing
 *
 * Always begin your functional tests with:
 *
 * $browser = new myTestFunctional(new sfBrowser());
 */
class myTestFunctional extends sfTestFunctional {
    /**
     * Override to always load in the doctrine tester
     */
    public function __construct($broswer = null, $lime = null, $options = array()) {
        parent::__construct($broswer, $lime, $options);

        $this->setTester('doctrine', 'sfTesterDoctrine');
    }

    /**
     * @return myTestFunctional
     */
    public function loadData() {
        Doctrine_Core::loadData(sfConfig::get('sf_data_dir') . '/fixtures');

        return $this;
    }

    /**
     * @return myTestFunctional
     */
    public function isModuleAction($module, $action, $statusCode = 200) {
        $this->with('request')->begin()->
			isParameter('module', $module)->
			isParameter('action', $action)->
        end()->
        with('response')->begin()->
			isStatusCode($statusCode)->
        end();

        return $this;
    }

    /**
     * @return myTestFunctional
     */
    public function login($username = 'admin', $password = 'admin', $debug = false) {
        $this
        ->info(sprintf('Logging in with %s/%s ', $username, $password))
        ->get('/login')
        ->setField('signin[username]', $username)
        ->setField('signin[password]', $password)
        ->click('sign in');

        if ($debug) {
            $this->with('response')->begin()
            ->debug()
            ->end();
        }

        return $this->followRedirect();
    }

    /**
     * @return myTestFunctional
     */
    public function logout() {
        return $this
        ->get('/logout')
        ->isModuleAction('sfGuardAuth', 'signout', 302)
        ->followRedirect()
        ->with('user')->begin()
        ->isAuthenticated(false)
        ->end()
        ;
    }

    /**
     * @return myTestFunctional
     */
	public function checkForm($click, $name, $data, $hasError = false) {
		return $this->click($click, array($name => $data))->
			with('form')->begin()->
				hasErrors($hasError)->
			end();
	}
}