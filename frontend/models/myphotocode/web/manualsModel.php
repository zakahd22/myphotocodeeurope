<?php

require_once G_PATH . "models/baseModel.php";

class manualsModel extends baseModel {

    public function __construct() {
        parent::__construct();
    }

    public function getManuals($owner, $pbmodel) {
        $pbmodel = "%$pbmodel%";
        $this->setFilter('owner', '=', $owner, "AND");
        $this->setFilter('CLD_idType', 'IS NOT', 'NULL', "AND");
        $this->setFilter('App_booths.version', 'LIKE', $pbmodel, "AND");
        $this->setFilter('manuals.version', 'LIKE', $pbmodel, "AND");

        $this->setGroup('manuals.name');

        $sql = "
            SELECT manuals.id, manuals.name
            FROM App_booths
            CROSS JOIN CLD_boothTypes ON App_booths.CLD_idType = CLD_boothTypes.id
            CROSS JOIN manualsBooths ON (manualsBooths.booth_id = CLD_boothTypes.id OR manualsBooths.booth_id = 0)
            CROSS JOIN manuals ON manualsBooths.manual_id = manuals.id
        ";



        $query = $this->my_query($sql);
        $result = $this->requestQueryResults($query, 'all', array('manuals'));

        return $result;
    }

    public function getItems($codi) {
        $this->setFilter('manuals.id', '=', $codi, "AND");

        $this->entity->loadEntity('manuals');
        $fields .= $this->entity->getEntityFields();
        $fields .= ', ';
        $this->entity->loadEntity('manualsItems');
        $fields .= $this->entity->getEntityFields();

        $sql = "SELECT {$fields}
        FROM manuals
        LEFT JOIN manualsItems
        ON manualsItems.manual_id = manuals.id";

        $query = $this->my_query($sql);
        $result = $this->requestQueryResults($query, 'all', array('manuals', 'manualsItems'));
        return $result;
    }

    public function getAll() {

        $this->setGroup('manuals.name');
        $this->setGroup('manualsItems.manual_id');

        $sql = "
                SELECT manuals.id, manuals.name, manuals.version, manualsBooths.id, GROUP_CONCAT(DISTINCT manualsBooths.booth_id) as booths, manualsBooths.manual_id, GROUP_CONCAT(DISTINCT manualsItems.id) as Items, GROUP_CONCAT(DISTINCT manualsItems.desc) as des
                FROM manuals
                INNER JOIN manualsBooths 
                ON manuals.id = manualsBooths.manual_id
                INNER JOIN manualsItems
                ON manuals.id = manualsItems.manual_id
        ";



        $query = $this->my_query($sql);
        $result = $this->requestQueryResults($query, 'all', array('manuals', 'manualsBooths', 'manualsItems'));

        return $result;
    }
    
    public function getOne($id) {

        $this->setFilter('manuals.id', '=', $id, "AND");
        //$this->setGroup('manualsItems.id');
        //$this->setGroup('manualsItems.manual_id');
        //$this->setGroup('manualsItems.type');
        $this->setGroup('manualsItems.data');
        //$this->setGroup('manualsItems.desc');

        $sql = "
                SELECT 
                manuals.id, 
                manuals.name, 
                manuals.version, 
                manualsBooths.id, 
                GROUP_CONCAT(DISTINCT manualsBooths.booth_id) as Booths,
                manualsBooths.manual_id, 
                manualsItems.id, 
                manualsItems.manual_id, 
                manualsItems.type, 
                manualsItems.data, 
                manualsItems.desc

                FROM manuals
                INNER JOIN manualsBooths 
                ON manuals.id = manualsBooths.manual_id
                INNER JOIN manualsItems
                ON manuals.id = manualsItems.manual_id
        ";



        $query = $this->my_query($sql);
        $result = $this->requestQueryResults($query, 'all', array('manuals', 'manualsBooths', 'manualsItems'));

        return $result;
    }

    public function getItemWhereid($idItem) {
        $this->setFilter('id', '=', $idItem);
        return $this->select('manualsItems');
    }
    

}
