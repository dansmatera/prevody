<?php

namespace App;

use Nette;

class MyAuthenticator implements Nette\Security\IAuthenticator
{
	private $database;
	private $passwords;

	public function __construct(Nette\Database\Context $database, Nette\Security\Passwords $passwords)
	{
		$this->database = $database;
		$this->passwords = $passwords;
	}

	public function authenticate(array $credentials): Nette\Security\IIdentity
	{
		[$username, $password] = $credentials;

		$row = $this->database->table('uzivatele')
			->where('prezdivka', $username)
			->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('User not found.');
		}

        /*
		if (!$this->passwords->verify($password, $row->password)) {
			throw new Nette\Security\AuthenticationException('Invalid password.');
		}
        */
        
        if($row->heslo !== md5($password)){
            throw new Nette\Security\AuthenticationException('Invalid password.');
        }
        
        //Pøevod rolí
        switch($row->role_id){
            case 1: $role = 'nereg.uzivatel'; break;
            case 2: $role = 'uzivatel'; break;
            case 3: $role = 'admin'; break;
            default: $role = 'nereg.uzivatel'; break; 
        }

		return new Nette\Security\Identity(
			$row->id,
			[$role],
			['name' => $row->prezdivka]
		);
	}
}


?>