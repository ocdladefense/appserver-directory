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


<?php if(empty($contacts)) : ?>
    <h1 style="text-align:center;">Couldn't find anyone using those search parameters...</h1>
<?php endif; ?>

<?php $isSingle ? $singleClass = "is-single" : ""; ?>






<?php foreach($contacts as $c): ?>

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

<?php endforeach; ?>

<div class="record-documents">
    <form id="contact-uploads" method="post" enctype="multipart/form-data" action="https://appdev.ocdla.org/file/upload">
        <h2>Related documents</h2>
            
        <div class="file-upload" style="border:1px solid #ccc; padding:50px;">
            <i class="fa-solid fa-cloud-arrow-up fa-2x" style="font-size:4.0em; color:blue;"></i>
            <input name="thefiles[]" type="file" id="upload" multiple />
        </div>

        <div class="form-item">
            <input type="submit" value="Upload" />
        </div>

        <div id="preview"></div>

    </form>
</div>




<!--  NOTE: this could be used to display a map locating the member's business address. 
    <script type="module" src="<?php print module_path(); ?>/assets/js/directory.js">
</script> -->
<script src="/node_modules/@ocdladefense/node-file-upload/upload.js">
</script>
