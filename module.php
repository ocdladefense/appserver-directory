<?php

class DirectoryModule extends Module {

    public function __construct() {

        parent::__construct();
    }

    public function showMemberDirectory(){

        $params = $_POST;
        $searchBar = $this->getSearchBar($params);

        $selectedOccupation = $params["Ocdla_Occupation_Field_Type__c"] != "All Occupations/Fields" ? $params["Ocdla_Occupation_Field_Type__c"] : null;
        $selectedInterest = $params["areaOfInterest"] != "All Areas of Interest" ? $params["areaOfInterest"] : null;

        if($selectedOccupation == null) unset($params["Ocdla_Occupation_Field_Type__c"]);
        if($selectedInterest == null) unset($params["areaOfInterest"]);

        $query = $this->buildDirectoryQuery($params);

        $api = $this->loadForceApi();
        $result = $api->query($query);

        if(!$result->success()) throw new Exception($result->getErrorMessage());

        $records = $result->getRecords();

        $contacts = Contact::from_query_result_records($records);

        $tpl = new Template("member-list");
        $tpl->addPath(__DIR__ . "/templates");

        return $tpl->render(array(
            "count"             => count($contacts),
            "search"            => $searchBar,
            "contacts"          => $contacts,
            "showQuery"         => true,
            "query"             => $query
        ));
    }


    public function showMemberSingle($id){

        $api = $this->loadForceApi();

        $query = "SELECT Id, FirstName, LastName, MailingCity, Ocdla_Current_Member_Flag__c, MailingState, Phone, Email, Ocdla_Occupation_Field_Type__c, Ocdla_Organization__c, (SELECT Interest__c from AreasOfInterest__r) FROM Contact WHERE Id = '$id'";

        $records = $api->query($query)->getRecords();

        $contacts = Contact::from_query_result_records($records);


        $tpl = new Template("member-single");
        $tpl->addPath(__DIR__ . "/templates");

        return $tpl->render(array(
            "contacts"          => $contacts,
            "isSingle"          => true,
            "showQuery"         => true,
            "query"             => $query
        ));


    }

