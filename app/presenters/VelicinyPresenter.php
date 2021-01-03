<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

final class VelicinyPresenter extends Nette\Application\UI\Presenter
{
	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}
    
	public function renderDefault(int $page = 1): void
	{
		$this->template->page = $page;
        
		$this->template->veliciny = $this->database->table('veliciny')
			->order('nazev DESC')
			->fetchAll();
	}
    
	protected function createComponentEditForm(): Form
	{
		$form = new Form;
		$form->addText('nazev', 'Název veličiny:');
		$form->addSubmit('send', 'Aktualizovat');
		$form->onSuccess[] = [$this, 'formSucceeded'];
		return $form;
	}

	protected function createComponentNewForm(): Form
	{
		$form = new Form;
		$form->addText('nazev', 'Název veličiny:');
		$form->addSubmit('send', 'Vytvořit');
		$form->onSuccess[] = [$this, 'formSucceeded'];
		return $form;
	}

	public function formSucceeded(Form $form, $data): void
	{
		//Aktualizace v DB
        $id = $this->getParameter('id');
		if ($id) {
			$velicina = $this->database->table('veliciny')->get($id);
			$velicina->update($data);
            $this->flashMessage('Veličina byla upravena.');
		} else {
			$velicina = $this->database->table('veliciny')->insert($data);
            $this->flashMessage('Veličina byla vložena.');
		}
		$this->redirect('Veliciny:');
	}    
    
    
	public function actionEdit(int $id): void
	{
        $velicina = $this->database->table('veliciny')
			->where('id', $id)
            ->fetch();

		$this['editForm']->setDefaults($velicina->toArray());
	}    
    
}
