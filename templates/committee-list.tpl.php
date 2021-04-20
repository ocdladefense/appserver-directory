<style type="text/css">
.table-headers {
    display: none;
}

.table-header,
h2,
h5,
#intro {
    padding-left: 16px;
}

li.table-cell {
    list-style: none;
    padding-right: 15px;
    padding-left: 15px;
}

h2 {
    margin-top: 36px;
    margin-bottom: 16px;
}

@media screen and (min-width: 800px) {
    .table-headers {
        display: table-row;
    }
}
</style>


<div>
    <h2>OCDLA Committees</h2>
</div>

<div id="intro">
    <p>Welcome to OCDLA's Committies Page. Below is the list of all the committees and their respective members.</p>
    <p>You may navigate to a member's contact information by clicking on a specific memeber's link associated </p>
    <p>with the committee of interest.</p>
</div>

<?php if (!isset($committees) || (isset($committees) && count($committees) < 1)) : ?>
<ul class="table-row">
    <li>There are no committees to display.</li>
</ul>

<?php else : ?>

<?php foreach ($committees as $committee) : ?>

<h2><?php print $committee["Name"]; ?></h2>
<div class="table">
    <tbody>
        <ul class="table-row">
            <li class="table-header">Name</li>
            <li class="table-header">Role</li>
            <li class="table-header">Phone</li>
            <li class="table-header">Email</li>
        </ul>
        <?php $members = $committee["members"]; ?>
        <?php foreach ($members as $member) : ?>
        <ul class="table-row">
            <li class="table-cell cart-middle">
                <?php print $member["Title"] . " " . $member["Name"]; ?>
            </li>
            <li class="table-cell cart-middle">
                <?php print $member["Role"]; ?>
            </li>
            <li class="table-cell cart-middle">
                <?php print $member["Phone"]; ?>
            </li>
            <li class="table-cell cart-middle">
                <?php print $member["Email"]; ?>
            </li>
        </ul>
        <?php endforeach; ?>
    </tbody>
</div>

<?php endforeach; ?>
<?php endif; ?>