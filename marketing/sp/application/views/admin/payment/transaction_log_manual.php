<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-hand-holding-usd"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
      <?php if ('Member' == $this->session->userdata('user_type')): ?>
      <a class="btn btn-primary" href="<?php echo base_url('payment/buy_package'); ?>"><i class="fa fa-cart-plus"></i> <?php echo $this->lang->line('Renew Package'); ?></a>
      <?php endif; ?>
    </div>
    <div class="section-header-breadcrumb">
      <?php 
      if($this->session->userdata("user_type")=="Admin") 
      echo '<div class="breadcrumb-item">'.$this->lang->line("Subscription").'</div>';
      else echo '<div class="breadcrumb-item active"><a href="'.base_url("payment/buy_package").'">'.$this->lang->line("Payment").'</a></div>';
      ?>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <div class="section-body">

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body data-card">
            <div class="table-responsive2">
              <table class="table table-bordered" id="mytable">
                <thead>
                  <tr>
                    <th>#</th>  
                    <th><?php echo $this->lang->line("Name"); ?></th>      
                    <th><?php echo $this->lang->line("Email"); ?></th>      
                    <th><?php echo $this->lang->line("Additional Info"); ?></th>      
                    <th><?php echo $this->lang->line("Attachment"); ?></th>      
                    <th><?php echo $this->lang->line("Status"); ?></th>

                    <?php if ('Admin' == $this->session->userdata('user_type')): ?>
                    <th><?php echo $this->lang->line("Actions"); ?></th>
                    <?php endif; ?>
                    
                    <th><?php echo $this->lang->line("Paid At"); ?></th>
                    <th><?php echo $this->lang->line("Paid Amount"); ?></th>      
                  </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                  <tr>
                    <th><?php echo $this->lang->line("Total"); ?></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <?php if('Admin' == $this->session->userdata('user_type')): ?>
                    <th></th>
                    <?php endif; ?>
                    <th></th>
                    <th></th>
                  </tr>
                </tfoot>
              </table>
            </div>             
          </div>

        </div>
      </div>
    </div>
    
  </div>
</section>


<?php
$drop_menu ='<a href="javascript:;" id="payment_date_range" class="btn btn-primary btn-lg float-right icon-left btn-icon"><i class="fas fa-calendar"></i> '.$this->lang->line("Choose Date").'</a><input type="hidden" id="payment_date_range_val">';
?>


