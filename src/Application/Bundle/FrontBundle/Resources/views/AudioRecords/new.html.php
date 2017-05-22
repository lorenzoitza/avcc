
<?php $view->extend('FOSUserBundle::layout.html.php') ?>
<?php $view['slots']->start('body') ?>

<div class="grid fluid">
    <h1>
        <a href="<?php echo $view['router']->generate('record_list') ?>"><i class="icon-arrow-left-3 smaller"></i> </a>
        New Record - <?php echo ucwords($type) ?>
    </h1>
    <div id="processing">
        <span><b>Processing please wait...</b></span>
    </div>
    <div id="fieldsPanel" style="display:none;">
        <?php echo $view['form']->start($form) ?>
        <?php echo $view['form']->errors($form) ?>
        <span class="has-error text-danger">
            <?php
            if (!empty($allErrors)) {
                echo implode("<br/>", $allErrors);
            }
            ?>
        </span>
        <fieldset>
            <?php echo $view['form']->errors($form) ?>
            <?php // echo $view['form']->widget($form) ?>
            <?php foreach ($fieldSettings[strtolower($type)] as $audioField): ?>
                <?php
                if ($audioField["title"] == "Show Images") {
                    continue;
                }
                $field = explode('.', $audioField['field']);
                if (count($field) == 2) {
                    $index = $field[1];
                } else {
                    $index = $field[0];
                }



                $_style = (count($field) == 2 && ($field[1] == 'isReview' || $field[1] == 'reformattingPriority' || $field[1] == 'digitized' || $field[1] == 'transcription')) ? 'width: 180px;float: left;margin-bottom: 15px;' : 'width: 200px';
                ?>
                <div style="<?php echo ($audioField['hidden']) ? "" : 'display:none;'; ?>" class="col-lg-6 " id="<?php echo (count($field) == 2) ? $field[1] . '_lbl' : $field[0] . '_lbl' ?>" data-view="<?php echo ($audioField['hidden']) ? 'show' : 'hide'; ?>">
                    <div class="label_class" data-toggle="popover" data-placement="bottom" data-content="<?php echo isset($tooltip[$index]) ? $tooltip[$index] : ''; ?>" style="<?php echo $_style ?>">
                        <?php
                        $attr = ($audioField['is_required']) ? array('class' => 'size4') : array('class' => 'size4');
                        echo $view['form']->label((count($field) == 2) ? $form[$field[0]][$field[1]] : $form[$field[0]], ' ');
                        echo $audioField['title'];
                        echo ($audioField['is_required']) ? "&nbsp;<span>*</span>" : "";
                        ?>
                    </div>
                    <?php
                    if (count($field) == 2 && ($field[1] == 'isReview' || $field[1] == 'reformattingPriority' || $field[1] == 'digitized' || $field[1] == 'transcription'))
                        $style = '';
                    else if (count($field) == 2 && $field[1] == 'conditionNote' || count($field) == 2 && $field[1] == 'generalNote' || count($field) == 2 && $field[1] == 'copyrightRestrictions' || count($field) == 2 && $field[1] == 'description')
                        $style = 'textarea';
                    else
                        $style = 'text';
                    ?>
                    <div class="input-control <?php echo $style; ?> new" data-role="input-control">
                        <?php
                        $_attr = (count($field) == 2 && ($field[1] == 'isReview' || $field[1] == 'reformattingPriority' || $field[1] == 'digitized' || $field[1] == 'transcription')) ? array() : $attr;
                        ?>
                        <?php echo $view['form']->widget((count($field) == 2) ? $form[$field[0]][$field[1]] : $form[$field[0]], array('id' => (count($field) == 2) ? $field[1] : $field[0], 'attr' => $_attr)) ?>
                        <span class="has-error text-danger"><?php echo $view['form']->errors((count($field) == 2) ? $form[$field[0]][$field[1]] : $form[$field[0]]) ?></span>
                    </div>
                </div>
                <div class="clearfix"></div>
            <?php endforeach; ?>
        </fieldset><br />
        <?php echo $view['form']->widget($form['record']['userId']) ?>
        <a href="<?php echo $view['router']->generate('record_list') ?>" name="cancle" class="button">Cancel</a>&nbsp;
        <?php echo $view['form']->widget($form['submit'], array('attr' => array('class' => 'button primary'))) ?>&nbsp;
        <?php echo $view['form']->widget($form['save_and_new']) ?>&nbsp;
        <?php echo $view['form']->widget($form['save_and_duplicate']) ?>
        <?php echo $view['form']->end($form) ?>
    </div>
