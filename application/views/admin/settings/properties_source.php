<?php
echo message_box('success');
echo message_box('error');
$created = can_action('158', 'created');
$edited = can_action('158', 'edited');
$deleted = can_action('158', 'deleted');
?>
<div class="panel panel-custom">
    <header class="panel-heading "><?= lang('proeprties_source') ?></header>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped ">
                <thead>
                <tr>
                    <th><?= lang('proeprties_source') ?></th>
                    <?php if (!empty($edited) || !empty($deleted)) { ?>
                        <th><?= lang('action') ?></th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <?php
                if (!empty($all_property_source)) {
                    foreach ($all_property_source as $property_source) {
                        $total_proeprty_source = count($this->db->where('property_source_id', $property_source->property_source_id)->get('tbl_properties')->result());
                        ?>
                        <tr id="property_source_<?= $property_source->property_source_id?>">
                            <td>
                                <?php
                                $id = $this->uri->segment(5);
                                if (!empty($id) && $id == $property_source->property_source_id) { ?>
                                <form method="post" action="<?= base_url() ?>admin/settings/properties_source/update_property_source/<?php
                                      if (!empty($property_source_info)) {
                                          echo $property_source_info->property_source_id;
                                      }
                                      ?>" class="form-horizontal">
                                    <input type="text" name="property_source" value="<?php
                                    if (!empty($property_source_info)) {
                                        echo $property_source_info->property_source;
                                    }
                                    ?>" class="form-control" placeholder="<?= lang('proeprties_source') ?>" required>

                                <?php } else {
                                    echo $property_source->property_source . '<p class="text-sm text-info m0 p0">' . lang('total') . ' ' . lang('properties') . ' : ' . $total_proeprty_source . '</p>';
                                }
                                ?></td>
                            <?php if (!empty($edited) || !empty($deleted)) { ?>
                                <td>
                                    <?php
                                    $id = $this->uri->segment(5);
                                    if (!empty($id) && $id == $property_source->property_source_id) { ?>
                                        <?= btn_update() ?>
                                        </form>
                                        <?= btn_cancel('admin/settings/properties_source/') ?>
                                    <?php } else {
                                        if (!empty($edited)) { ?>
                                            <?= btn_edit('admin/settings/properties_source/edit_property_source/' . $property_source->property_source_id) ?>
                                        <?php }
                                        if (!empty($deleted)) { ?>
                                            <?php echo ajax_anchor(base_url("admin/settings/delete_property_source/" . $property_source->property_source_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#property_source_" . $property_source->property_source_id)); ?>
                                        <?php }
                                    }
                                    ?>

                                </td>
                            <?php } ?>
                        </tr>
                        <?php
                    }
                }
                if (!empty($created) || !empty($edited)) { ?>
                    <form method="post" action="<?= base_url() ?>admin/settings/properties_source/update_property_source"
                          class="form-horizontal" data-parsley-validate="" novalidate="">
                        <tr>
                            <td><input type="text" name="property_source" value="" class="form-control"
                                       placeholder="<?= lang('proeprties_source') ?>" required></td>
                            <td>
                                <button type="submit" class="btn btn-sm btn-primary"></i> <?= lang('add') ?></button>
                            </td>
                        </tr>
                    </form>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
