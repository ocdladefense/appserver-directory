<?php

use function Session\get_current_user;

class DirectoryModule extends Module {

    public function __construct() {

        parent::__construct();
    }


    /* #region Member Directory */

    public function showMemberDirectory(){

        $_POST["Ocdla_Current_Member_Flag__c"] = True;

        $query = "SELECT Id, FirstName, LastName, MailingCity, Ocdla_Current_Member_Flag__c, MailingState, Phone, Email, Ocdla_Occupation_Field_Type__c, Ocdla_Organization__c, (SELECT Interest__c from AreasOfInterest__r) FROM Contact";

        $whereClause = $this->buildMemberWhereClause();

        if($whereClause != null) $query .= $whereClause;

        $query .= " ORDER BY LastName";

        $api = $this->loadForceApi();

        $result = $api->query($query);

        if(!$result->success()) throw new Exception($result->getErrorMessage());

        $records = $result->getRecords();

        $contacts = Contact::from_query_result_records($records);


        $tpl = new Template("member-list");
        $tpl->addPath(__DIR__ . "/templates");

        $searchBar = $this->getMemberSearchBar($params);

        return $tpl->render(array(
            "count"             => count($contacts),
            "search"            => $searchBar,
            "contacts"          => $contacts,
            "query"             => $query,
            "user"              => get_current_user()
        ));
    }

