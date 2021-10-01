<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/directory.css"></link>

<div class="directory-container">

<a class="back-link" href="/directory/members" style="float: left;"><i class="fa fa-arrow-left" style="font-size:25px;color:blue"></i></a><br /><br />


<h1 style="text-align:center; margin-bottom:10px;">OCDLA Member</h1>

<?php if($user->isAdmin()) : ?>
    <div>
        <p><?php print $query; ?></p>
    </div>
<?php endif; ?>

<br />
<br />

<?php if(empty($contacts)) : ?>
    <h1 style="text-align:center;">Couldn't find anyone using those search parameters......</h1>
<?php endif; ?>

<?php $isSingle ? $singleClass = "is-single" : ""; ?>

<div class="directory-list">

    <?php foreach($contacts as $c) : ?>

        <div class="list-item <?php print $singleClass; ?>" data-contactId="<?php print $c->getId(); ?>">
            <p class="primary"><?php !empty($c->getFirstName()) ? print $c->getFirstName() . " " . $c->getLastName() : print "<br />";  ?></p>
            <p class="secondary"><?php !empty($c->getOccupationFieldType()) ? print $c->getOccupationFieldType() : print "<br />"; ?></p>
            <p><?php !empty($c->getOcdlaOrganization()) ? print $c->getOcdlaOrganization() : print "<br />"; ?></p>
            <a href="tel:<?php print $c->getPhoneNumericOnly(); ?>"><?php print $c->getPhone(); ?></a>
            <p><?php !empty($c->getMailingCity()) ? print $c->getMailingCity() . ", " . $c->getMailingState() : print "City Not Listed"; ?></p>
            <?php !empty($c->getEmail()) ? print "<a href='mailto: {$c->getEmail()}' style='text-decoration:none;'>{$c->getEmail()}</a>" : print "No Email Available"; ?>

            <br /><br />
            
            <?php if(!empty($c->getAreasOfInterest())) : ?>
                <p>
                    <strong>
                        Areas of Interest:
                    </strong>
                </p>
                
                <p><?php print $c->getAreasOfInterest(); ?></p>
            <?php endif; ?>
        </div>

    <?php endforeach; ?>

</div>

</div>

<script src="<?php print module_path(); ?>/assets/js/directory.js"></script>