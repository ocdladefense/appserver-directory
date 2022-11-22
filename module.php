<?php

use Salesforce\QueryBuilder;
use Salesforce\SObject;



class DirectoryModule extends Module {

    public function __construct() {

        parent::__construct();
    }


    public function home() {

        $tpl = new Template("home");
        $tpl->addPath(__DIR__ . "/templates");

        return $tpl;
    }

    /* #region Member Directory */
    public function showMemberDirectory() {

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
                )
            /*                array(
                    "fieldname"  => "Ocdla_Is_Expert_Witness__c",
                    "op"         => "=",
                    "syntax"     => "%s"
                )
                */
                
            )
        );


        $fields = array("Id", "FirstName", "LastName", "MailingCity","MailingAddress", "Ocdla_Current_Member_Flag__c", "MailingState", "Phone", "Email", "Ocdla_Occupation_Field_Type__c", "Ocdla_Organization__c", "(SELECT Interest__c FROM AreasOfInterest__r)");

        $soql = new QueryBuilder("Contact");
        $soql->setFields($fields);
        $soql->setConditions($conditionGroup, $_POST);
        $soql->setOrderBy("LastName");

        if(!empty($areaOfInterest)) {

            $condition = " Id IN (SELECT Contact__c FROM AreaOfInterest__c WHERE Interest__c = '$areaOfInterest')";
            $soql->addCondition($condition);
        }

 
        $conditions = array_values($soql->getConditions()["conditions"]);
        foreach($conditions as &$c) {
            unset($c["syntax"]);
        }
 
        $query = $soql->compile();

        // print $query;exit;

        $api = loadApi();  
        
        // Uncomment to have the results paged.
        // WARNING: this is not fast and the pager doesn't actually exist,
        // So this feature is experimental.
        // $api->setPageSize(50);
        
        $result = $api->query($query);
        // var_dump($result);exit;
        $records = $result->getRecords();
        $contacts = Contact::fromSObjects($records);

        $metadata = $api->getSobjectMetadata("Contact");
        $sobject = SObject::fromMetadata($metadata);

        $occupations = $sobject->getPicklist("Ocdla_Occupation_Field_Type__c");
        $areasOfInterestPicklistId = $api->getGlobalValueSetIdByDeveloperName("AOI");
        $areasOfInterest = $api->getGlobalValueSetNames($areasOfInterestPicklistId);

        $_POST["areasOfInterest"] = $areasOfInterest;
        $_POST["occupationalFields"] = $occupations;


        if(!isset($contacts) || count($contacts) < 1) {
            $tpl = new Template("no-results");
            $tpl->addPath(__DIR__ . "/templates");

            return $tpl;
        }


        $tpl = new Template("member-list");
        $tpl->addPath(__DIR__ . "/templates");
        
        return $tpl->render(array(
            "count"             => count($contacts),
            "search"            => $this->getMemberSearchBar($_POST),
            "contacts"          => $contacts,
            "query"             => $query,
            "user"              => current_user(),
            "conditions"        => json_encode($conditions)
        ));
    }



    public function showMemberSingle($id){

        $api = loadApi();

        $query = "SELECT Id, FirstName, LastName, MailingCity, Ocdla_Current_Member_Flag__c, MailingState, Phone, Email, Ocdla_Bar_Number__c, Ocdla_Investigator_License_Number__c, Ocdla_Occupation_Field_Type__c, Ocdla_Organization__c, (SELECT Interest__c from AreasOfInterest__r) FROM Contact WHERE Id = '$id'";

        $records = $api->query($query)->getRecords();

        $contacts = Contact::fromSObjects($records);

        $contact = $contacts[0];

        $tpl = new Template("member");
        $tpl->addPath(__DIR__ . "/templates");

        return $tpl->render(array(
            "c"                 => $contact,
            "isSingle"          => true,
            "query"             => $query,
            "user"              => current_user()
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

        $fields = array("Id", "FirstName", "LastName", "MailingCity", "Ocdla_Current_Member_Flag__c", "MailingState", "Phone", "Email", "Ocdla_Occupation_Field_Type__c", "Ocdla_Organization__c", "Ocdla_Expert_Witness_Other_Areas__c", "Ocdla_Expert_Witness_Primary__c");

        $soql = new QueryBuilder("Contact");
        $soql->setFields($fields);
        $soql->setConditions($conditionGroup, $_POST);
        $soql->setOrderBy("LastName");
        $query = $soql->compile();


        $api = $this->loadForceApi();
        $resp = $api->query($query);

        if(!$resp->isSuccess()) throw new Exception($resp->getErrorMessage());

        $records = $resp->getRecords();
        $experts = Contact::fromSObjects($records);


        $metadata = $api->getSobjectMetadata("Contact");
        $sobject = SObject::fromMetadata($metadata);
        $primaryFields = $sobject->getPicklist("Ocdla_Expert_Witness_Primary__c");


        if(!isset($experts) || count($experts) < 1) {
            $tpl = new Template("no-results");
            $tpl->addPath(__DIR__ . "/templates");

            return $tpl;
        }


        $tpl = new Template("expert-list");
        $tpl->addPath(__DIR__ . "/templates");

        return $tpl->render(array(
            "search"    =>  $this->getExpertWitnessSearchBar($_POST, $primaryFields),
            "experts"   =>  $experts,
            "count"     =>  count($experts),
            "query"     =>  $query,
            "user"      =>  current_user()
        ));
    }

    public function showExpertSingle($id){

        $query = "SELECT Id, FirstName, LastName, MailingCity, Ocdla_Current_Member_Flag__c, MailingState, Phone, Email, Ocdla_Occupation_Field_Type__c, Ocdla_Organization__c, Ocdla_Expert_Witness_Other_Areas__c, Ocdla_Expert_Witness_Primary__c FROM Contact WHERE Id = '$id'";

        $api = $this->loadForceApi();

        $resp = $api->query($query);

        if(!$resp->IsSuccess()) throw new Exception($resp->getErrorMessage());

        $records = $resp->getRecords();
        $experts = Contact::fromSObjects($records);

        $expert = $experts[0];

        $tpl = new Template("expert");
        $tpl->addPath(__DIR__ . "/templates");

        return $tpl->render(array(
            "c"                 => $expert,
            "isSingle"          => true,
            "query"             => $query,
            "user"              => current_user()
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



    public function testQuery() {
        $qb = new QueryBuilder("Contact");

        $c1 = new stdClass;
        $c1->field = "LastName";
        $c1->op = "=";
        $c1->value = "Smith";

        $c2 = new stdClass;
        $c2->field = "Is_Current_Member__c";
        $c2->op = "=";
        $c2->value = true;

        $conditions = array($c1,$c2);

        $where = QueryBuilder::toWhere($conditions);

        return $where;
    }
}