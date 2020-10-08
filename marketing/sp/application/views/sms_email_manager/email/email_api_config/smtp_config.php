<?php include("application/views/sms_email_manager/email/email_section_global_js.php"); ?>
<style>.bbw{border-bottom-width: thin !important;border-bottom:solid .5px #f9f9f9 !important;padding-bottom:20px;}</style>
<section class="section section_custom">
	<div class="section-header">
		<h1><i class="fas fa-plug"></i> <?php echo $page_title; ?></h1>
		<div class="section-header-button">
			<a class="btn btn-primary new_smtp" href="#">
				<i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("New SMTP API"); ?>
			</a> 
		</div>
		<div class="section-header-breadcrumb">
			<div class="breadcrumb-item"><a href="<?php echo base_url("messenger_bot_broadcast"); ?>"><?php echo $this->lang->line("Broadcasting"); ?></a></div>
			<div class="breadcrumb-item"><?php echo $page_title; ?></div>
		</div>
	</div>

	<div class="section-body">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body data-card">
						<div class="table-responsive2">
							<table class="table table-bordered" id="mytable1">
								<thead>
									<tr>
										<th>#</th>
										<th><?php echo $this->lang->line("ID"); ?></th>      
										<th><?php echo $this->lang->line("Email"); ?></th>
										<th><?php echo $this->lang->line("SMTP Host"); ?></th>
										<th><?php echo $this->lang->line("SMTP Port"); ?></th>
										<th><?php echo $this->lang->line("SMTP Username"); ?></th>
										<th><?php echo $this->lang->line("SMTP Password"); ?></th>
										<th><?php echo $this->lang->line("SMTP Type"); ?></th>
										<th><?php echo $this->lang->line("Status"); ?></th>
										<th><?php echo $this->lang->line("Actions"); ?></th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>             
					</div>
				</div>
			</div>
		</div>
	</div>
</section>


<div class="modal fade" id="new_smtp_api_form_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bbw">
				<h5 class="modal-title blue"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line('New SMTP API'); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-12">
						<form action="#" method="POST" id="add_new_smtp">
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label><?php echo $this->lang->line('Email Address'); ?></label>
										<input type="text" class="form-control" id="smtp_email" name="smtp_email">
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label><?php echo $this->lang->line('SMTP Host'); ?></label>
										<input type="text" class="form-control" id="smtp_host" name="smtp_host">
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label><?php echo $this->lang->line('SMTP Port'); ?></label>
										<input type="text" class="form-control" id="smtp_port" name="smtp_port">
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label><?php echo $this->lang->line('SMTP Username'); ?></label>
										<input type="text" class="form-control" id="smtp_username" name="smtp_username">
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label><?php echo $this->lang->line('SMTP Password'); ?></label>
										<input type="text" class="form-control" id="smtp_password" name="smtp_password">
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label><?php echo $this->lang->line('SMTP Type'); ?></label>
										<select class="form-control select2" id="smtp_type" name="smtp_type" style="width:100%;">
											<option value=""><?php echo $this->lang->line('Select SMTP Type'); ?></option>
											<option value="Default"><?php echo $this->lang->line('Default'); ?></option>
											<option value="tls"><?php echo $this->lang->line('tls'); ?></option>
											<option value="ssl"><?php echo $this->lang->line('ssl'); ?></option>
										</select>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label><?php echo $this->lang->line('Status'); ?></label><br>
										<label class="custom-switch">
											<input type="checkbox" name="smtp_status" value="1" id="smtp_status" class="custom-switch-input" checked>
											<span class="custom-switch-indicator"></span>
											<span class="custom-switch-description"><?php echo $this->lang->line('Active');?></span>
										</label>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="modal-footer bg-whitesmoke">
				<button type="button" class="btn btn-primary btn-lg" id="save_smtp"><i class="fas fa-save"></i> <?php echo $this->lang->line('Save'); ?></button>
				<button type="button" class="btn btn-light btn-lg" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line('Close'); ?></button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="update_smtp_api_form_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bbw">
				<h5 class="modal-title blue"><i class="fas fa-edit"></i> <?php echo $this->lang->line('Update SMTP API'); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
				<div id="update_form_body">
					
				</div>
			</div>

			<div class="modal-footer bg-whitesmoke">
			    <button type="button" class="btn btn-primary btn-lg" id="update_smtp"><i class="fas fa-edit"></i> <?php echo $this->lang->line('Update'); ?></button>
			    <button type="button" class="btn btn-light btn-lg float-right" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line('Close'); ?></button>
			</div>
		</div>
	</div>
</div>