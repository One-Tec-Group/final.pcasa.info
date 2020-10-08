<?php
echo message_box('success');
echo message_box('error');
$created = can_action('159', 'created');
$edited = can_action('159', 'edited');
$deleted = can_action('159', 'deleted');
?>
<div class="panel panel-custom">
    <header class="panel-heading "><?= lang('proeprties_type') ?></header>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped ">
                <thead>
                <tr>
                    <th><?= lang('proeprties_type') ?></th>
                    <?php if (!empty($edited) || !empty($deleted)) { ?>
                        <th><?= lang('action') ?></th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <?php
                if (!empty($all_property_type)) {
                    foreach ($all_property_type as $property_type) {
                        $total_proeprty_type = count($this->db->where('property_type_id', $property_type->property_type_id)->get('tbl_properties_types')->result());
                        ?>
                        <tr id="property_type_<?= $property_type->property_type_id?>">
                            <td>
                                <?php
                                $id = $this->uri->segment(5);
                                if (!empty($id) && $id == $property_type->property_type_id) { ?>
                                <form method="post" action="<?= base_url() ?>admin/settings/properties_type/update_property_type/<?php
                                      if (!empty($property_type_info)) {
                                          echo $property_type_info->property_type_id;
                                      }
                                      ?>" class="form-horizontal">
                                    <input type="text" name="property_type" value="<?php
                                    if (!empty($property_type_info)) {
                                        echo $property_type_info->property_type;
                                    }
                                    ?>" class="form-control" placeholder="<?= lang('proeprties_type') ?>" required>

                                <?php } else {
                                    echo $property_type->property_type . '<p class="text-sm text-info m0 p0">' . lang('total') . ' ' . lang('properties') . ' : ' . $total_proeprty_type . '</p>';
                                }
                                ?></td>
                            <?php if (!empty($edited) || !empty($deleted)) { ?>
                                <td>
                                    <?php
                                    $id = $this->uri->segment(5);
                                    if (!empty($id) && $id == $property_type->property_type_id) { ?>
                                        <?= btn_update() ?>
                                        </form>
                                        <?= btn_cancel('admin/settings/properties_type/') ?>
                                    <?php } else {
                                        if (!empty($edited)) { ?>
                                            <?= btn_edit('admin/settings/properties_type/edit_property_type/' . $property_type->property_type_id) ?>
                                        <?php }
                                        if (!empty($deleted)) { ?>
                                            <?php echo ajax_anchor(base_url("admin/settings/delete_property_type/" . $property_type->property_type_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#property_type_" . $property_type->property_type_id)); ?>
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
                    <form method="post" action="<?= base_url() ?>admin/settings/properties_type/update_property_type"
                          class="form-horizontal" data-parsley-validate="" novalidate="">
                        <tr>
                            <td><input type="text" name="property_type" value="" class="form-control"
                                       placeholder="<?= lang('proeprties_type') ?>" required></td>
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
