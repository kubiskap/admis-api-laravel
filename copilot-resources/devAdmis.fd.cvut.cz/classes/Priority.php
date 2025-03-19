<?php

class Priority
{
    protected $dbh;
    public $configWeight;
    public $resultEvaluate;
    public $attsArr;
    public $idProject;
    const ARRAY_KEYS_TEMPLATE = [
        "dopravni_zatizeni",
        "spolufinancovani",
        "dopravni_vyznam",
        "technicky_stav",
        "stavebni_stav",
        "zivotni_prostred",
        "regionalni_vyznam",
        "jedina_pristupova_cesta",
        "stav_pripravy",
        "hromadna_doprava",
        "nehodova_lokalita"
    ];

    public function __construct($idProject, array $attsArr)
    {
       // print_r(array_diff_key(array_keys($attsArr),self::ARRAY_KEYS_TEMPLATE));
        $this->configWeight = $this->getPriorityConfig($idProject);
        $this->attsArr = $attsArr;
        $this->idProject = $idProject;

        if((empty(array_diff_key(array_keys($attsArr),self::ARRAY_KEYS_TEMPLATE)) &&
            (empty(array_diff_key(array_keys($this->configWeight),self::ARRAY_KEYS_TEMPLATE))))){
            $this->resultEvaluate = $this->skalarniSoucin($this->configWeight, $attsArr);
        }
    }

    public function getAtts(){
        return $this->attsArr;
    }

    public function getResult(){
        return $this->resultEvaluate / 10;
    }

    public function getCorrectionValue(){

        return $this->getResult() / $this->getMaxScore();

    }

    public function getMaxScore(){
        $attsArrFull = [
            "dopravni_zatizeni" => 10,
            "spolufinancovani" => 10,
            "dopravni_vyznam" => 10,
            "technicky_stav" => 10,
            "stavebni_stav" => 10,
            "zivotni_prostred" => 10,
            "regionalni_vyznam" => 10,
            "jedina_pristupova_cesta" => 10,
            "stav_pripravy" => 10,
            "hromadna_doprava" => 10,
            "nehodova_lokalita" => 10
        ];
        return $this->skalarniSoucin($this->getPriorityConfig($this->idProject), $attsArrFull);

    }

    public function getPriorityConfig($idProject){
        $this->dbh = new DatabaseConnector();
        $stmt = $this->dbh->getDbLink()->prepare('SELECT configJson FROM rangePriorityScaleConfig 
    JOIN  type2subtype USING (idPriorityConfig)
    JOIN projects ON projects.idProjectType = type2subtype.idProjectType AND projects.idProjectSubtype = type2subtype.idProjectSubtype
           WHERE projects.idProject = :idProject     ');
        $stmt->bindParam(':idProject', $idProject, PDO::PARAM_INT);
        $stmt->execute();
        $this->configWeight = json_decode($stmt->fetchColumn(),true);
        //print_r($this->configWeight);
        return $this->configWeight;
    }

    public function skalarniSoucin( array $vect_A, array $vect_B)
    {
        $productResult = 0;
        $keysA = array_keys($vect_A);
        $keysB = array_keys($vect_B);
        if (empty(array_diff($keysA, $keysB))) {
            foreach ($vect_A as $index => $attValue) {
                (int) $attValue = $attValue ?:0;
                (int) $vect_B[$index] = $vect_B[$index] ?:0;
                $product = $attValue * $vect_B[$index];
                $productResult = $productResult + $product;
            }
        }
        return $productResult;
    }

    public function insert(){
        $insertProjectArr['priorityAtts'] = json_encode($this->attsArr);
        $insertProjectArr['idProject'] = $this->idProject;
        try {
            $confirmedProjectId = Project::insertProject($insertProjectArr, 21);
        }
        catch (Exception $e) {
            $lastId = 'Chyba pri volani funkce insertPriorityProject' . $e;
            writeError2Log('submit calling insertPriorityProject while inserting priority', $_POST, $e);
        }
        if(is_numeric($confirmedProjectId)){
            return true;
        }
        else{
            return false;
        }
    }

}