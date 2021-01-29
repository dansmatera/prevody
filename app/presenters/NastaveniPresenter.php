<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;


final class NastaveniPresenter extends Nette\Application\UI\Presenter
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
        
        $user = $this->getUser();
        
        $results = $this->database->query("
            SELECT v.nazev,n.stav_id,n.id AS nastaveni_id
            FROM nastaveni n
                JOIN veliciny v ON v.id = n.velicina_id
            WHERE n.uzivatel_id = ? AND n.stav_id = 1
            ",$user->getId());

        $this->template->povolene_veliciny = $results;
        
        $results = $this->database->query("
            SELECT v.nazev,n.stav_id,n.id AS nastaveni_id
            FROM nastaveni n
                JOIN veliciny v ON v.id = n.velicina_id
            WHERE n.uzivatel_id = ? AND n.stav_id = -1
            ",$user->getId());
            
        $this->template->zakazane_veliciny = $results;
	}
    
	public function actionOff(int $nastaveni_id): void
	{
        $results = $this->database->query("UPDATE nastaveni SET stav_id = (-1) * stav_id WHERE id = ?",$nastaveni_id);
        $this->flashMessage('Veličina byla deaktivována.');
        $this->redirect('Nastaveni:');
	}    

	public function actionOn(int $nastaveni_id): void
	{
        $results = $this->database->query("UPDATE nastaveni SET stav_id = (-1) * stav_id WHERE id = ?",$nastaveni_id);
        $this->flashMessage('Veličina byla aktivována.');
        $this->redirect('Nastaveni:');
	}    

}
