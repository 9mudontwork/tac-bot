<div class="login-content">
    <div class="lc-block lc-block-alt toggled" id="l-lockscreen">
        <div class="lcb-form z-depth-1">
            <img class="lcb-user z-depth-2" src="<?php echo base_url(); ?>theme/custom/img/1.png">

            <?php echo form_open('/');?>

            <?php 
            if (validation_errors()) {
                ?>
            <div class="alert alert-danger" role="alert">
                <?php 
                echo validation_errors(); ?>
            </div>
            <?php
            }
            ?>

                <div class="fg-line">
                    <input type="text" id="email_login" name="email_login" value="<?=set_value('email_login');?>" class="form-control text-center input-lg"
                        placeholder="Enter Facebook Email Linked with Game" autocomplete="off">
                </div>

                <button type="submit" class="btn btn-login btn-default btn-float">
                    <i class="zmdi zmdi-arrow-forward"></i>
                </button>
                <?php echo form_close(); ?>
        </div>
    </div>
</div>