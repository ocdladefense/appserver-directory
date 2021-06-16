
<div class="directory-container">

    <div class="search">
        <?php print $search; ?>
    </div>

    <div class="directory-list">

        <?php foreach($contacts as $c) : ?>

            <div class="list-item">
                <?php print $c->getFirstName() . " " . $c->getLastName(); ?>
                <br />
                <strong><?php print $c->getOccupationFieldType(); ?></strong>
                <br />
                <?php print $c->getOcdlaOrganization(); ?>
                <br />
                <?php !empty($c->getMailingCity()) ? print $c->getMailingCity() : print "City Not Listed"; ?>
            </div>

        <?php endforeach; ?>

    </div>

</div>