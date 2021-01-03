<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

final class RegistracePresenter extends Nette\Application\UI\Presenter
{
	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}
    
	public function renderDefault(): void
	{
	}

	protected function createComponentNewForm(): Form
	{
		$form = new Form;
		$form->addText('username', 'Přezdívka:')->setRequired('Prosím zadejte přezdívku.');
		$form->addPassword('password', 'Heslo:')->setRequired('Prosím zadejte heslo.');
		$form->addSubmit('send', 'Registrovat');
		$form->onSuccess[] = [$this, 'formSucceeded'];
		return $form;
	}

	public function formSucceeded(Form $form, $data): void
	{
        $row = $this->database->table('uzivatele')->where('prezdivka', $data->username)->fetch();
        if ($row) {
			$this->flashMessage('Tento uživatel již existuje!');
            $this->redirect('Registrace:');
		}
        else {
            $user['role_id'] = 2;
            $user['prezdivka'] = $data->username;
            $user['heslo'] = md5($data->password);
            $this->database->table('uzivatele')->insert($user);
            $this->flashMessage('Byli jste zaregistrováni. Můžete se přihlásit.');
            $this->redirect('Sign:in:');
        }
	}    
}
