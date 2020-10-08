 <section class="section">
  <div class="section-header">
    <h1><i class="fas fa-share-square"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
     <a class="btn btn-primary" href="<?php echo base_url('social_accounts/index'); ?>">
        <i class="fa fa-cloud-download-alt"></i> <?php echo $this->lang->line("Import Facebook Accounts"); ?></a> 
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">

      <?php if($this->session->userdata('user_type') == 'Admin' || in_array(223,$this->module_access)) : ?>
        <div class="col-lg-6">
          <div class="card card-large-icons">
            <div class="card-icon text-primary">
              <i class="fas fa-list"></i>
            </div>
            <div class="card-body">
              <h4><?php echo $this->lang->line("Text/Link/Image/Video Post"); ?></h4>
              <p><?php echo $this->lang->line("Text, Link, Image, Video Poster..."); ?></p>
              <a href="<?php echo base_url("ultrapost/text_image_link_video"); ?>" class="card-cta"><?php echo $this->lang->line("Campaign List"); ?> <i class="fas fa-chevron-right"></i></a>
            </div>
          </div>
        </div>
      <?php endif; ?>


      <?php if($this->session->userdata('user_type') == 'Admin' || in_array(220,$this->module_access)) : ?>
        <div class="col-lg-6">
          <div class="card card-large-icons">
            <div class="card-icon text-primary">
              <i class="fas fa-hand-point-up"></i>
            </div>
            <div class="card-body">
              <h4><?php echo $this->lang->line("CTA Post"); ?></h4>
              <p><?php echo $this->lang->line("Call to Action Poster"); ?></p>
              <a href="<?php echo base_url("ultrapost/cta_post"); ?>" class="card-cta"><?php echo $this->lang->line("Campaign List"); ?> <i class="fas fa-chevron-right"></i></a>
            </div>
          </div>
        </div>
      <?php endif; ?>

      
      <?php if($this->session->userdata('user_type') == 'Admin' || in_array(222,$this->module_access)) : ?>
        <div class="col-lg-6">
          <div class="card card-large-icons">
            <div class="card-icon text-primary">
              <i class="fas fa-video"></i>
            </div>
            <div class="card-body">
              <h4><?php echo $this->lang->line("Carousel/Video Post"); ?></h4>
              <p><?php echo $this->lang->line("Carousel, Video Poster..."); ?></p>
              <a href="<?php echo base_url("ultrapost/carousel_slider_post"); ?>" class="card-cta"><?php echo $this->lang->line("Campaign List"); ?> <i class="fas fa-chevron-right"></i></a>
            </div>
          </div>
        </div>
      <?php endif; ?>


      <?php if($this->session->userdata('user_type') == 'Admin' || in_array(256,$this->module_access)) : ?>
        <div class="col-lg-6">
          <div class="card card-large-icons">
            <div class="card-icon text-primary">
              <i class="fas fa-rss"></i>
            </div>
            <div class="card-body">
              <h4><?php echo $this->lang->line("RSS Auto Post"); ?></h4>
              <p><?php echo $this->lang->line("RSS Auto Poster"); ?></p>
              <a href="<?php echo base_url("autoposting/settings"); ?>" class="card-cta"><?php echo $this->lang->line("Campaign List"); ?> <i class="fas fa-chevron-right"></i></a>
            </div>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </div>
</section><br><br>



<?php if($this->session->userdata('user_type') == 'Admin' || in_array(100,$this->module_access)) : ?>
  <section class="section">
    <div class="section-header">
      <h1><i class="fa fa-tasks"></i> <?php echo $this->lang->line("Comboposter"); ?></h1>
      <div class="section-header-button">
       <a class="btn btn-primary" href="<?php echo base_url('comboposter/social_accounts'); ?>">
          <i class="fa fa-cloud-download-alt"></i> <?php echo $this->lang->line("Import Social Accounts"); ?></a> 
      </div>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><?php echo $this->lang->line("Comboposter"); ?></div>
      </div>
    </div>

    <div class="section-body">
      <div class="row">

        <?php if($this->session->userdata('user_type') == 'Admin' || in_array(110,$this->module_access)) : ?>
          <div class="col-lg-6">
            <div class="card card-large-icons">
              <div class="card-icon text-primary">
                <i class="fa fa-file-text fa-4x"></i>
              </div>
              <div class="card-body">
                <h4><?php echo $this->lang->line("Text Post"); ?></h4>
                <p><?php echo $this->lang->line("Text Poster"); ?></p>
                <a href="<?php echo base_url("comboposter/text_post/campaigns"); ?>" class="card-cta"><?php echo $this->lang->line("Campaign List"); ?> <i class="fas fa-chevron-right"></i></a>
              </div>
            </div>
          </div>
        <?php endif; ?>


        <?php if($this->session->userdata('user_type') == 'Admin' || in_array(111,$this->module_access)) : ?>
          <div class="col-lg-6">
            <div class="card card-large-icons">
              <div class="card-icon text-primary">
                <i class="fa fa-picture-o fa-4x"></i>
              </div>
              <div class="card-body">
                <h4><?php echo $this->lang->line("Image Post"); ?></h4>
                <p><?php echo $this->lang->line("Image Poster"); ?></p>
                <a href="<?php echo base_url("comboposter/image_post/campaigns"); ?>" class="card-cta"><?php echo $this->lang->line("Campaign List"); ?> <i class="fas fa-chevron-right"></i></a>
              </div>
            </div>
          </div>
        <?php endif; ?>

        
        <?php if($this->session->userdata('user_type') == 'Admin' || in_array(112,$this->module_access)) : ?>
          <div class="col-lg-6">
            <div class="card card-large-icons">
              <div class="card-icon text-primary">
                <i class="fas fa-video fa-4x"></i>
              </div>
              <div class="card-body">
                <h4><?php echo $this->lang->line("Video Post"); ?></h4>
                <p><?php echo $this->lang->line("Video Poster"); ?></p>
                <a href="<?php echo base_url("comboposter/video_post/campaigns"); ?>" class="card-cta"><?php echo $this->lang->line("Campaign List"); ?> <i class="fas fa-chevron-right"></i></a>
              </div>
            </div>
          </div>
        <?php endif; ?>


        <?php if($this->session->userdata('user_type') == 'Admin' || in_array(113,$this->module_access)) : ?>
          <div class="col-lg-6">
            <div class="card card-large-icons">
              <div class="card-icon text-primary">
                <i class="fa fa-link fa-4x"></i>
              </div>
              <div class="card-body">
                <h4><?php echo $this->lang->line("Link Post"); ?></h4>
                <p><?php echo $this->lang->line("Link Poster"); ?></p>
                <a href="<?php echo base_url("comboposter/link_post/campaigns"); ?>" class="card-cta"><?php echo $this->lang->line("Campaign List"); ?> <i class="fas fa-chevron-right"></i></a>
              </div>
            </div>
          </div>
        <?php endif; ?>


        <?php if($this->session->userdata('user_type') == 'Admin' || in_array(114,$this->module_access)) : ?>
          <div class="col-lg-6">
            <div class="card card-large-icons">
              <div class="card-icon text-primary">
                <i class="fa fa-html5 fa-4x"></i>
              </div>
              <div class="card-body">
                <h4><?php echo $this->lang->line("HTML Post"); ?></h4>
                <p><?php echo $this->lang->line("HTML Poster"); ?></p>
                <a href="<?php echo base_url("comboposter/html_post/campaigns"); ?>" class="card-cta"><?php echo $this->lang->line("Campaign List"); ?> <i class="fas fa-chevron-right"></i></a>
              </div>
            </div>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </section>
<?php endif; ?>