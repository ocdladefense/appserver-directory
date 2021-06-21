
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