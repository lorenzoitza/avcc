<?php if (!$isAjax): ?>
    <?php $view->extend('FOSUserBundle::layout.html.php') ?>
    <?php $view['slots']->start('body') ?>
    <div class="grid">
        <div class="row" id="recordsContainer">
        <?php endif; ?>
        <div class="span3">
            <?php echo $view->render('ApplicationFrontBundle::Records/_facets.html.php', array('facets' => $facets)) ?>
        </div>
        <div class="span11">
            <div id="div-select-all-records" style="display:none;"></div>
            <div class="clearfix"></div>
            <div class="button-dropdown place-left">
                <button class="dropdown-toggle">Operations</button>
                <ul class="dropdown-menu" data-role="dropdown">

                    <li>
                        <a class="dropdown-toggle" href="#">Add Record</a>
                        <ul class="dropdown-menu" data-role="dropdown">
                            <li><a  href="<?php echo $view['router']->generate('record_new') ?>">Audio</a></li>
                            <li> <a  href="<?php echo $view['router']->generate('record_film_new') ?>">Film</a></li>
                            <li><a  href="<?php echo $view['router']->generate('record_video_new') ?>">Video</a></li>
                        </ul>
                    </li>
                    <li>
                        <a class="dropdown-toggle" href="#">Export</a>
                        <ul class="dropdown-menu" data-role="dropdown">
                            <li><a href="javascript://" class="export" data-type="csv">CSV</a></li>
                            <li><a href="javascript://" class="export" data-type="xlsx">XLSX</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">Import</a>
                    </li>
                    <li>
                        <a href="#">Bulk Edit</a>
                    </li>
                </ul>
            </div>
<?php echo $view->render('ApplicationFrontBundle::Records/_modal.html.php') ?>
            <div class="table-responsive">
                <table class="table hovered bordered" id="records">
                    <thead>
                        <tr>
                            <?php
                            foreach ($columns as $column => $value) {
                                ?>
                                <?php
                                if ($column == 'checkbox_Col') {
                                    ?>
                                    <th id="<?php echo $value ?>"><input type="checkbox" name="selectAll" id="selectAll" /></th>
                                        <?php
                                    } else {
                                        ?>
                                    <th id="<?php echo $value ?>"><?php echo str_replace('_', ' ', $column) ?></th>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <input type="hidden" name="selectedrecords" id="selectedrecords" value="" />
            <input type="hidden" name="exportType" id="exportType" value="" />
        </div>
        <?php if (!$isAjax): ?>
            <?php $view['slots']->start('view_javascripts') ?>

            <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.4/js/jquery.dataTables.js"></script>
            <script type="text/javascript" src="<?php echo $view['assets']->getUrl('js/records.js') ?>"></script>
            <script type="text/javascript" src="<?php echo $view['assets']->getUrl('js/tristate-0.9.2.js') ?>"></script>
            <script type="text/javascript" src="<?php echo $view['assets']->getUrl('js/jquery.blockUI.js') ?>"></script>
            <script type="text/javascript">

                var record = new Records();
                record.setAjaxSource('<?php echo $view['router']->generate('record_dataTable') ?>');
                record.setAjaxSaveStateUrl('<?php echo $view['router']->generate('record_saveState') ?>');
                record.initDataTable();
                record.setAjaxExportUrl('<?php echo $view['router']->generate('record_export') ?>');
                record.setPageUrl('<?php echo $view['router']->generate('record_list') ?>');
                record.bindEvents();

            </script>
            <?php
            $view['slots']->stop();
            ?>
        </div>
    </div>
    <?php
    $view['slots']->stop();

endif;
?>
