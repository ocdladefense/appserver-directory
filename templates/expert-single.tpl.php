<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/directory.css"></link>

<a class="back-link" href="/directory/experts" style="float: left;"><i class="fa fa-arrow-left" style="font-size:25px;color:blue"></i></a><br /><br />


<div class="search">
    <?php print $search; ?>
</div>

<div class="directory-container">

<h1 style="text-align:center; margin-bottom:10px;">OCDLA Expert Witness</h1>

<br /><br />

<?php if($user->isAdmin()) : ?>
    <div>
        <p><?php print $query; ?></p>
    </div>
<?php endif; ?>

<?php if(empty($experts)) : ?>
    <h1 style="text-align:center;">Couldn't find anyone using those search parameters......</h1>
<?php endif; ?>

<?php $isSingle ? $singleClass = "is-single" : ""; ?>

<div class="directory-list">

    <?php foreach($experts as $ex) : ?>

        <div class="list-item <?php print $singleClass; ?>" data-contactId="<?php print $ex->getId(); ?>">
            <p class="primary"><?php !empty($ex->getFirstName()) ? print $ex->getFirstName() . " " . $ex->getLastName() : print "<br />";  ?></p>
            <p class="secondary"><?php !empty($ex->getPrimaryFields()) ? print $ex->getPrimaryFields() : print "<br />"; ?></p>
            <p><?php !empty($ex->getOcdlaOrganization()) ? print $ex->getOcdlaOrganization() : print "<br />"; ?></p>
            <a href="tel:<?php print $ex->getPhoneNumericOnly(); ?>"><?php print $ex->getPhone(); ?></a>
            <p><?php !empty($ex->getMailingCity()) ? print $ex->getMailingCity() . ", " . $ex->getMailingState() : print "City Not Listed"; ?></p>
            <?php !empty($ex->getEmail()) ? print "<a href='mailto: {$ex->getEmail()}' style='text-decoration:none;'>{$ex->getEmail()}</a>" : print "No Email Available"; ?>

            <br />
            <br />
            <p><?php print $ex->getExpertWitnessOtherAreas(); ?></p>

            <?php if(!empty($ex->getAreasOfInterest())) : ?>
                <p style="text-decoration:underline;">
                    <strong>
                        Other Areas/Info
                    </strong>
                </p>
                
                <p><?php print $ex->getExpertWitnessOtherAreas(); ?></p>
            <?php endif; ?>
        </div>

    <?php endforeach; ?>

</div>

</div>

<script src="<?php print module_path(); ?>/assets/js/directory.js"></script>