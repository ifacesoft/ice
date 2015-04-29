<h1><?= \Ice\Core\Route::getInstance('ice_admin_database')->getResource()->get('ice_admin_database') ?></h1>

<?= $dataSourceKeysMenu ?>

<h2><?= $resource['Admin_Database']->get('tables') ?></h2>

<div class="row">
    <div class="col-md-2"><?= $Admin_Database_TablesMenu[0] ?></div>
    <div class="col-md-10"><?php if (isset($Crud)) : ?><?= $Crud[0] ?><?php endif; ?></div>
</div>

