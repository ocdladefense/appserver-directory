<?php

use function Session\get_current_user;
use Salesforce\ApiHelper;

class DirectoryModule extends Module {

    public function __construct() {

        parent::__construct();
    }


    /* #region Member Directory */

    public function showMemberDirectory(){

        $_POST["Ocdla_Current_Member_Flag__c"] = True;
        $includeExperts = True;
        
        if(empty($_POST["IncludeExperts"])){

            $_POST["Ocdla_Is_Expert_Witness__c"] = False;
            $includeExperts = False;
        }

        $areaOfInterest = $_POST["areaOfInterest"];
        unset($_POST["areaOfInterest"]);
        unset($_POST["IncludeExperts"]);


        $fieldSyntaxes = array(
          "FirstName"                     => "LIKE '%%%s%%'",
          "LastName"                      => "LIKE '%%%s%%'",
          "Ocdla_Organization__c"         => "LIKE '%%%s%%'",
          "MailingCity"                   => "LIKE '%%%s%%'",
          "Ocdla_Occupation_Field_Type__c"=> "LIKE '%%%s%%'",
          "Ocdla_Current_Member_Flag__c"  => "= %s",
          "Ocdla_Is_Expert_Witness__c"    => "= %s"
        );

        $query = "SELECT Id, FirstName, LastName, MailingCity, Ocdla_Current_Member_Flag__c, MailingState, Phone, Email, Ocdla_Occupation_Field_Type__c, Ocdla_Organization__c, (SELECT Interest__c from AreasOfInterest__r) FROM Contact";

        $conditions = ApiHelper::getSoqlConditions($_POST, $fieldSyntaxes);

        if(!empty($areaOfInterest)) {
            $conditions[] = "id IN (SELECT Contact__c FROM AreaOfInterest__c WHERE Interest__c = '$areaOfInterest')";
        }

        //var_dump($conditions);exit;

        $query .= " WHERE " . implode(" AND ", $conditions) . " ORDER BY LastName";

        $api = $this->loadForceApi();
        $result = $api->query($query);

        if(!$result->success()) throw new Exception($result->getErrorMessage());

        $records = $result->getRecords();

        $contacts = Contact::from_query_result_records($records);


        $tpl = new Template("member-list");
        $tpl->addPath(__DIR__ . "/templates");

        $searchBar = $this->getMemberSearchBar($params, $areaOfInterest, $includeExperts);

        return $tpl->render(array(
            "count"             => count($contacts),
            "search"            => $searchBar,
            "contacts"          => $contacts,
            "query"             => $query,
            "user"              => get_current_user()
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
            "query"             => $query,
            "user"              => get_current_user()
        ));


    }

    public function getMemberSearchBar($params, $areaOfInterest, $includeExperts){

        $api = $this->loadForceApi();

        //$areasOfInterestPicklistId = "0Nt5b000000CbzK";
        $areasOfInterestPicklistId = $api->getGlobalValueSetIdByDeveloperName("AOI");
        $areasOfInterest = $api->getGlobalValueSetNames($areasOfInterestPicklistId);


        $occupationFieldData = $api->getsObjectField("Contact", "Ocdla_Occupation_Field_Type__c");
        $occupationFieldsList = ApiHelper::getPicklistFieldValues($occupationFieldData);

        // $list = $api->getSoqlDistinctFieldValues("Contact", "LastName", True);
        // var_dump($list);exit;

        $search = new Template("member-search");
        $search->addPath(__DIR__ . "/templates");

        return $search->render(array(
            "occupationFields"   => $occupationFieldsList,
            "selectedOccupation" => $_POST["Ocdla_Occupation_Field_Type__c"],
            "areasOfInterest"    => $areasOfInterest,
            "selectedInterest"   => $areaOfInterest,
            "firstName"          => $_POST["FirstName"],
            "lastName"           => $_POST["LastName"],
            "companyName"        => $_POST["Ocdla_Organization__c"],
            "city"               => $_POST["MailingCity"],
            "includeExperts"     => $includeExperts
        ));
    }


    /* #endregion */


    /* #region Expert Witness Directory */
    public function showExpertDirectory(){

        $_POST["Ocdla_Is_Expert_Witness__c"] = True;

        $fields = array(
            "FirstName" => "LIKE '%%%s%%'",
            "LastName" => "LIKE '%%%s%%'",
            "Ocdla_Organization__c" => "LIKE '%%%s%%'",
            "MailingCity" => "LIKE '%%%s%%'",
            "Ocdla_Expert_Witness_Primary__c" => "INCLUDES('%s')",
            "Ocdla_Is_Expert_Witness__c" => "= %s"
        );

        $query = "SELECT Id, FirstName, LastName, MailingCity, Ocdla_Current_Member_Flag__c, MailingState, Phone, Email, Ocdla_Occupation_Field_Type__c, Ocdla_Organization__c, Ocdla_Expert_Witness_Other_Areas__c, Ocdla_Expert_Witness_Primary__c FROM Contact";

        $conditions = ApiHelper::getSoqlConditions($_POST, $fields);

        $query .= (" WHERE " . implode(" AND ", $conditions) . " ORDER BY LastName");

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

    public function showExpertSingle($id){

        $query = "SELECT Id, FirstName, LastName, MailingCity, Ocdla_Current_Member_Flag__c, MailingState, Phone, Email, Ocdla_Occupation_Field_Type__c, Ocdla_Organization__c, Ocdla_Expert_Witness_Other_Areas__c, Ocdla_Expert_Witness_Primary__c FROM Contact WHERE Id = '$id'";

        $api = $this->loadForceApi();

        $resp = $api->query($query);

        if(!$resp->IsSuccess()) throw new Exception($resp->getErrorMessage());

        $records = $resp->getRecords();
        $experts = Contact::from_query_result_records($records);


        $tpl = new Template("expert-single");
        $tpl->addPath(__DIR__ . "/templates");

        $search = $this->getExpertWitnessSearchBar($_POST);

        return $tpl->render(array(
            "search"            => $search,
            "experts"           => $experts,
            "isSingle"          => true,
            "query"             => $query,
            "user"              => get_current_user()
        ));
    }

    public function getExpertWitnessSearchBar(){

        $api = $this->loadForceApi();

        $witnessPrimaryFieldMeta= $api->getSobjectField("Contact", "Ocdla_Expert_Witness_Primary__c");

        $primaryFieldPicklistValues = ApiHelper::getPicklistFieldValues($witnessPrimaryFieldMeta);

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

    /* #endregion */
}