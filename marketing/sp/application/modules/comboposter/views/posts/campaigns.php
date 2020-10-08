<section class="section section_custom">
  <div class="section-header">
    <h1><i class="<?php echo $icon; ?>"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
     <a class="btn btn-primary" href="<?php echo base_url("comboposter/". $campaign_type ."_post/create");?>">
        <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Create new Post"); ?>
     </a> 
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('ultrapost'); ?>"><?php echo $this->lang->line("Comboposter"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body data-card">
          	<div class="row">
          		<div class="col-md-9 col-12">
              	<div class="input-group mb-3 float-left" id="searchbox">
  					        <!-- search by page name -->
                    <input type="text" class="form-control" id="searching" name="searching" autofocus placeholder="<?php echo $this->lang->line('Search...'); ?>" aria-label="" aria-describedby="basic-addon2">
  	          	  	<div class="input-group-append">
  	          	    	<button class="btn btn-primary" id="search_submit" title="<?php echo $this->lang->line('Search'); ?>" type="button"><i class="fas fa-search"></i> <span class="d-none d-sm-inline"><?php echo $this->lang->line('Search'); ?></span></button>
  	      	 	 	    </div>
            		</div>
          		</div>
          		<div class="col-md-3 col-12">
          			<a href="javascript:;" id="post_date_range" class="btn btn-primary btn-lg float-right icon-left btn-icon"><i class="fas fa-calendar"></i> <?php echo $this->lang->line("Choose Date");?></a><input type="hidden" id="post_date_range_val">
          		</div>
          	</div>
            <div class="table-responsive2">
            	<table class="table table-bordered" id="mytable">
                <thead>
                	<tr>
      							<th>#</th>      
      							<th><?php echo $this->lang->line("Campaign ID"); ?></th>      
      							<th><?php echo $this->lang->line("Campaign Name"); ?></th>
      							<th><?php echo $this->lang->line("Social Media"); ?></th>
      							<th style="min-width: 150px !important;"><?php echo $this->lang->line("Actions"); ?></th>
      							<th><?php echo $this->lang->line("Status"); ?></th>
      							<th><?php echo $this->lang->line("Scheduled at"); ?></th>
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

<script>
$(document).ready(function($) {

  var base_url = '<?php echo base_url(); ?>';

  setTimeout(function(){ 
    $('#post_date_range').daterangepicker({
      ranges: {
        '<?php echo $this->lang->line("Last 30 Days");?>': [moment().subtract(29, 'days'), moment()],
        '<?php echo $this->lang->line("This Month");?>'  : [moment().startOf('month'), moment().endOf('month')],
        '<?php echo $this->lang->line("Last Month");?>'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      startDate: moment().subtract(29, 'days'),
      endDate  : moment()
    }, function (start, end) {
      $('#post_date_range_val').val(start.format('YYYY-M-D') + '|' + end.format('YYYY-M-D')).change();
    });
  }, 2000);




  // datatable section started
  var perscroll;
  var table = $("#mytable").DataTable({
      serverSide: true,
      processing:true,
      bFilter: false,
      order: [[ 1, "desc" ]],
      pageLength: 10,
      ajax: 
      {
        "url": base_url+'comboposter/campaigns_info_data/' + '<?php echo $campaign_type; ?>',
        "type": 'POST',
  	    data: function ( d )
  	    {
  	        d.searching = $('#searching').val();
  	        d.post_date_range = $('#post_date_range_val').val();
  	    }
      },
      language: 
      {
        url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
      },
      dom: '<"top"f>rt<"bottom"lip><"clear">',
      columnDefs: [
          {
            targets: [1],
            visible: false
          },
          {
          	targets: [0,1,3,4,5,6],
          	className: 'text-center'
          },
          {
          	targets:[0,1,3,4,5,6],
          	sortable: false
          }
      ],
      fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
        if(areWeUsingScroll)
        {
          if (perscroll) perscroll.destroy();
          perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
        }
      },
      scrollX: 'auto',
      fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
        if(areWeUsingScroll)
        { 
          if (perscroll) perscroll.destroy();
          perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
        }
      }
  });


  /* delete campaign */
  $(document).on('click', '.delete_campaign', function(event) {
    event.preventDefault();
    
    swal({
      title: '<?php echo $this->lang->line("Are you sure?"); ?>',
      text: '<?php echo $this->lang->line("Do you really want to delete this campaign? If you delete this campaign it will be deleted from your database."); ?>',
      icon: 'warning',
      buttons: true,
      dangerMode: true,
    })
    .then((willDelete) => {
      if (willDelete) 
      {
        var table_id = $(this).attr('table_id');

        $.ajax({
          context: this,
          type:'POST',
          dataType: 'json',
          url:"<?php echo base_url('comboposter/delete_campaign')?>",
          data:{table_id:table_id},
          success:function(response){ 

            if (response.status == 'success') {
              iziToast.success({title: '', message: response.message, position: 'bottomRight'});
              table.draw();
            } else if (response.status == 'error') {
              iziToast.error({title: '',message: response.message, position: 'bottomRight'});
            }
          }
        });
      } 
    });

  });


  $(document).on('change', '#post_date_range_val', function(event) {
    event.preventDefault(); 
    table.draw();
  });

  $(document).on('click', '#search_submit', function(event) {
    event.preventDefault(); 
    table.draw();
  });
  // End of datatable section




		
});

</script>

<div class="modal fade" id="view_report_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-mega">
        <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><i class="far fa-list-alt"></i> <?php echo $this->lang->line("Report of Text/Image/Link/Video Poster");?></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body data-card">
                <div class="row">
                  <div class="col-12 col-md-6">
                    <input type="text" id="searching1" name="searching1" class="form-control" placeholder="<?php echo $this->lang->line("Search..."); ?>" style='width: 200px;'>                                          
                  </div>
                  <div class="col-12">
                    <div class="table-responsive2">
                      <input type="hidden" id="put_row_id">
                      <table class="table table-bordered" id="mytable1">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th><?php echo $this->lang->line("id"); ?></th>
                              <th><?php echo $this->lang->line("Posting Page or Group"); ?></th>
                              <th><?php echo $this->lang->line("Post Type"); ?></th>
                              <th><?php echo $this->lang->line("Post ID"); ?></th>
                              <th><?php echo $this->lang->line("Posting Status"); ?></th>
                              <th><?php echo $this->lang->line("Schedule Time"); ?></th>
                              <th><?php echo $this->lang->line("Error"); ?></th>
                            </tr>
                          </thead>
                      </table>
                    </div>
                  </div> 
                </div>               
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="embed_code_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="fa fa-code"></i> <?php echo $this->lang->line("Get Embed Code");?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body" id="embed_code_content">
      
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="view_report" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg" style="min-width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center"><i class="fa fa-list-alt"></i> <?php echo $this->lang->line("report of Text/Image/Link/Video Poster") ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body text-center" id="view_report_modal_body">                

            </div>
        </div>
    </div>
</div>