</div>
<script src="<?php echo $view['assets']->getUrl('js/manage.records.js') ?>"></script>
<script type="text/javascript">
    var changes = false;
    var baseUrl = '<?php echo $view['router']->generate('record') ?>';
    var selectedFormat = '<?php echo ($entity->getRecord() && $entity->getRecord()->getFormat()) ? $entity->getRecord()->getFormat()->getId() : ''; ?>';
    var selectedMediaType = 1;
    var selectedFormatVersion = '';
    var viewUrl = baseUrl + 'audio/new/';
    var selectedRS = '<?php echo ($entity->getRecordingSpeed()) ? $entity->getRecordingSpeed()->getId() : ''; ?>';
    var selectedRD = '<?php echo ($entity->getRecord() && $entity->getRecord()->getReelDiameters()) ? $entity->getRecord()->getReelDiameters()->getId() : ''; ?>';
    var selectedProject = '<?php echo ($entity->getRecord() && $entity->getRecord()->getProject()) ? $entity->getRecord()->getProject()->getId() : ''; ?>';
    var projectId = <?php echo ($app->getSession()->get('project_id')) ? $app->getSession()->get('project_id') : 0 ?>;
    var bulk = false;
    $(document).ready(function () {
        initialize_records_form();
        $(function () {
            $('[data-toggle="popover"]').popover();
        });
        hideFields();
        $('#mediaType, #format').change(function () {
            hideFields();
        });

        function hideFields() {
            var fields = ['uniqueId_lbl', 'location_lbl', 'reformattingPriority_lbl', 'alternateId_lbl', 'title_lbl', 'collectionName_lbl', 'description_lbl', 'commercial_lbl', 'diskDiameters_lbl', 'reelDiameters_lbl', 'mediaDiameters_lbl', 'bases_lbl', 'contentDuration_lbl', 'mediaDuration_lbl', 'creationDate_lbl', 'contentDate_lbl', 'isReview_lbl', 'recordingSpeed_lbl', 'tapeThickness_lbl', 'slides_lbl', 'trackTypes_lbl', 'monoStereo_lbl', 'noiceReduction_lbl', 'genreTerms_lbl', 'contributor_lbl', 'part_lbl', 'generation_lbl', 'copyrightRestrictions_lbl', 'duplicatesDerivatives_lbl', 'relatedMaterial_lbl', 'conditionNote_lbl', 'generalNote_lbl', 'parentCollection_lbl', 'digitized_lbl', 'transcription_lbl', 'digitizedBy_lbl', 'digitizedWhen_lbl', 'urn_lbl'];
            if ($('#mediaType').val() == '' || $('#project').val() == '' || $('#format').val() == '') {
                for (i = 0; i < fields.length; i++) {
                    $('#' + fields[i]).hide();
                }
            } else {
                var fields1 = ['uniqueId_lbl', 'alternateId_lbl', 'reformattingPriority_lbl', 'location_lbl', 'title_lbl', 'collectionName_lbl', 'description_lbl', 'commercial_lbl', 'contentDuration_lbl', 'mediaDuration_lbl', 'creationDate_lbl', 'contentDate_lbl', 'isReview_lbl', 'genreTerms_lbl', 'contributor_lbl', 'generation_lbl', 'part_lbl', 'copyrightRestrictions_lbl', 'duplicatesDerivatives_lbl', 'relatedMaterial_lbl', 'conditionNote_lbl', 'generalNote_lbl', 'parentCollection_lbl', 'digitized_lbl', 'transcription_lbl'];
                for (i = 0; i < fields1.length; i++) {
                    if ($('#' + fields1[i]).data('view') == 'show')
                        $('#' + fields1[i]).show();
                }
                showUpdateFields();
            }
        }

    });
</script>
<?php
$view['slots']->stop();
?>
