<?php
/**
 * @template expert.tpl.php
 * 
 * @description Template to render an individual expert.
 */

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





<?php if($user->isAdmin()) : ?>
    <div>
        <?php print $query; ?>
    </div>
<?php endif; ?>


<?php if(empty($c)): ?>
    <h1 style="text-align:center;">We couldn't find that member...</h1>
<?php endif; ?>

<?php $isSingle ? $singleClass = "is-single" : ""; ?>




<div class="breadcrumb">
    <a href="/directory/experts">Directory</a> // <a href="/directory/experts">Expert Witnesses</a> // <?php print $c->FirstName . " " . $c->LastName; ?>
</div>



<div class="list-item <?php print $singleClass; ?>" data-contact-id="<?php print $c->getId(); ?>">

    
    <div class="directory-title">
        <!-- <div class="member-status">OCDLA Member</div> -->
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

        <?php if(false && !empty($c->Ocdla_Occupation_Field_Type__c)): ?>
        <h2 class="section-title">Occupation</h2>
        
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

        <?php if(!empty($c->Email)): ?>
            Email: <a href='mailto: <?php print $c->Email; ?>' style='text-decoration:none;'>
                <?php print $c->Email; ?>
            </a>
        <?php endif; ?>
    </section>



    <section>
        <h2 class="section-title">Other Areas of Interest</h2>

        <?php if(!empty($c->Ocdla_Expert_Witness_Other_Areas__c)): ?>
            <p>
                <?php print $c->Ocdla_Expert_Witness_Other_Areas__c; ?>
            </p>
        <?php endif; ?>
    </section>

</div>





<!--
<script src="/node_modules/@ocdladefense/node-file-upload/upload.js">
</script>
-->