    public function getSearchBar($params){

        $search = new Template("member-search");
        $search->addPath(__DIR__ . "/templates");
        

        return $search->render(array(
            "occupationFields"   => $this->getOccupationFieldsDistinct(),
            "selectedOccupation" => $params["Ocdla_Occupation_Field_Type__c"],
            "areasOfInterest"    => $this->getAreasOfInterest(),
            "selectedInterest"   => $params["areaOfInterest"],
            "firstName"          => $params["FirstName"],
            "lastName"           => $params["LastName"],
            "companyName"        => $params["Ocdla_Organization__c"],
            "city"               => $params["MailingCity"],
            "includeExperts"     => $params["IncludeExperts"]
        ));
    }
    public function buildDirectoryQuery($params){

        $includeExperts = $params["IncludeExperts"];
        unset($params["IncludeExperts"]);

        $areaOfInterest = $params["areaOfInterest"];
        unset($params["areaOfInterest"]);

        $onlyExperts = $params["only-experts"];
        unset($params["only-experts"]);

        $fields = "Id, FirstName, LastName, MailingCity, Ocdla_Current_Member_Flag__c, MailingState, Phone, Email, Ocdla_Occupation_Field_Type__c, Ocdla_Organization__c, (SELECT Interest__c from AreasOfInterest__r)";
        
        $conditions = array();
        foreach($params as $field => $value){

            if(!empty($value)){

                $conditions[] = "$fields LIKE '%$value%'";
            }
        }

        if(!empty($includeExperts) && $includeExperts) $conditions[] = "Ocdla_is_expert_witness__c = false";

        if($onlyExperts){

            $fields.= ", Ocdla_Expert_Witness_Other_Areas__c, Ocdla_Expert_Witness_Primary__c";
            $conditions[] = "Ocdla_is_expert_witness__c = True";
        }

        // If there is an area of interest selected, query for all of the contacts who have set that as one of their areas of intersts.
        // Only use those contacts in you query.
        if(!empty($areaOfInterest)){

            $conditions[] = "id IN (SELECT Contact__c FROM AreaOfInterest__c WHERE Interest__c = '$areaOfInterest')";
        }

        $conditions[] = "Ocdla_Current_Member_Flag__c = True";

        $query = "SELECT $fields FROM Contact";

        if(!empty($conditions)){

            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query.= " AND (NOT Email LIKE '%qq.com%')";

        $query .= " ORDER BY LastName";

        return $query;
    }

    public function getOccupationFieldsDistinct(){

        $query = "SELECT Ocdla_Occupation_Field_Type__c FROM Contact ORDER BY Ocdla_Occupation_Field_Type__c DESC";

        $api = $this->loadForceApi();

        $result = $api->query($query);

        if(!$result->isSuccess()) throw new Exception($result->getErrorMessage());

        $records = $result->getRecords();

        $areas = array();

        foreach($records as $record){

            $area = $record["Ocdla_Occupation_Field_Type__c"];

            if(!in_array($area, $areas)){

                array_unshift($areas, $area);
            }
        }

        return $areas;
    }


    public function getAreasOfInterest(){

        $pickListId = "0Nt5b000000CbzK";

        $req = $this->loadForceApi();

        $url = "/services/data/v39.0/tooling/sobjects/GlobalValueSet/$pickListId";

        $resp = $req->send($url);

        $picklistValues = $resp->getBody()["Metadata"]["customValue"];

        $areasOfInterest = array();
        foreach($picklistValues as $value){

            $areasOfInterest[] = $value["valueName"];
        }

        return $areasOfInterest;
    }

    ////////////////////////////////////    EXPERT WITNESSES    /////////////////////////////////////////////////////////////

    public function showExpertDirectory(){

        $selectedPrimaryField = $_POST["Ocdla_Occupation_Field_Type__c"];

        $search = new Template("expert-search");
        $search->addPath(__DIR__ . "/templates");

        $primaryFields = $this->getPrimaryFields();

        $expertQuery = $this->buildDirectoryQuery(array("only-experts" => true));

        $expertRecords = $this->getExperts($expertQuery);

        $experts = Contact::from_query_result_records($expertRecords);

        if(!empty($selectedPrimaryField) && $selectedPrimaryField != "All Primary Fields"){

            $experts = $this->filterOnPrimaryField($experts, $selectedPrimaryField);
        }


        $tpl = new Template("expert-list");
        $tpl->addPath(__DIR__ . "/templates");

        return $tpl->render(array(
            "search"    =>  $search->render(array("primaryFields" => $primaryFields, "selectedPrimaryField" => $selectedPrimaryField)),
            "experts"   =>  $experts,
            "count"     =>  count($experts),
            "query"     =>  $expertQuery 
        ));
    }

    public function getPrimaryFields(){

        $endpoint = "/services/data/v23.0/sobjects/Contact/describe";
        $api = $this->loadForceApi();
        $resp = $api->send($endpoint);
        $primaryFieldObjs = $resp->getBody()["fields"];

        $primaryFields = array();

        foreach($primaryFieldObjs as $field){

            if($field["name"] == "Ocdla_Expert_Witness_Primary__c"){

                $values = $field["picklistValues"];

                foreach($values as $value){

                    $primaryFields[] = $value["value"];
                }
            }
        }

        return $primaryFields;
    }

    public function getExperts($query){

        
        $api = $this->loadForceApi();
        $resp = $api->query($query);

        if(!$resp->isSuccess()) throw new Exception($resp->getErrorMessage());

        return $resp->getRecords();
    }

    public function filterOnPrimaryField($contacts, $selectedPrimaryField){

        $tmp = array();

        foreach($contacts as $c){

            $primaryFields = $c->getPrimaryFields(True);

            if(in_array($selectedPrimaryField, $primaryFields)) {

                $tmp[] = $c;

            }
        }

        return $tmp;
    }
}