<script>
  <?php if ('Admin' == $this->session->userdata('user_type')): ?>

    var datatable_display_columns = [
      {data: 'id'},
      {data: 'name'},
      {data: 'email'},
      {data: 'additional_info'},
      {data: 'attachment'},
      {data: 'status'},
      {data: 'actions'},
      {data: 'created_at'},
      {data: 'paid_amount'},
    ],
    style_targets = [0,1,2,4,5,6,7,8],
    sortable_targets = [3,4,6],
    total_amount_column = 8;

  <?php else: ?>

    var datatable_display_columns = [
      {data: 'id'},
      {data: 'name'},
      {data: 'email'},
      {data: 'additional_info'},
      {data: 'attachment'},
      {data: 'status'},
      {data: 'created_at'},
      {data: 'paid_amount'},
    ],
    style_targets = [0,1,2,4,5,6,7],
    sortable_targets = [3,4],
    total_amount_column = 7;

  <?php endif; ?>

  var base_url="<?php echo site_url(); ?>";

  var drop_menu = '<?php echo $drop_menu;?>';
  setTimeout(function(){ 
    $("#mytable_filter").append(drop_menu); 
    $('#payment_date_range').daterangepicker({
      ranges: {
        '<?php echo $this->lang->line("Last 30 Days");?>': [moment().subtract(29, 'days'), moment()],
        '<?php echo $this->lang->line("This Month");?>'  : [moment().startOf('month'), moment().endOf('month')],
        '<?php echo $this->lang->line("Last Month");?>'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      startDate: moment().subtract(29, 'days'),
      endDate  : moment()
    }, function (start, end) {
      $('#payment_date_range_val').val(start.format('YYYY-M-D') + '|' + end.format('YYYY-M-D')).change();
    });
  }, 2000);
    
   
  $(document).ready(function() {
    var perscroll;
    var table = $("#mytable").DataTable({
        serverSide: true,
        processing:true,
        bFilter: true,
        order: [[ 0, "desc" ]],
        pageLength: 10,
        ajax: {
          url: '<?php echo base_url('payment/transaction_log_manual_data'); ?>',
          type: 'POST',
          data: function (d) {
            d.payment_date_range = $('#payment_date_range_val').val();
          }
        },
        language: {
          url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
        },
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        columns: datatable_display_columns,          
        columnDefs: [
          {
            // targets: [1,2],
            // visible: false
          },
          {
            targets: style_targets,
            className: 'text-center'
          },
          {
            // targets: [10],
            // className: 'text-right'
          },
          {
            targets: sortable_targets,
            sortable: false
          }
        ],
        footerCallback: function ( row, data, start, end, display ) {
          var api = this.api(), data;
          var payment_total = api
            .column(total_amount_column)
            .data()
            .reduce(function (a, b) {
              return parseInt(a) + parseInt(b);
            }, 0);

            $(api.column(total_amount_column).footer()).html('<?php echo $currency_icon; ?>' + parseFloat(payment_total, 2));
        },
        fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
            if(areWeUsingScroll) {
              if (perscroll) {
                perscroll.destroy();
              }

              perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
            }
        },
        scrollX: 'auto',
        fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
            if(areWeUsingScroll) { 
              if (perscroll) {
                perscroll.destroy();
              }

              perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
            }
        }
    });

    $(document).on('change', '#payment_date_range_val', function(event) {
      event.preventDefault(); 
      table.draw();
    });

    // Downloads file
    $(document).on('click', '#mp-download-file', function(e) {
      e.preventDefault();

      // Makes reference 
      var that = this;

      // Starts spinner
      $(that).removeClass('btn-outline-info');
      $(that).addClass('btn-info disabled btn-progress');

      // Grabs ID
      var file = $(this).data('id');

      // Requests for file
      $.ajax({
        type: 'POST',
        data: { file },
        dataType: 'JSON',
        url: '<?php echo base_url('payment/manual_payment_download_file') ?>',
        success: function(res) {
          // Stops spinner
          $(that).removeClass('btn-info disabled btn-progress');
          $(that).addClass('btn-outline-info');

          // Shows error if something goes wrong
          if (res.error) {
            swal({
              icon: 'error',
              text: res.error,
              title: '<?php echo $this->lang->line('Error!'); ?>',
            });
            return;
          }

          // If everything goes well, requests for downloading the file
          if (res.status && 'ok' === res.status) {
            window.location = '<?php echo base_url('payment/manual_payment_download_file'); ?>';
          }
        },
        error: function(xhr, status, error) {
          // Stops spinner
          $(that).removeClass('btn-info disabled btn-progress');
          $(that).addClass('btn-outline-info');

          // Shows internal errors
          swal({
            icon: 'error',
            text: error,
            title: '<?php echo $this->lang->line('Error!'); ?>',
          });
        }
      });
    });
  
    <?php if ('Admin' == $this->session->userdata('user_type')): ?>    
      // Approve manual transaction
      $(document).on('click', '#mp-approve-btn', function(e) {
        e.preventDefault();

        // Makes reference
        var that = this;

        // Gets classes
        var el_classes = $(that)[0] ? $(that)[0].className : '';
        var new_classes = el_classes ? el_classes.replace('-outline', '') : '';

        // Shows spinner
        $(that).removeClass();
        $(that).addClass(new_classes.concat(' disabled btn-progress'));

        // Gets transaction ID
        var id = $(that).data('id');
        var action_type = $(that).attr('id');    

        $.ajax({
          type: 'POST',
          dataType: 'JSON',
          data: { id, action_type },
          url: '<?php echo base_url('payment/manual_payment_handle_actions'); ?>',
          success: function(res) {
            // Stops spinner
            $(that).removeClass();
            $(that).addClass(el_classes);

            // Shows error if something goes wrong
            if (res.error) {
              swal({
                icon: 'error',
                text: res.error,
                title: '<?php echo $this->lang->line('Error!'); ?>',
              });
              return;
            }
            // If everything goes well, requests for downloading the file
            if (res.status && 'ok' === res.status) {
              // Shows success message
              swal({
                icon: 'success',
                text: res.message,
                title: '<?php echo $this->lang->line('Success!'); ?>',
              });

              // Reloads datatable
              table.ajax.reload();
            }
          },
          error: function(xhr, status, error) {
            // Stops spinner
            $(that).removeClass();
            $(that).addClass(prev_el_classes);

            // Shows error if something goes wrong
            swal({
              icon: 'error',
              text: xhr.responseText,
              title: '<?php echo $this->lang->line('Error!'); ?>',
            });            
          }
        });
      });
    <?php endif; ?>
  });
  
 
</script>