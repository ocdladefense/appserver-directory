<?php

use function Session\get_current_user;
use Salesforce\SoqlQueryBuilder;
use Salesforce\SObject;

class DirectoryModule extends Module {

    public function __construct() {

        parent::__construct();
    }


    /* #region Member Directory */

    public function showMemberDirectory(){

        $areaOfInterest = $_POST["areaOfInterest"];
        $_POST["Ocdla_Current_Member_Flag__c"] = True;

        if(empty($_POST["IncludeExperts"])) $_POST["Ocdla_Is_Expert_Witness__c"] = False;

        $fields = array(
          "FirstName"                     => "LIKE '%%%s%%'",
          "LastName"                      => "LIKE '%%%s%%'",
          "Ocdla_Organization__c"         => "LIKE '%%%s%%'",
          "MailingCity"                   => "LIKE '%%%s%%'",
          "Ocdla_Occupation_Field_Type__c"=> "LIKE '%%%s%%'",
          "Ocdla_Current_Member_Flag__c"  => "= %s",
          "Ocdla_Is_Expert_Witness__c"    => "= %s"
        );

        $query = "SELECT Id, FirstName, LastName, MailingCity, Ocdla_Current_Member_Flag__c, MailingState, Phone, Email, Ocdla_Occupation_Field_Type__c, Ocdla_Organization__c, (SELECT Interest__c FROM AreasOfInterest__r) FROM Contact";


        $group = array(
            "op" => "AND",
            "conditions" => array(
                array(
                    "fieldname"  => "FirstName",
                    "value"      => "Trevor",
                    "op"         => " = ",
                    "syntax"     => "%%%s%%'"
                ),
                array(
                    "op" => "OR",
                    "isGroup" => True,
                    "conditions" => array(
                        array(
                            "fieldname"  => "LastName",
                            "value"      => "Uehlin",
                            "op"         => " = ",
                            "syntax"     => "%%%s%%'"
                        ),
                        array(
                            "fieldname"  => "LastName",
                            "value"      => "Johnson",
                            "op"         => " = ",
                            "syntax"     => "%%%s%%'"
                        )
                    )
                )
            )
        );

        SoqlQueryBuilder::buildConditions($group);

        $conditions = SoqlQueryBuilder::getSoqlConditions($_POST, $fields);

        if(!empty($areaOfInterest)) {
            $conditions[] = "Id IN (SELECT Contact__c FROM AreaOfInterest__c WHERE Interest__c = '$areaOfInterest')";
        }

        $query .= " WHERE " . implode(" AND ", $conditions) . " ORDER BY LastName";


        $api = $this->loadForceApi();
        $result = $api->queryAll($query);
        $records = $result->getRecords();
        $contacts = Contact::from_query_result_records($records);

        $metadata = $api->getSobjectMetadata("Contact");
        $sobject = SObject::fromMetadata($metadata);

        $occupations = $sobject->getPicklist("Ocdla_Occupation_Field_Type__c");
        $areasOfInterestPicklistId = $api->getGlobalValueSetIdByDeveloperName("AOI");
        $areasOfInterest = $api->getGlobalValueSetNames($areasOfInterestPicklistId);

        $_POST["areasOfInterest"] = $areasOfInterest;
        $_POST["occupationalFields"] = $occupations;

        $tpl = new Template("member-list");
        $tpl->addPath(__DIR__ . "/templates");

        return $tpl->render(array(
            "count"             => count($contacts),
            "search"            => $this->getMemberSearchBar($_POST),
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

    public function getMemberSearchBar($params){

        $includeExperts = $params["IncludeExperts"] == 1 ? True : False;

        $search = new Template("member-search");
        $search->addPath(__DIR__ . "/templates");

        return $search->render(array(
            "occupationFields"   => $params["occupationalFields"],
            "selectedOccupation" => $params["Ocdla_Occupation_Field_Type__c"],
            "areasOfInterest"    => $params["areasOfInterest"],
            "selectedInterest"   => $params["areaOfInterest"],
            "firstName"          => $params["FirstName"],
            "lastName"           => $params["LastName"],
            "companyName"        => $params["Ocdla_Organization__c"],
            "city"               => $params["MailingCity"],
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

        $conditions = SoqlQueryBuilder::getSoqlConditions($_POST, $fields);

        $query .= (" WHERE " . implode(" AND ", $conditions) . " ORDER BY LastName");

        $api = $this->loadForceApi();
        $resp = $api->query($query);

        if(!$resp->isSuccess()) throw new Exception($resp->getErrorMessage());

        $records = $resp->getRecords();
        $experts = Contact::from_query_result_records($records);


        $metadata = $api->getSobjectMetadata("Contact");
        $sobject = SObject::fromMetadata($metadata);
        $primaryFields = $sobject->getPicklist("Ocdla_Expert_Witness_Primary__c");


        $tpl = new Template("expert-list");
        $tpl->addPath(__DIR__ . "/templates");

        return $tpl->render(array(
            "search"    =>  $this->getExpertWitnessSearchBar($_POST, $primaryFields),
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

        return $tpl->render(array(
            "experts"           => $experts,
            "isSingle"          => true,
            "query"             => $query,
            "user"              => get_current_user()
        ));
    }

    public function getExpertWitnessSearchBar($state, $primaryFields){

        $search = new Template("expert-search");
        $search->addPath(__DIR__ . "/templates");


        return $search->render(array(
            "firstName"     => $state["FirstName"],
            "lastName"      => $state["LastName"],
            "companyName"   => $state["Ocdla_Organization__c"],
            "city"          => $state["MailingCity"],
            "primaryFields" => $primaryFields,
            "selectedPrimaryField" => $state["Ocdla_Expert_Witness_Primary__c"]
        ));
    }

    /* #endregion */
}