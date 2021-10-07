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








        $conditionGroup = array(
            "op" => "AND",
            "conditions" => array(
                array(
                    "fieldname"  => "Ocdla_Current_Member_Flag__c",
                    "op"         => "=",
                    "syntax"     => "%s"
                ),
                array(
                    "fieldname"  => "FirstName",
                    "op"         => "LIKE",
                    "syntax"     => "'%%%s%%'"
                ),
                array(
                    "fieldname"  => "LastName",
                    "op"         => "LIKE",
                    "syntax"     => "'%%%s%%'"
                ),
                array(
                    "fieldname"  => "Ocdla_Organization__c",
                    "op"         => "LIKE",
                    "syntax"     => "'%%%s%%'"
                ),
                array(
                    "fieldname"  => "MailingCity",
                    "op"         => "LIKE",
                    "syntax"     => "'%%%s%%'"
                ),
                array(
                    "fieldname"  => "Ocdla_Occupation_Field_Type__c",
                    "op"         => "LIKE",
                    "syntax"     => "'%%%s%%'"
                ),
                array(
                    "fieldname"  => "Ocdla_Is_Expert_Witness__c",
                    "op"         => "=",
                    "syntax"     => "%s"
                )
                
            )
        );


        $query = "SELECT Id, FirstName, LastName, MailingCity, Ocdla_Current_Member_Flag__c, MailingState, Phone, Email, Ocdla_Occupation_Field_Type__c, Ocdla_Organization__c, (SELECT Interest__c FROM AreasOfInterest__r) FROM Contact";

        $queryBuilder = new SoqlQueryBuilder($query);

        $conditions = $queryBuilder->mergeValues($conditionGroup, $_POST);

        $queryBuilder->setConditions($conditions);
        $queryBuilder->setOrderBy("LastName");

        if(!empty($areaOfInterest)) $queryBuilder->addCondition(" AND Id IN (SELECT Contact__c FROM AreaOfInterest__c WHERE Interest__c = '$areaOfInterest')");

        $query = $queryBuilder->compile();


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

        $conditionGroup = array(
            "op" => "AND",
            "conditions" => array(
                array(
                    "fieldname"  => "FirstName",
                    "op"         => "LIKE",
                    "syntax"     => "'%%%s%%'"
                ),
                array(
                    "fieldname"  => "LastName",
                    "op"         => "LIKE",
                    "syntax"     => "'%%%s%%'"
                ),
                array(
                    "fieldname"  => "Ocdla_Organization__c",
                    "op"         => "LIKE",
                    "syntax"     => "'%%%s%%'"
                ),
                array(
                    "fieldname"  => "MailingCity",
                    "op"         => "LIKE",
                    "syntax"     => "'%%%s%%'"
                ),
                array(
                    "fieldname"  => "Ocdla_Expert_Witness_Primary__c",
                    "op"         => null,
                    "syntax"     => "INCLUDES('%s')"
                ),
                array(
                    "fieldname"  => "Ocdla_Is_Expert_Witness__c",
                    "op"         => "=",
                    "syntax"     => "%s"
                )
            )
        );

        $query = "SELECT Id, FirstName, LastName, MailingCity, Ocdla_Current_Member_Flag__c, MailingState, Phone, Email, Ocdla_Occupation_Field_Type__c, Ocdla_Organization__c, Ocdla_Expert_Witness_Other_Areas__c, Ocdla_Expert_Witness_Primary__c FROM Contact";

        $queryBuilder = new SoqlQueryBuilder($query);
        $conditions = $queryBuilder->mergeValues($conditionGroup, $_POST);
        $queryBuilder->setConditions($conditions);
        $queryBuilder->setOrderBy("LastName");
        $query = $queryBuilder->compile();


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