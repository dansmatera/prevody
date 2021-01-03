<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

final class PrevodyPresenter extends Nette\Application\UI\Presenter
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
        
        $this->template->prevody = $this->database->query("
                    SELECT 
                        p.id
                        ,CONCAT(zj.nazev,' (',zj.zkratka,')') AS zdrojova_jednotka
                        ,p.zdroj_jednotka_id 
                        ,CONCAT(cj.nazev,' (',cj.zkratka,')') AS cilova_jednotka
                        ,p.cil_jednotka_id 
                        ,CONCAT(1,zj.zkratka,' = ',CAST(p.koeficient AS CHAR)+0,cj.zkratka) AS definice
                        ,p.koeficient
                    FROM prevody p
                        JOIN jednotky zj ON zj.id = p.zdroj_jednotka_id
    	                JOIN jednotky cj ON cj.id = p.cil_jednotka_id
                    ORDER BY zj.nazev
        ");
        
        $this->template->chybejiciPrevody = $this->database->query("
                    SELECT 
                        zj.id AS zdroj_jednotka_id
                        ,cj.id AS cil_jednotka_id
                        ,v.nazev AS velicina
                        ,CONCAT(zj.nazev,' (',zj.zkratka,')') AS zdrojova_jednotka
                        ,CONCAT(cj.nazev,' (',cj.zkratka,')') AS cilova_jednotka
                      FROM 
                      	jednotky zj
                          JOIN jednotky cj ON cj.velicina_id = zj.velicina_id AND cj.id <> zj.id
                          JOIN veliciny v ON v.id = zj.velicina_id
                          LEFT JOIN prevody p ON p.zdroj_jednotka_id = zj.id AND p.cil_jednotka_id = cj.id
                      WHERE p.id IS NULL
                      ORDER BY v.nazev
        ");
	}

	protected function createComponentEditForm(): Form
	{
        $seznamJednotek = $this->database->table('jednotky')->fetchAll();
        $map = array();
        foreach ($seznamJednotek as $row) {
            $map[$row->id] = $row['nazev'];
        }
    
		$form = new Form;
        $form->addSelect('zdroj_jednotka_id', 'Zdrojová jednotka:', $map)->setPrompt('Zvolte zdroj')->setRequired('Zadejte prosím zdrojovou jednotku.');
        $form->addSelect('cil_jednotka_id', 'Cílová jednotka:', $map)->setPrompt('Zvolte cíl')->setRequired('Zadejte prosím cílovou jednotku.');
		$form->addText('koeficient', 'Koeficient:')->setRequired('Zadejte prosím koeficient.');
        $form->addSubmit('send', 'Aktualizovat');
		$form->onSuccess[] = [$this, 'formSucceeded'];
		return $form;
	}

	protected function createComponentNewForm(): Form
	{
        $seznamJednotek = $this->database->table('jednotky')->fetchAll();
        $map = array();
        foreach ($seznamJednotek as $row) {
            $map[$row->id] = $row['nazev'];
        }
    
		$form = new Form;
        $form->addSelect('zdroj_jednotka_id', 'Zdrojová jednotka:', $map)->setPrompt('Zvolte zdroj')->setRequired('Zadejte prosím zdrojovou jednotku.');
        $form->addSelect('cil_jednotka_id', 'Cílová jednotka:', $map)->setPrompt('Zvolte cíl')->setRequired('Zadejte prosím cílovou jednotku.');
		$form->addText('koeficient', 'Koeficient:')->setRequired('Zadejte prosím koeficient.');
		$form->addSubmit('send', 'Aktualizovat');
		$form->onSuccess[] = [$this, 'formSucceeded'];
		return $form;
	}

	public function formSucceeded(Form $form, $data): void
	{
		//Aktualizace v DB
        $id = $this->getParameter('id');
		if ($id) {
			$prevod = $this->database->table('prevody')->get($id);
			$prevod->update($data);
            $this->flashMessage('Převod byl upraven.');
		} else {
			$prevod = $this->database->table('prevody')->insert($data);
            $this->flashMessage('Převod byl vytvořen.');
		}
		$this->redirect('Prevody:');
	}
    
	public function actionEdit(int $id = null,int $zdroj_jednotka_id = null,int $cil_jednotka_id = null): void
	{
        if($id == null){
            $this['editForm']->setDefaults(['zdroj_jednotka_id' => $zdroj_jednotka_id,'cil_jednotka_id' => $cil_jednotka_id]);
            unset($this['editForm']['send']);
            $this['editForm']->addSubmit('send', 'Vytvořit');
            $this->template->edit = false;
        }
        else {
            $prevody = $this->database->table('prevody')->where('id', $id)->fetch();
      		$this['editForm']->setDefaults($prevody->toArray());
            $this->template->edit = true;
        }
	}    
}
