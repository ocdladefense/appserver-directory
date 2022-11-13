<link rel="stylesheet" type="text/css" href="<?php print module_path(); ?>/assets/css/directory.css"></link>



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
</style>



<div class="search">
    <?php print $search; ?>
</div>





<?php if($user->isAdmin()) : ?>
    <div>
        <p><?php print $query; ?></p>
    </div>
<?php endif; ?>


<?php if(empty($c)) : ?>
    <h1 style="text-align:center;">We couldn't find that member...</h1>
<?php endif; ?>

<?php $isSingle ? $singleClass = "is-single" : ""; ?>








<div class="list-item <?php print $singleClass; ?>" data-contactId="<?php print $c->getId(); ?>">
    <h1>
        <?php if(!empty($c->getFirstName())): ?>
            <?php print $c->getFirstName() . " " . $c->getLastName(); ?>
        <?php endif; ?>
    </h1>

    <p class="secondary">
        <?php !empty($c->getOccupationFieldType()) ? print $c->getOccupationFieldType() : print "<br />"; ?>
    </p>
    
    <p>
        <?php !empty($c->getOcdlaOrganization()) ? print $c->getOcdlaOrganization() : print "<br />"; ?>
    </p>

    <a href="tel:<?php print $c->getPhoneNumericOnly(); ?>">
        <?php print $c->getPhone(); ?>
    </a>

    <p>
        <?php !empty($c->getMailingCity()) ? print $c->getMailingCity() . ", " . $c->getMailingState() : print "City Not Listed"; ?>
    </p>

    <?php !empty($c->getEmail()) ? print "<a href='mailto: {$c->getEmail()}' style='text-decoration:none;'>{$c->getEmail()}</a>" : print "No Email Available"; ?>

    
    <?php if(!empty($c->getAreasOfInterest())) : ?>
        <p>
            <strong>
                Areas of Interest:
            </strong>
        </p>
        
        <p><?php print $c->getAreasOfInterest(); ?></p>
    <?php endif; ?>

</div>



