
<div class="directory-container">

<div class="search">
    <?php print $search; ?>
</div>


<h1 style="text-align:center; margin-bottom:10px;">SEARCH RESULTS</h1>

<?php if($showQuery) : ?>
    <div>
        <p><?php print $query; ?></p>
    </div>
<?php endif; ?>

<div>
    <br />
    <p><?php print "Showing $count results."; ?></p>
    <br />
</div>

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
            <p><?php !empty($c->getPhone()) ? print $c->getPhone() : print "<br />"; ?></p>
            <p><?php !empty($c->getMailingCity()) ? print $c->getMailingCity() . ", " . $c->getMailingState() : print "City Not Listed"; ?></p>
            <?php !empty($c->getEmail()) ? print "<a href='mailto: {$c->getEmail()}' style='text-decoration:none;'>{$c->getEmail()}</a>" : print "No Email Available"; ?>

            <?php if(!empty($c->getAreasOfInterest())) : ?>
                <p style="text-decoration:underline;">
                    <strong>
                        Areas of Interest
                    </strong>
                </p>
                
                <p><?php print $c->getAreasOfInterest(); ?></p>
            <?php endif; ?>
        </div>

    <?php endforeach; ?>

</div>

</div>

<script src="<?php print module_path(); ?>/assets/js/directory.js"></script>