<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;


final class SignPresenter extends Nette\Application\UI\Presenter
{
	protected function createComponentSignInForm(): Form
	{
		$form = new Form;
		$form->addText('username', 'Nick:')->setRequired('Prosím vyplňte vaši přezdívku.');
		$form->addPassword('password', 'Password:')->setRequired('Prosím zadejte heslo.');
		$form->addSubmit('send', 'Přihlásit se');
		$form->onSuccess[] = [$this, 'signInFormSucceeded'];
		return $form;
	}


	public function signInFormSucceeded(Form $form, \stdClass $values): void
	{
		try {
			$this->getUser()->login($values->username, $values->password);
			$this->redirect('Homepage:');

		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError('Nesprávné jméno nebo heslo.');
		}
	}


	public function actionOut(): void
	{
		$this->getUser()->logout();
		$this->flashMessage('Byli jste odhlášeni.');
		$this->redirect('Homepage:');
	}
}
