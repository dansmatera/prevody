<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

final class PrehledyPresenter extends Nette\Application\UI\Presenter
{
	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}
    
	public function renderDefault(): void
	{
        $user = $this->getUser(); 
        $uzivatel_id = $user->getId();

        $results = $this->database->query("
            SELECT 
                res.celkem
                ,res.spravne
                ,ROUND(100.0 * res.spravne / res.celkem,2) AS uspesnost
            FROM (
                SELECT COUNT(*) AS celkem,SUM(IF(v.vysledek_id = 1,1,0)) AS spravne
            	FROM vysledky v
                WHERE ((v.uzivatel_id = ?) OR (? = 3))
             ) res",$uzivatel_id,$uzivatel_id);
        
        $pole = (object)[];
        foreach($results as $result){
            $pole->celkem = $result['celkem'];
            $pole->uspesnost = $result['uspesnost'];
        }
        $this->template->stats = $pole;
        
        $results = $this->database->query("SELECT res.velicina,res.celkem,res.spravne,ROUND(100.0 * res.spravne / res.celkem,2) AS uspesnost
                    FROM (
                        SELECT ve.nazev AS velicina,COUNT(*) AS celkem,SUM(IF(v.vysledek_id = 1,1,0)) AS spravne
	                    FROM vysledky v 
	                       JOIN prevody p ON p.id = v.prevod_id
    	                   JOIN jednotky zj ON zj.id = p.zdroj_jednotka_id
    	                   JOIN jednotky cj ON cj.id = p.cil_jednotka_id
    	                   JOIN veliciny ve ON ve.id = zj.velicina_id
                        WHERE ((v.uzivatel_id = ?) OR (? = 3))
                        GROUP BY ve.nazev
                    ) res
                    ORDER BY 4 DESC",$uzivatel_id,$uzivatel_id);
        $this->template->stats_veliciny = $results;
        
        $results = $this->database->query("
                    SELECT 
                    	CONCAT(zj.zkratka,' -> ',cj.zkratka) AS prevod
                    	,res.celkem
                        ,res.spravne
                        ,ROUND(100.0 * res.spravne / res.celkem,2) AS uspesnost
                    FROM (
                        SELECT 
                           v.prevod_id
       					   ,COUNT(*) AS celkem
                           ,SUM(IF(v.vysledek_id = 1,1,0)) AS spravne
	                    FROM vysledky v
                        WHERE ((v.uzivatel_id = ?) OR (? = 3))
                        GROUP BY v.prevod_id
                    ) res
                       JOIN prevody p ON p.id = res.prevod_id
   	                   JOIN jednotky zj ON zj.id = p.zdroj_jednotka_id
   	                   JOIN jednotky cj ON cj.id = p.cil_jednotka_id
                    ORDER BY 4 DESC",$uzivatel_id,$uzivatel_id);
        $this->template->stats_prevody = $results;
                            
	}
    
}
