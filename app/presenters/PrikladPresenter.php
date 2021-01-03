<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

final class PrikladPresenter extends Nette\Application\UI\Presenter
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
	}

	protected function createComponentExampleForm(): Form
	{
		$form = new Form;
        $form->addHidden('pocet');
        $form->addHidden('prevod_id');
        
		$form->addText('vypocet', 'Výsledek:');
		$form->addSubmit('send', 'Zkontrolovat');
		$form->onSuccess[] = [$this, 'formSucceeded'];
		return $form;
	}
    
	public function formSucceeded(Form $form, $data): void
	{
        $results = $this->database->query("
            SELECT 
                  ? AS pocet
                  ,(? * p.koeficient) AS vypocet_ok
                  ,? AS vypocet_uzivatel
                  ,? - (? * p.koeficient) AS chyba
                  ,((ABS((? - (? * p.koeficient)) ) / ?) * 100) AS procento_chyby
                  ,zj.zkratka AS zdroj_zkratka
                  ,cj.zkratka AS cil_zkratka
              FROM prevody p 
                JOIN jednotky zj ON zj.id = p.zdroj_jednotka_id
                JOIN jednotky cj ON cj.id = p.cil_jednotka_id
              WHERE p.id = ?        
        ",$data['pocet'],$data['pocet'],$data['vypocet'],$data['vypocet'],$data['pocet'],$data['vypocet'],$data['pocet'],$data['vypocet'],$data['prevod_id']);
        
        $pole = (object)[];
        
        foreach($results as $result){
            if($result->procento_chyby > 0.01){
                $text = 'CHYBA! Správná odpověď byla: '.$result->vypocet_ok.'. Vaše odpověd: '.$result->vypocet_uzivatel;
                $this->flashMessage($text);
                $vysledek_id = -1;
            }
            else {
                $this->flashMessage('Správně.');
                $vysledek_id = 1;
            }
            
            $vysledky['prevod_id'] = $data->prevod_id;
            $user = $this->getUser(); 
            $vysledky['uzivatel_id'] = $user->isLoggedIn() ? $user->getId() : -1;
            $vysledky['priklad'] = '';
            $vysledky['vysledek_id'] = $vysledek_id;
            $this->database->table('vysledky')->insert($vysledky);
        }
		$this->redirect('Priklad:');
	}
    
    
	public function actionDefault(): void
	{
        $examples = $this->database->query("
            SELECT
           	    p.id AS prevod_id
                ,v.nazev AS velicina
                ,(FLOOR(1 + RAND() * 100)) AS pocet
                ,zj.nazev AS zdrojova_jednotka_nazev
                ,zj.zkratka AS zdrojova_jednotka_zkratka
                ,cj.nazev AS cilova_jednotka_nazev
                ,cj.zkratka AS cilova_jednotka_zkratka
            FROM prevody p
            	JOIN jednotky zj ON zj.id = p.zdroj_jednotka_id
              JOIN jednotky cj ON cj.id = p.cil_jednotka_id
              JOIN veliciny v ON v.id = zj.velicina_id
            ORDER BY RAND()
            LIMIT 1            
        ");
        $pole = (object)[];
        
        foreach($examples as $example){
            $pole->prevod_id = $example['prevod_id'];
            $pole->pocet = $example['pocet'];
            $pole->zdrojova_jednotka_nazev = $example['zdrojova_jednotka_nazev'];
            $pole->zdrojova_jednotka_zkratka = $example['zdrojova_jednotka_zkratka'];
            $pole->cilova_jednotka_nazev = $example['cilova_jednotka_nazev'];
            $pole->cilova_jednotka_zkratka = $example['cilova_jednotka_zkratka'];
        }
    
		$this['exampleForm']->setDefaults($pole);
        $this->template->example = $pole;
	} 
    
}