    public function buildMemberWhereClause(){

        $fieldSyntaxes = array(
          "FirstName"                     => "LIKE '%%%s%%'",
          "LastName"                      => "LIKE '%%%s%%'",
          "Ocdla_Organization__c"         => "LIKE '%%%s%%'",
          "MailingCity"                   => "LIKE '%%%s%%'",
          "Ocdla_Occupation_Field_Type__c"=> "LIKE '%%%s%%'",
          "Ocdla_Current_Member_Flag__c"  => "= True"  
        );

        $postFieldsWithValues = array_filter($_POST);

        if(empty($postFieldsWithValues)) return null;

        $conditions = array();
        foreach($postFieldsWithValues as $field => $value){

            $syntax = sprintf($fieldSyntaxes[$field], $postFieldsWithValues[$field]);
            $formatted = $field . " " . $syntax;
            $conditions[] = $formatted;
        }

        $clause = " WHERE " . implode(" AND ", $conditions);

        return $clause;
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
            "query"             => $query,
            "user"              => get_current_user()
        ));


    }

    public function getMemberSearchBar($params){

        $search = new Template("member-search");
        $search->addPath(__DIR__ . "/templates");

        

        return $search->render(array(
            "occupationFields"   => $this->getOccupationFieldsDistinct(),
            "selectedOccupation" => $_POST["Ocdla_Occupation_Field_Type__c"],
            "areasOfInterest"    => $this->getAreasOfInterest(),
            "selectedInterest"   => $_POST["areaOfInterest"],
            "firstName"          => $_POST["FirstName"],
            "lastName"           => $_POST["LastName"],
            "companyName"        => $_POST["Ocdla_Organization__c"],
            "city"               => $_POST["MailingCity"],
            "includeExperts"     => $_POST["IncludeExperts"]
        ));
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

            $areas[$area] = $area;
        }

        //var_dump($areas);exit;

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

            $valueName = $value["valueName"];

            $areasOfInterest[$valueName] = $valueName;
        }

        return $areasOfInterest;
    }


    /* #endregion */


    /* #region Expert Witness Directory */
    public function showExpertDirectory(){

        $_POST["Ocdla_Is_Expert_Witness__c"] = True;

        $query = "SELECT Id, FirstName, LastName, MailingCity, Ocdla_Current_Member_Flag__c, MailingState, Phone, Email, Ocdla_Occupation_Field_Type__c, Ocdla_Organization__c, Ocdla_Expert_Witness_Other_Areas__c, Ocdla_Expert_Witness_Primary__c FROM Contact";

        $whereClause = $this->buildExpertWhereClause();

        if($whereClause != null) $query .= $whereClause;

        $query .= " ORDER BY LastName";

        $api = $this->loadForceApi();
        $resp = $api->query($query);

        if(!$resp->isSuccess()) throw new Exception($resp->getErrorMessage());

        $records = $resp->getRecords();
        $experts = Contact::from_query_result_records($records);


        $tpl = new Template("expert-list");
        $tpl->addPath(__DIR__ . "/templates");

        $search = $this->getExpertWitnessSearchBar($_POST);

        return $tpl->render(array(
            "search"    =>  $search,
            "experts"   =>  $experts,
            "count"     =>  count($experts),
            "query"     =>  $query,
            "user"      =>  get_current_user()
        ));
    }

    public function buildExpertWhereClause(){

        $conditions = array();

        $fields = array(
            "FirstName" => "LIKE '%%%s%%'",
            "LastName" => "LIKE '%%%s%%'",
            "Ocdla_Organization__c" => "LIKE '%%%s%%'",
            "MailingCity" => "LIKE '%%%s%%'",
            "Ocdla_Expert_Witness_Primary__c" => "INCLUDES('%s')",
            "Ocdla_Is_Expert_Witness__c" => "= True"
        );

        $fieldsWithValues = array_filter($_POST);

        if(empty($fieldsWithValues)) return null;


        foreach($fieldsWithValues as $field => $value){

            $syntax = $fields[$field];
            $formatted = sprintf($syntax, $value);
            $conditions[] = $field . " " . $formatted;
        }

        $clause = " WHERE " . implode(" AND ", $conditions);

        return $clause;
    }

    public function getExpertWitnessSearchBar(){

        $witnessPrimaryField = $this->getContactField("Ocdla_Expert_Witness_Primary__c");

        $primaryFieldPicklistValues = $this->getPicklistValues($witnessPrimaryField);

        $search = new Template("expert-search");
        $search->addPath(__DIR__ . "/templates");


        return $search->render(array(
            "firstName"     => $_POST["FirstName"],
            "lastName"      => $_POST["LastName"],
            "companyName"   => $_POST["Ocdla_Organization__c"],
            "city"          => $_POST["MailingCity"],
            "primaryFields" => $primaryFieldPicklistValues,
            "selectedPrimaryField" => $_POST["Ocdla_Expert_Witness_Primary__c"]
        ));
    }


    public function getContactField($fieldName){

        $endpoint = "/services/data/v23.0/sobjects/Contact/describe";
        $api = $this->loadForceApi();
        $resp = $api->send($endpoint);
        $fields = $resp->getBody()["fields"];

        foreach($fields as $field){

            if($field["name"] == $fieldName){

                return $field;
            }
        }

        return null;
    }

    public function getPicklistValues($field){

        $pValues = array();

        $pickListValues = $field["picklistValues"];

        foreach($pickListValues as $value){

            $pValues[$value["value"]] = $value["label"];
        }

        return $pValues;
    }

    public function showExpertSingle($id){

        $query = "SELECT Id, FirstName, LastName, MailingCity, Ocdla_Current_Member_Flag__c, MailingState, Phone, Email, Ocdla_Occupation_Field_Type__c, Ocdla_Organization__c, Ocdla_Expert_Witness_Other_Areas__c, Ocdla_Expert_Witness_Primary__c FROM Contact WHERE Id = '$id'";

        $api = $this->loadForceApi();

        $resp = $api->query($query);

        if(!$resp->IsSuccess()) throw new Exception($resp->getErrorMessage());

        $records = $resp->getRecords();
        $experts = Contact::from_query_result_records($records);


        $tpl = new Template("expert-single");
        $tpl->addPath(__DIR__ . "/templates");

        return $tpl->render(array(
            "experts"           => $experts,
            "isSingle"          => true,
            "query"             => $query,
            "user"              => get_current_user()
        ));
    }

    /* #endregion */
}