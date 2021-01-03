<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

final class JednotkyPresenter extends Nette\Application\UI\Presenter
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
        
        $this->template->jednotky = $this->database->query("
                                SELECT 
                                    j.id
                                    ,v.nazev AS velicina
                                    ,CONCAT(j.nazev,' (',j.zkratka,')') AS nazev
                                    ,j.zkratka
                                    ,v.id AS velicina_id 
                                FROM 
                                    jednotky j
                                    JOIN veliciny v ON v.id = j.velicina_id
                                ORDER BY v.nazev,j.nazev");
	}
    
	protected function createComponentEditForm(): Form
	{
        $seznamVelicin = $this->database->table('veliciny')->fetchAll();
        $map = array();
        foreach ($seznamVelicin as $row) {
            $map[$row->id] = $row['nazev'];
        }
    
		$form = new Form;
		$form->addSelect('velicina_id', 'Veličina:', $map)->setPrompt('Zvolte veličinu')->setRequired('Zadejte prosím veličinu.');
		$form->addText('nazev', 'Název jednotky:')->setRequired('Zadejte prosím název jednotky.');
        $form->addText('zkratka', 'Zkratka:')->setRequired('Zadejte prosím zkratku.');
		$form->addSubmit('send', 'Aktualizovat');
		$form->onSuccess[] = [$this, 'formSucceeded'];
		return $form;
	}

	protected function createComponentNewForm(): Form
	{
        $seznamVelicin = $this->database->table('veliciny')->fetchAll();
        $map = array();
        foreach ($seznamVelicin as $row) {
            $map[$row->id] = $row['nazev'];
        }

		$form = new Form;
		$form->addSelect('velicina_id', 'Veličina:', $map)->setPrompt('Zvolte veličinu')->setRequired('Zadejte prosím veličinu.');
		$form->addText('nazev', 'Název jednotky:')->setRequired('Zadejte prosím název jednotky.');
        $form->addText('zkratka', 'Zkratka:')->setRequired('Zadejte prosím zkratku.');
		$form->addSubmit('send', 'Vytvořit');
		$form->onSuccess[] = [$this, 'formSucceeded'];
		return $form;
	}

	public function formSucceeded(Form $form, $data): void
	{
		//Aktualizace v DB
        $id = $this->getParameter('id');
		if ($id) {
			$jednotka = $this->database->table('jednotky')->get($id);
			$jednotka->update($data);
            $this->flashMessage('Jednotka byla upravena.');
		} else {
			$jednotka = $this->database->table('jednotky')->insert($data);
            $this->flashMessage('Jednotka byla vložena.');
		}
		$this->redirect('Jednotky:');
	}    
    
    
	public function actionEdit(int $id): void
	{
        $jednotka = $this->database->table('jednotky')
			->where('id', $id)
            ->fetch();

		$this['editForm']->setDefaults($jednotka->toArray());
	}    
    
}
