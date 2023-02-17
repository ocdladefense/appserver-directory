<?php


$aoi = $c->getAreasOfInterest();

// var_dump($c);exit;
?>


<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/directory.css" />



<style type="text/css">
    img.obj {
        vertical-align: middle;
        padding: 15px;
        border: 1px solid #eee;
        margin: 10px;
        border-radius: 5px;
    }

    .record-documents {
        margin-top: 25px;
    }

    h2.section-title {
        border-radius: 9px;
        width: 25%;
        padding: 5px;
        padding-left: 0px;
        font-size: 1.0rem;
        margin-top: 30px;
    }

    .member-status {
        background-color: rgba(85,99,141,1.0);
        color: #fff;
        border-radius: 3px;
        padding: 3px;
        display: inline-block;
    }

    .directory-title {
        margin-top: 25px;
    }


</style>


<div class="search">
    <?php print $search; ?>
</div>





<?php if(false && $user->isAdmin()) : ?>
    <div>
        <?php print $query; ?>
    </div>
<?php endif; ?>


<?php if(empty($c)): ?>
    <h1 style="text-align:center;">We couldn't find that member...</h1>
<?php endif; ?>

<?php $isSingle ? $singleClass = "is-single" : ""; ?>




<div class="breadcrumb">
    <a href="/directory/members">Directory</a> // <a href="/directory/members">OCDLA Members</a> // <?php print $c->FirstName . " " . $c->LastName; ?>
</div>



<div class="list-item <?php print $singleClass; ?>" data-contact-id="<?php print $c->getId(); ?>">

    
    <div class="directory-title">
        <div class="member-status">OCDLA Member</div>
        <h1 class="directory-name">
            <?php print $c->FirstName . " " . $c->LastName; ?>
        </h1>
    </div>

    
    <?php if(!empty($c->Ocdla_Organization__c)): ?>
        <p>
            <?php print $c->Ocdla_Organization__c; ?>
        </p>
    <?php endif; ?>
    


    <section>
        <h2 class="section-title">Occupation</h2>
        <?php if(!empty($c->Ocdla_Occupation_Field_Type__c)): ?>
            <p class="secondary">
                <?php print $c->Ocdla_Occupation_Field_Type__c; ?>
            </p>
        <?php endif; ?>
        <?php if(!empty($c->Ocdla_Bar_Number__c)): ?>
            <div class="row">
                <div class="text cell cell-label">
                    OSB #<?php print $c->Ocdla_Bar_Number__c; ?>
                </div>
            </div>
        <?php endif; ?>
    </section>






    <?php if(!empty($c->Ocdla_Investigator_License_Number__c)): ?>
        <div class="row">
            <div class="text cell cell-label">
                Investigator #<?php print $c->Ocdla_Investigator_License_Number__c; ?>
            </div>
        </div>
    <?php endif; ?>


    
    <section>
        <h2 class="section-title">Location</h2>
        <?php if(!empty($c->MailingCity)): ?>
            <p>
                <?php print $c->MailingCity . ", " . $c->MailingState; ?>
            </p>
        <?php endif; ?>
    </section>


    <section>
        <h2 class="section-title">Contact</h2>
        Phone: <a href="tel:<?php print $c->getPhoneNumericOnly(); ?>">
            <?php print $c->Phone; ?>
        </a>

        <br />
        <?php
         
/*
         <div class="row">
         <div class="text cell cell-label">
             Phone:
         </div>
         <div class="text cell">
             {# phoneNumberFormat( work_phone ) #}
             {% if contact.Ocdla_Publish_Work_Phone__c %}
                 {{ contact.OrderApi__Work_Phone__c }}
             {% else %}
                 This member has chosen not to publish their phone number.
             {% endif %}
         </div>
 
     </div>
     <div class="row">
         <div class="text cell cell-label">
             Cell Phone:
         </div>
         <div class="text cell" width="76%">
             {# phoneNumberFormat(cell_phone) #}
             {% if contact.Ocdla_Publish_Work_Phone__c %}
                 {{ contact.Ocdla_Cell_Phone__c }}
             {% else %}
                 This member has chosen not to publish their cell phone.
             {% endif %}
         </div>
     </div>
     <div class="row">
         <div class="text cell cell-label">
             <font color="000000">Fax:</font>
         </div>
         <div class="text cell">
             {# phoneNumberFormat(fax_number) #}
             {{ contact.Fax }}
         </div>
     </div>

*/

        ?>

        <?php if(!empty($c->Email)): ?>
            Email: <a href='mailto: <?php print $c->Email; ?>' style='text-decoration:none;'>
                <?php print $c->Email; ?>
            </a>
        <?php endif; ?>
    </section>



    <section>
        <h2 class="section-title">Areas of Interest</h2>

        <?php if($c->hasInterests()): ?>
            <p>
                <?php print $c->getAreasOfInterest(); ?>
            </p>
        <?php endif; ?>
    </section>

</div>



