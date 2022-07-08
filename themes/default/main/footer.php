    <?php if(IS_LOGGED == true){ ?>
        <?php if($config->credit_earn_system == 1){?>
        <div class="payment_modal modal" id="reward_daily_credit_modal">
            <div class="modal-dialog">
                <div class="modal-content dt_bank_trans_modal">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo __( 'Credit Reward' );?></h5>
                    </div>
                    <div class="modal-body  credit_pln">
                        <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"></circle><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"></path></svg>
                        <p><?php echo __( 'Congratulation!. you login to our site for' );?> <?php echo (int)$config->credit_earn_max_days;?> <?php echo __( 'days' );?>, <?php echo __( 'and you earn' );?> <?php echo (int)$config->credit_earn_max_days * (int)$config->credit_earn_day_amount;?> <?php echo __( 'credits' );?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php if($config->isDailyCredit){ ?>
            <script>
                $(document).ready(function() {
                    $('#reward_daily_credit_modal').modal({
                        onCloseEnd: function(){
                            window.location.href = window.site_url+'/credit';
                        }
                    }).modal("open");

                });
            </script>
        <?php }} ?>
        <div class="payment_modalx modal">
            <div class="modal-dialog">
                <div class="modal-content dt_bank_trans_modal">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo __( 'Unlock Private Photo Payment' );?></h5>
                    </div>
                    <div class="modal-body  credit_pln">
                        <br>
                        <h6><?php echo __( 'To unlock private photo feature in your account, you have to pay' );?> <?php echo $config->currency_symbol . (int)$config->lock_private_photo_fee;?>.</h6>

                        <p class="bold"><?php echo __( 'Pay Using' );?></p>
                        <div class="pay_using">
                            <div class="pay_u_btns valign-wrapper">
                            <?php if(!empty($config->bank_description)){?>
                                <button onclick="unlock_photo_private_pay_via_bank();" class="btn valign-wrapper bank"><?php echo __( 'Bank Transfer' );?> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M15,14V11H18V9L22,12.5L18,16V14H15M14,7.7V9H2V7.7L8,4L14,7.7M7,10H9V15H7V10M3,10H5V15H3V10M13,10V12.5L11,14.3V10H13M9.1,16L8.5,16.5L10.2,18H2V16H9.1M17,15V18H14V20L10,16.5L14,13V15H17Z"></path></svg></button>
                            <?php } ?>

                            <?php if(!empty($config->paypal_id)){?>
                            <button id="unlock_photo_private_paypal" onclick="clickAndDisable(this);" class="btn paypal valign-wrapper">
                                <span><?php echo __( 'PayPal' );?></span> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M8.32,21.97C8.21,21.92 8.08,21.76 8.06,21.65C8.03,21.5 8,21.76 8.66,17.56C9.26,13.76 9.25,13.82 9.33,13.71C9.46,13.54 9.44,13.54 10.94,13.53C12.26,13.5 12.54,13.5 13.13,13.41C16.38,12.96 18.39,11.05 19.09,7.75C19.13,7.53 19.17,7.34 19.18,7.34C19.18,7.33 19.25,7.38 19.33,7.44C20.36,8.22 20.71,9.66 20.32,11.58C19.86,13.87 18.64,15.39 16.74,16.04C15.93,16.32 15.25,16.43 14.05,16.46C13.25,16.5 13.23,16.5 13,16.65C12.83,16.82 12.84,16.79 12.45,19.2C12.18,20.9 12.08,21.45 12.04,21.55C11.97,21.71 11.83,21.85 11.67,21.93L11.56,22H10C8.71,22 8.38,22 8.32,21.97V21.97M3.82,19.74C3.63,19.64 3.5,19.47 3.5,19.27C3.5,19 6.11,2.68 6.18,2.5C6.27,2.32 6.5,2.13 6.68,2.06L6.83,2H10.36C14.27,2 14.12,2 15,2.2C17.62,2.75 18.82,4.5 18.37,7.13C17.87,10.06 16.39,11.8 13.87,12.43C13,12.64 12.39,12.7 10.73,12.7C9.42,12.7 9.32,12.71 9.06,12.85C8.8,13 8.59,13.27 8.5,13.6C8.46,13.67 8.23,15.07 7.97,16.7C7.71,18.33 7.5,19.69 7.5,19.72L7.47,19.78H5.69C4.11,19.78 3.89,19.78 3.82,19.74V19.74Z" /></svg>
                                <svg class="spinner" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="spinner__path" fill="none" stroke-width="7" stroke-linecap="round" cx="33" cy="33" r="29"></circle></svg>
                            </button>
                            <?php } ?>
                            <?php if(!empty($config->stripe_secret)){?>
                            <button id="unlock_photo_private_stripe" class="btn stripe valign-wrapper"><?php echo __( 'Card' );?> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M20,8H4V6H20M20,18H4V12H20M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z" /></svg></button>
                            <?php } ?>
                            <?php if(!empty($config->paysera_password)){?>
                                <button id="unlock_photo_private_sms_payment" onclick="unlock_photo_private_pay_via_sms();" class="btn valign-wrapper sms"><?php echo __( 'SMS' );?> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M17,19V5H7V19H17M17,1A2,2 0 0,1 19,3V21A2,2 0 0,1 17,23H7C5.89,23 5,22.1 5,21V3C5,1.89 5.89,1 7,1H17M9,7H15V9H9V7M9,11H13V13H9V11Z"></path></svg></button>
                            <?php } ?>
                            <?php if( $config->cashfree_payment === 'yes' && !empty($config->cashfree_client_key) && !empty($config->cashfree_secret_key)){?>
                                <button id="cashfree_payment" onclick="unlock_photo_private_pay_via_cashfree();" class="btn valign-wrapper cashfree"><?php echo __( 'cashfree' );?> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M20,8H4V6H20M20,18H4V12H20M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z" /></svg></button>
                            <?php } ?>
                            <?php if ($config->iyzipay_payment == "yes" && !empty($config->iyzipay_key) && !empty($config->iyzipay_secret_key)) { ?>
                                <button id="iyzipay-button1" class="btn-cart btn btn-iyzipay-payment valign-wrapper iyzipay" onclick="unlock_photo_private_pay_via_iyzipay();">
                                    <?php echo __( 'Iyzipay');?> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M20,8H4V6H20M20,18H4V12H20M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z" /></svg>
                                </button>
                            <?php } ?>
                            <?php if ($config->paystack_payment == "yes" && !empty($config->paystack_secret_key)) { ?>
                                <button id="paystack-button1" class="btn-cart btn btn-paystack-payment valign-wrapper paystack" onclick="unlock_photo_private_pay_via_paystack();">
                                    <?php echo __( 'PayStack');?> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M20,8H4V6H20M20,18H4V12H20M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z" /></svg>
                                </button>
                            <?php } ?>
                            <?php if ($config->authorize_payment == "yes") { ?>
                                <button id="authorize-button1" class="btn-cart btn btn-authorize-payment valign-wrapper authorize" onclick="unlock_photo_private_pay_via_authorize();">
                                    <?php echo __( 'Authorize.net');?> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M20,8H4V6H20M20,18H4V12H20M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z" /></svg>
                                </button>
                            <?php } ?>
                            <?php if ($config->securionpay_payment == "yes") { ?>
                                <button id="securionpay-button1" class="btn-cart btn btn-securionpay-payment valign-wrapper securionpay" onclick="unlock_photo_private_pay_via_securionpay();">
                                    <?php echo __( 'Securionpay');?> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M20,8H4V6H20M20,18H4V12H20M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z" /></svg>
                                </button>
                            <?php } ?>
                            <?php if ($config->checkout_payment == 'yes') { ?>
                                <button id="2co_credit" class="btn 2co valign-wrapper twoco"  onclick="unlock_photo_private_pay_via_2co();">
                                    <?php echo __( '2Checkout' );?> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M20,8H4V6H20M20,18H4V12H20M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z" /></svg>
                                </button>
                            <?php } ?>
                            <?php if ($config->payu_payment == 'yes') { ?>
                                <button class="btn-payu btn valign-wrapper payu" onclick="pay_using_payu('unlock_private_photo',this,true);">
                                    <?php echo __( 'payu' );?> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M20,8H4V6H20M20,18H4V12H20M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z" /></svg>
                                </button>
                            <?php } ?>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function unlock_photo_private_pay_via_2co(){
                $('#2checkout_type').val('unlock_private_photo');
                $('#2checkout_description').val('<?php echo __( "Unlock Private photo feature");?>');
                $('#2checkout_price').val(<?php echo (int)$config->lock_private_photo_fee;?>);

                $('#2checkout_modal').modal('open');
            }
            function unlock_photo_private_pay_via_cashfree(){
                $('.cashfree-payment').attr('disabled','true');

                $('#cashfree_type').val('unlock_private_photo');
                $('#cashfree_description').val('<?php echo __( "Unlock Private photo feature");?>');
                $('#cashfree_price').val(<?php echo (int)$config->lock_private_photo_fee;?>);

                $("#cashfree_alert").html('');
                $('.go_pro--modal').fadeOut(250);
                $('#cashfree_modal_box').modal('open');

                $('.btn-cashfree-payment').removeAttr('disabled');
            }
            function unlock_photo_private_pay_via_sms() {
                window.location = window.ajax + 'sms/generate_unlock_private_photo_link';
            }
            <?php if(!empty($config->paypal_id)){ ?>
            document.getElementById('unlock_photo_private_paypal').addEventListener('click', function(e) {
                $.post(window.ajax + 'paypal/generate_link', {description:'<?php echo __( "Unlock Private photo feature");?>', amount:<?php echo (int)$config->lock_private_photo_fee;?>, mode: "unlock_private_photo"}, function (data) {
                    if (data.status == 200) {
                        window.location.href = data.location;
                    } else {
                        $('.modal-body').html('<i class="fa fa-spin fa-spinner"></i> <?php echo __( 'Payment declined' );?> ');
                    }
                });

                e.preventDefault();
            });
            <?php } ?>
            <?php if(!empty($config->stripe_secret)){?>
            document.getElementById('unlock_photo_private_stripe').addEventListener('click', function(e) {
                $('#unlock_photo_private_stripe_email').val(atob($('#unlock_photo_private_stripe_email').attr('data-email')));
                $('#unlock_photo_private_stripe_number').val('');
                $('#unlock_photo_private_stripe_cvc').val('');
                $('#unlock_photo_private_stripe_modal').removeClass('up_rec_img_ready, up_rec_active');
                //$('#unlock_photo_private_stripe_modal').modal('open');

                $.post(window.ajax + 'stripe/createsession', {
                    payType: 'unlock_private_photo',
                    description: '<?php echo __( "Unlock Private photo feature");?>',
                    price: '<?php echo (int)$config->lock_private_photo_fee;?>'
                }, function (data) {
                    if (data.status == 200) {
                        stripe.redirectToCheckout({ sessionId: data.session_id });
                    } else {
                        // $('.modal-body').html('<i class="fa fa-spin fa-spinner"></i> <?php echo __('Payment declined');?> ');
                    }
                });

            });
            <?php }?>


            function lock_pro_video_pay_via_sms() {
                window.location = window.ajax + 'sms/generate_lock_pro_video_link';
            }
            <?php if(!empty($config->paypal_id)){ ?>
            document.getElementById('lock_pro_video_paypal').addEventListener('click', function(e) {
                $.post(window.ajax + 'paypal/generate_link', {description:'<?php echo __( "Unlock Upload video feature");?>', amount:<?php echo (int)$config->lock_pro_video_fee;?>, mode: "lock_pro_video"}, function (data) {
                    if (data.status == 200) {
                        window.location.href = data.location;
                    } else {
                        $('.modal-body').html('<i class="fa fa-spin fa-spinner"></i> <?php echo __( 'Payment declined' );?> ');
                    }
                });

                e.preventDefault();
            });
            <?php } ?>
            <?php if(!empty($config->stripe_secret)){?>
                if (document.getElementById('lock_pro_video_stripe')) {
                    document.getElementById('lock_pro_video_stripe').addEventListener('click', function(e) {
                        $('#lock_pro_video_stripe_email').val(atob($('#lock_pro_video_stripe_email').attr('data-email')));
                        $('#lock_pro_video_stripe_number').val('');
                        $('#lock_pro_video_stripe_cvc').val('');
                        $('#lock_pro_video_stripe_modal').removeClass('up_rec_img_ready, up_rec_active');
                        //$('#lock_pro_video_stripe_modal').modal('open');
                        $.post(window.ajax + 'stripe/createsession', {
                            payType: 'lock_pro_video',
                            description: '<?php echo __( "Unlock Upload video feature");?>',
                            price: <?php echo (int)$config->lock_pro_video_fee;?>
                        }, function (data) {
                            if (data.status == 200) {
                                stripe.redirectToCheckout({ sessionId: data.session_id });
                            } else {
                                // $('.modal-body').html('<i class="fa fa-spin fa-spinner"></i> <?php echo __('Payment declined');?> ');
                            }
                        });
                    });
                }
                
                    
            <?php }?>

        </script>

        <div class="modal fade" id="lock_pro_video_stripe_modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content add_money_modal add_address_modal">
                    <h5 class="modal-title text-center">Stripe</h5>
                    <div class="modal-body">
                        <div id="lock_pro_video_stripe_alert" style="    color: red;"></div>
                        <form id="stripe_form" method="post">
                            <div class="form-group">
                                <input class="form-control shop_input" type="text" placeholder="<?php echo __( 'Name' );?>"  value="<?php echo $profile->full_name;?>" id="lock_pro_video_stripe_name">
                            </div>
                            <div class="form-group">
                                <input class="form-control shop_input" type="email" placeholder="<?php echo __( 'Email' );?>" value="" data-email="<?php echo base64_encode($profile->email);?>" id="lock_pro_video_stripe_email">
                            </div>
                            <div class="form-group">
                                <input id="lock_pro_video_stripe_number" class="form-control shop_input" type="text" placeholder="<?php echo __( 'Card Number' );?>">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select id="lock_pro_video_stripe_month" type="text" class="form-control shop_input" autocomplete="off" placeholder="<?php echo __( 'Month' );?> (01)">
                                            <option value="01">01</option>
                                            <option value="02">02</option>
                                            <option value="03">03</option>
                                            <option value="04">04</option>
                                            <option value="05">05</option>
                                            <option value="06">06</option>
                                            <option value="07">07</option>
                                            <option value="08">08</option>
                                            <option value="09">09</option>
                                            <option value="10">10</option>
                                            <option value="11">11</option>
                                            <option value="12">12</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select id="lock_pro_video_stripe_year" type="text" class="form-control shop_input" autocomplete="off" placeholder="<?php echo __( 'Year' );?> (2019)">
                                            <?php for ($i=date('Y'); $i <= date('Y')+15; $i++) {  ?>
                                                <option value="<?php echo($i) ?>"><?php echo($i) ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input id="lock_pro_video_stripe_cvc" type="text" class="form-control shop_input" autocomplete="off" placeholder="CVC" maxlength="3" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-main" onclick="SH_lock_pro_video_StripeRequest()" id="lock_pro_video_stripe_button"><?php echo __( 'Pay' );?></button>
                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal"><?php echo __( 'Cancel' );?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="unlock_photo_private_stripe_modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content add_money_modal add_address_modal">
                    <h5 class="modal-title text-center">Stripe</h5>
                    <div class="modal-body">
                        <div id="unlock_photo_private_stripe_alert" style="    color: red;"></div>
                        <form id="stripe_form" method="post">
                            <div class="form-group">
                                <input class="form-control shop_input" type="text" placeholder="<?php echo __( 'Name' );?>"  value="<?php echo $profile->full_name;?>" id="unlock_photo_private_stripe_name">
                            </div>
                            <div class="form-group">
                                <input class="form-control shop_input" type="email" placeholder="<?php echo __( 'Email' );?>" value="" data-email="<?php echo base64_encode($profile->email);?>" id="unlock_photo_private_stripe_email">
                            </div>
                            <div class="form-group">
                                <input id="unlock_photo_private_stripe_number" class="form-control shop_input" type="text" placeholder="<?php echo __( 'Card Number' );?>">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select id="unlock_photo_private_stripe_month" type="text" class="form-control shop_input" autocomplete="off" placeholder="<?php echo __( 'Month' );?> (01)">
                                            <option value="01">01</option>
                                            <option value="02">02</option>
                                            <option value="03">03</option>
                                            <option value="04">04</option>
                                            <option value="05">05</option>
                                            <option value="06">06</option>
                                            <option value="07">07</option>
                                            <option value="08">08</option>
                                            <option value="09">09</option>
                                            <option value="10">10</option>
                                            <option value="11">11</option>
                                            <option value="12">12</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select id="unlock_photo_private_stripe_year" type="text" class="form-control shop_input" autocomplete="off" placeholder="<?php echo __( 'Year' );?> (2019)">
                                            <?php for ($i=date('Y'); $i <= date('Y')+15; $i++) {  ?>
                                                <option value="<?php echo($i) ?>"><?php echo($i) ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input id="unlock_photo_private_stripe_cvc" type="text" class="form-control shop_input" autocomplete="off" placeholder="CVC" maxlength="3" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-main" onclick="SH_unlock_photo_private_StripeRequest()" id="unlock_photo_private_stripe_button"><?php echo __( 'Pay' );?></button>
                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal"><?php echo __( 'Cancel' );?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <?php if(IS_LOGGED === true){ ?>
            <?php if ($config->payu_payment == 'yes') { ?>
                <div class="modal modal-sm" id="payu_modal" role="dialog" data-keyboard="false" style="overflow-y: auto;">
                    <div class="modal-dialog">
                        <div class="modal-content">
							<h4 class="modal-title text-center"><?php echo __('payu');?></h4>
                            <form class="form form-horizontal" method="post" id="payu_form" action="#">
                                <div class="modal-body payu_modal">
                                    <div id="payu_alert"></div>
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <input id="payu_card_number" type="text" autocomplete="off" placeholder="<?php echo __('card number');?>">
                                        </div>
                                        <div class="input-field col s4">
                                            <select id="payu_card_month" name="card_month" class="browser-default" type="text" autocomplete="off" placeholder="<?php echo __('month');?> (01)">
                                                <option value="01">01</option>
                                                <option value="02">02</option>
                                                <option value="03">03</option>
                                                <option value="04">04</option>
                                                <option value="05">05</option>
                                                <option value="06">06</option>
                                                <option value="07">07</option>
                                                <option value="08">08</option>
                                                <option value="09">09</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                            </select>
                                        </div>
                                        <div class="input-field col s4 no-padding-both">
                                            <select id="payu_card_year" name="card_year" class="browser-default" type="text" autocomplete="off" placeholder="<?php echo __('year');?> (2021)">
                                                <?php for ($i=date('Y'); $i <= date('Y')+15; $i++) {  ?>
                                                    <option value="<?php echo($i) ?>"><?php echo($i) ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="input-field col s4">
                                            <input id="payu_card_cvc" name="card_cvc" type="text" autocomplete="off" placeholder="CVC" maxlength="3" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                        </div>
                                    </div>
                                </div>
                                <div class="clear"></div>
                                <div class="modal-footer">
                                    <div class="ball-pulse"><div></div><div></div><div></div></div>
                                    <button type="button" class="waves-effect waves-light btn-flat btn_primary white-text" id="payu_btn"><?php echo __('pay');?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <script type="text/javascript">
                    function pay_using_payu(type,self,show_modale = false) {
                        if(show_modale == true){
                          $('#payu_modal').find('#payu_btn').attr('onclick', "pay_using_payu('"+type+"',self)");
                          $('#payu_modal').modal('open');
                          return false;
                        }
                        var plans;
                        
                        if (type == 'go_pro') {
                            plans = document.getElementsByName('pro_plan');
                        }
                        else if(type == 'credit'){
                            plans = document.getElementsByName('plans');
                        }
                        var price = 0;
                        var description = '';
                        if (type == 'go_pro' || type == 'credit') {
                            for (index=0; index < plans.length; index++) {
                                if (plans[index].checked) {
                                    description = plans[index].value;
                                    price = plans[index].getAttribute('data-price');
                                    break;
                                }
                            }
                        }
                        if (type == 'unlock_private_photo') {
                            price = "<?php echo (int)$config->lock_private_photo_fee;?>";
                            description = "<?php echo __( "Unlock Private photo feature");?>";
                        }
                        if (type == 'unlock_private_photo') {
                            price = "<?php echo (int)$config->lock_pro_video_fee;?>";
                            description = "<?php echo __( "Unlock Upload video feature");?>";
                        }
                            
                        card_number = $('#payu_card_number').val();
                        card_month = $('#payu_card_month').val();
                        card_year = $('#payu_card_year').val();
                        card_cvc = $('#payu_card_cvc').val();
                        $('#payu_btn').attr('disabled','true');
                        $.post(window.ajax + 'payu/pay', {
                            type: type,
                            description: description,
                            price: price,card_number:card_number,card_month:card_month,card_year:card_year,card_cvc:card_cvc
                        }, function(data) {
                            $('#payu_btn').removeAttr('disabled');
                            if (data.status == 200) {
                                if(data.url){
                                  location.href = data.url;
                                }
                            } else {
                                $('#payu_alert').html("<div class='alert alert-danger'>"+data.message+"</div>");
                            }
                        }).fail(function(data) {
                            $('#payu_btn').removeAttr('disabled');
                            $('#payu_alert').html("<div class='alert alert-danger'>"+data.responseJSON.message+"</div>");
                        });
                    }
                </script>
                <?php } ?>
            <?php if ($config->checkout_payment == 'yes') { ?>
                <div class="modal modal-sm" id="2checkout_modal" role="dialog" data-keyboard="false" style="overflow-y: auto;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <h4 class="modal-title"><?php echo __('Check out');?></h4>
                            <form class="form form-horizontal" method="post" id="2checkout_form" action="#">
                                <div class="modal-body twocheckout_modal">
                                    <div id="2checkout_alert"></div>
                                    <div class="clear"></div>
                                    <div class="row">
                                        <div class="input-field col s6">
                                            <input id="card_name" type="text" class="form-control input-md" autocomplete="off" placeholder="<?php echo __('name');?>" value="<?php echo $profile->full_name;?>">
                                        </div>
                                        <div class="input-field col s6">
                                            <input id="card_address" type="text" class="form-control input-md" autocomplete="off" placeholder="<?php echo __('address');?>" value="<?php echo $profile->address;?>">
                                        </div>
                                        <div class="input-field col s6">
                                            <input id="card_city" type="text" class="form-control input-md" autocomplete="off" placeholder="<?php echo __('city');?>" value="<?php echo $profile->city;?>">
                                        </div>
                                        <div class="input-field col s6">
                                            <input id="card_state" type="text" class="form-control input-md" autocomplete="off" placeholder="<?php echo __('state');?>" value="<?php echo $profile->state;?>">
                                        </div>
                                        <div class="input-field col s6">
                                            <input id="card_zip" type="text" class="form-control input-md" autocomplete="off" placeholder="<?php echo __('zip');?>" value="<?php echo $profile->zip;?>">
                                        </div>
                                        <div class="input-field col s6">
                                            <select id="card_country" name="card_country" class="browser-default">
                                                <option value="" disabled selected><?php echo __( 'Choose your country' );?></option>
                                                <?php
                                                $_countries = Dataset::load('countries');
                                                foreach( $_countries as $key => $val ){
                                                    echo '<option value="'. $key .'" data-code="'. $val['isd'] .'">'. $val['name'] .'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="input-field col s6">
                                            <input id="card_email" type="text" class="form-control input-md" autocomplete="off" placeholder="<?php echo __('email');?>" value="<?php echo $profile->email;?>">
                                        </div>
                                        <div class="input-field col s6">
                                            <input id="card_phone" type="text" class="form-control input-md" autocomplete="off" placeholder="<?php echo __('phone number');?>" value="<?php echo $profile->cc_phone_number;?>">
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                    <hr class="border_hr">
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <input id="_number_" type="text" class="form-control input-md" autocomplete="off" placeholder="<?php echo __('card number');?>">
                                            <input id="card_number" name="card_number" type="hidden" class="form-control input-md" autocomplete="off">
                                        </div>
                                        <div class="input-field col s4">
                                            <select id="card_month" name="card_month" type="text" class="browser-default" autocomplete="off" placeholder="<?php echo __('month');?> (01)">
                                                <option value="01">01</option>
                                                <option value="02">02</option>
                                                <option value="03">03</option>
                                                <option value="04">04</option>
                                                <option value="05">05</option>
                                                <option value="06">06</option>
                                                <option value="07">07</option>
                                                <option value="08">08</option>
                                                <option value="09">09</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                            </select>
                                        </div>
                                        <div class="input-field col s4 no-padding-both">
                                            <select id="card_year" name="card_year" type="text" class="browser-default" autocomplete="off" placeholder="<?php echo __('year');?> (2021)">
                                                <?php for ($i=date('Y'); $i <= date('Y')+15; $i++) {  ?>
                                                    <option value="<?php echo($i) ?>"><?php echo($i) ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="input-field col s4">
                                            <input id="card_cvc" name="card_cvc" type="text" class="form-control input-md" autocomplete="off" placeholder="CVC" maxlength="3" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                    <input type="hidden" id="2checkout_type" class="hidden" name="payType">
                                    <input type="hidden" id="2checkout_description" class="hidden" name="description">
                                    <input type="hidden" id="2checkout_price" class="hidden" name="price">
                                    <input id="card_token" name="token" type="hidden" value="">
                                </div>
                                <div class="clear"></div>
                                <div class="modal-footer">
                                    <div class="ball-pulse"><div></div><div></div><div></div></div>
                                    <button type="button" class="btn btn-main" onclick="tokenRequest()" id="2checkout_btn"><?php echo __('pay');?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <script type="text/javascript">
                    // Called when token created successfully.
                    var successCallback = function(data) {
                        var myForm = document.getElementById('2checkout_form');
                        $.post(window.ajax + 'checkout/createsession', {
                            card_number: $("#card_number").val(),
                            card_cvc: $("#card_cvc").val(),
                            card_month: $("#card_month").val(),
                            card_year: $("#card_year").val(),
                            card_name: $("#card_name").val(),
                            card_address: $("#card_address").val(),
                            card_city: $("#card_city").val(),
                            card_state: $("#card_state").val(),
                            card_zip: $("#card_zip").val(),
                            card_country: $("#card_country").val(),
                            card_email: $("#card_email").val(),
                            card_phone: $("#card_phone").val(),
                            token: data.response.token.token,
                            payType: $("#2checkout_type").val(),
                            description: $("#2checkout_description").val(),
                            price: $("#2checkout_price").val()
                        }, function(data, textStatus, xhr) {
                            $('#2checkout_btn').html("<?php echo __('pay');?>");
                            $('#2checkout_btn').attr('disabled');
                            $('#2checkout_btn').removeAttr('disabled');
                            $('#2checkout_form').find('.ball-pulse').fadeOut(100);
                            if (data.status == 200) {
                                window.location.href = data.url;
                            } else {
                                $('#2checkout_alert').html("<div class='alert alert-danger'>"+data.error+"</div>");
                                setTimeout(function () {
                                    $('#2checkout_alert').html("");
                                },3000);
                            }
                            /*optional stuff to do after success */
                        });
                    };

                        // Called when token creation fails.
                    var errorCallback = function(data) {
                        $('#2checkout_btn').html("<?php echo __('pay');?>");
                        $('#2checkout_btn').removeAttr('disabled');
                        $('#2checkout_form').find('.ball-pulse').fadeOut(100);
                        if (data.errorCode === 200) {
                            tokenRequest();
                        } else {
                            $('#2checkout_alert').html("<div class='alert alert-danger'>"+data.errorMsg+"</div>");
                            setTimeout(function () {
                                $('#2checkout_alert').html("");
                            },3000);
                        }
                    };

                    var tokenRequest = function() {
                        $('#card_number').val($('#_number_').val());
                        $('#2checkout_btn').html("<?php echo __('please wait');?>");
                        $('#2checkout_btn').attr('disabled','true');
                        if ($("#card_number").val() != '' && $("#card_cvc").val() != '' && $("#card_month").val() != '' && $("#card_year").val() != '' && $("#card_name").val() != '' && $("#card_address").val() != '' && $("#card_city").val() != '' && $("#card_state").val() != '' && $("#card_zip").val() != '' && $("#card_country").val() != 0 && $("#card_email").val() != '' && $("#card_phone").val() != '') {
                            $('#2checkout_form').find('.ball-pulse').fadeIn(100);
                            // Setup token request arguments
                            var args = {
                                sellerId: "<?php echo($config->checkout_seller_id) ?>",
                                publishableKey: "<?php echo($config->checkout_publishable_key) ?>",
                                ccNo: $("#card_number").val(),
                                cvv: $("#card_cvc").val(),
                                expMonth: $("#card_month").val(),
                                expYear: $("#card_year").val()
                            };

                            // Make the token request
                            TCO.requestToken(successCallback, errorCallback, args);
                        }
                        else{
                            $('#2checkout_btn').html("<?php echo __('pay');?>");
                            $('#2checkout_btn').removeAttr('disabled');
                            $('#2checkout_alert').html("<div class='alert alert-danger'><?php echo __('please check details');?></div>");
                            setTimeout(function () {
                                $('#2checkout_alert').html("");
                            },3000);

                        }
                    };

                    $(function() {
                        try {
                            // Pull in the public encryption key for our environment
                            TCO.loadPubKey("<?php echo($config->checkout_mode) ?>");
                        } catch(e) {
                            console.log(e.toSource());
                        }

                    });
                </script>

            <?php } 
        }?>

        <!-- Boost Modal -->
		<div id="modal_boost" class="modal modal-sm">
			<div class="modal-content">
				<?php
					$_cost = 0;
					if( $profile->is_pro == "1" ){
						$_cost = $config->pro_boost_me_credits_price;
					}else{
						$_cost = $config->normal_boost_me_credits_price;
					}
                    if ( isGenderFree($profile->gender) === true ) {
                        $_cost = 0;
                    }
				?>
				<h6 class="bold"><?php echo __('Boost me!');?></h6>
				<p><?php echo __('Get seen more by people around you in find match.');?></p>
				<p><?php
                    if ( isGenderFree($profile->gender) === true ) {
                        echo __('Boost your profile for free for') . ' ' . $config->boost_expire_time . ' ' . __('miuntes') . '.';
                    }else {
                        echo __('This service costs you') . ' ' . $_cost . ' ' . __('credits and will last for') . ' ' . $config->boost_expire_time . ' ' . __('miuntes') . '.';
                    }
                    ?></p>
				<div class="modal-footer">
					<button type="button" class="btn-flat waves-effect modal-close"><?php echo __( 'Cancel' );?></button>
					<?php if($profile->balance >= $_cost ){?>
						<button data-userid="<?php echo $profile->id;?>" id="btn_boostme" class="modal-close waves-effect waves-light btn-flat btn_primary white-text"><?php
                            if ( isGenderFree($profile->gender) === true ) {
                                echo __( 'Boost For Free' );
                            }else{
                                echo __( 'Boost Now' );
                            }


                            ?></button>
					<?php }else{ ?>
						<a href="<?php echo $site_url;?>/credit" data-ajax="/credit" class="modal-close waves-effect waves-light btn-flat btn_primary white-text"><?php echo __( 'Buy Credits' );?></a>
					<?php } ?>
				</div>
			</div>
		</div>
		<!-- End Boost Modal -->
		
		<div class="sidenav_overlay" onclick="javascript:$('body').removeClass('side_open');"></div>
	<?php } ?>
		
	<!-- Scroll to top  -->
    <a href="javascript:void(0);" class="btn-floating btn-large waves-effect waves-light dt_to_top bg_gradient"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M7.41,15.41L12,10.83L16.59,15.41L18,14L12,8L6,14L7.41,15.41Z"></path></svg></a>
    <!-- End Scroll to top  -->

	<?php require( $theme_path . 'main' . $_DS . 'geolocation.php' );?>
    <?php require( $theme_path . 'main' . $_DS . 'custom-footer-js.php' );?>
    <?php //write_console();?>
	
	<script type="text/javascript">
		$(document).on('click', '.find_matches_cont > .row > .col.l3 .dt_sections [data-ajax]', function() {
			$('body').removeClass('side_open');
        });
        $('#open_slide').on('click', function(event) {
    event.preventDefault();
    $('body').addClass('side_open');
});
		</script>

    <?php if( IS_LOGGED == true ){ ?>
        <div style="display:none">
            <audio id="notification-sound" class="sound-controls" preload="auto" style="visibility: hidden;">
                <source src="<?php echo $theme_url; ?>assets/mp3/New-notification.mp3" type="audio/mpeg">
            </audio>
            <audio id="message-sound" class="sound-controls" preload="auto" style="visibility: hidden;">
                <source src="<?php echo $theme_url; ?>assets/mp3/New-message.mp3" type="audio/mpeg">
            </audio>
            <audio id="calling-sound" class="sound-controls" preload="auto">
                <source src="<?php echo $theme_url; ?>assets/mp3/calling.mp3" type="audio/mpeg">
            </audio>
            <audio id="video-calling-sound" class="sound-controls" preload="auto">
                <source src="<?php echo $theme_url; ?>assets/mp3/video_call.mp3" type="audio/mpeg">
            </audio>
        </div>
    <?php } ?>

    <?php if(route( 1 ) !== 'find-matches'){ ?>
    <script>
        window.addEventListener("load", function(){
            window.cookieconsent.initialise({
                "palette": {
                    "popup": {
                        "background": "#ffffff",
                        "text": "#afafaf"
                    },
                    "button": {
                        "background": "#c649b8"
                    }
                },
                "theme": "edgeless",
                "position": "bottom-left",
                "content": {
                    "message": "<?php echo __('This website uses cookies to ensure you get the best experience on our website.');?>",
                    "dismiss": "<?php echo __('Got It!');?>",
                    "link": "<?php echo __('Learn More');?>",
                    "href": "<?php echo $site_url;?>/privacy"
                }
            });
        });
    </script>
    <?php } ?>


<?php if( IS_LOGGED == true ){ ?>
    <?php if ($config->cashfree_payment == 'yes') { ?>
        <div class="modal modal-sm" id="cashfree_modal_box" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content add_money_modal add_address_modal">
                <h4 class="modal-title text-center"><?php echo __('Cashfree');?></h4>
                <div class="modal-body">
                    <div id="cashfree_alert"></div>
                    <form id="cashfree_form" method="post">
                    <div class="form-group">
                        <label class="plr15" for="cashfree_name"><?php echo __('name');?></label>
                        <input class="form-control shop_input" type="text" placeholder="<?php echo __('name');?>" value="<?php echo $profile->full_name; ?>" id="cashfree_name" name="name">
                    </div>
                    <div class="form-group">
                        <label class="plr15" for="cashfree_email"><?php echo __('email');?></label>
                        <input class="form-control shop_input" type="email" placeholder="<?php echo __('email');?>" value="<?php echo $profile->email; ?>" id="cashfree_email" name="email">
                    </div>
                    <div class="form-group">
                        <label class="plr15" for="cashfree_phone"><?php echo __('phone_number');?></label>
                        <input class="form-control shop_input" type="text" placeholder="<?php echo __('phone_number');?>" id="cashfree_phone" name="phone" value="<?php echo $profile->phone_number; ?>">
                    </div>

                    <input id="cashfree_type" name="cashfree_type" type="hidden" value="">
                    <input id="cashfree_description" name="cashfree_description" type="hidden" value="">
                    <input id="cashfree_price" name="cashfree_price" type="hidden" value="0">

                    <div class="modal-footer">
                        <button class="waves-effect waves-light btn-flat btn_primary white-text" id="cashfree_button" type="button" onclick="InitializeCashfree()"><?php echo __('pay');?></button>
                    </div>
                    </form>
                </div>
                </div>
            </div>
        </div>
        <script>
        $(document).ready(function(){
            $('#cashfree_modal_box').modal();
        });
        function InitializeCashfree() {
            $('#cashfree_button').html("<?php echo __('please_wait');?>");
            $('#cashfree_button').attr('disabled','true');
            name = $('#cashfree_name').val();
            phone = $('#cashfree_phone').val();
            email = $('#cashfree_email').val();
            amount = $('#cashfree_price').val();
            description = $('#cashfree_description').val();
            payType = $('#cashfree_type').val();
            $.post(window.ajax + 'cashfree/createsession', {
                name:name,
                phone:phone,
                email:email,
                description:description,
                price:amount,
                payType:payType
            }, function(data) {
            if (data.status == 200) {
                $('body').append(data.html);
                document.getElementById("redirectForm").submit();
            } else {
                $('#cashfree_alert').html("<div class='alert alert-danger'>"+data.message+"</div>");
                setTimeout(function () {
                $('#cashfree_alert').html("");
                },3000);
            }
            $('#cashfree_button').html("<?php echo __('pay');?>");
            $('#cashfree_button').removeAttr('disabled');
            });
        }
        </script>
    <?php } ?>

    <?php if ($config->iyzipay_payment === "yes" && !empty($config->iyzipay_key) && !empty($config->iyzipay_secret_key)) { ?>
        <div id="iyzipay_content"></div>
        <script>
            function unlock_photo_private_pay_via_iyzipay(){
                $('.btn-iyzipay-payment').attr('disabled','true');

                $.post(window.ajax + 'iyzipay/createsession', {
                    payType: 'unlock_private_photo',
                    description: '<?php echo __( "Unlock Private photo feature");?>',
                    price: <?php echo (int)$config->lock_private_photo_fee;?>
                }, function(data) {
                    if (data.status == 200) {
                        $('#iyzipay_content').html('');
                        $('#iyzipay_content').html(data.html);
                    } else {
                        $('.btn-iyzipay').attr('disabled', false).html("Iyzipay App not set yet.");
                    }
                    $('.btn-iyzipay').removeAttr('disabled');
                    $('.btn-iyzipay').find('span').text("<?php echo __( 'iyzipay');?>");
                });

                $('.btn-iyzipay-payment').removeAttr('disabled');

            }
        </script>
    <?php } ?>
    
    <?php if ($config->securionpay_payment === "yes") { ?>
        <script type="text/javascript">
        $(function () {
            SecurionpayCheckout.key = '<?php echo($config->securionpay_public_key); ?>';
            SecurionpayCheckout.success = function (result) {
                $.post(window.ajax + 'securionpay/handle', result, function(data, textStatus, xhr) {
                    if (data.status == 200) {
                        window.location.href = data.url;
                    }
                }).fail(function(data) {
                    M.toast({html: data.responseJSON.message});
                });
            };
            SecurionpayCheckout.error = function (errorMessage) {
                M.toast({html: errorMessage});
            };
        });
        function unlock_photo_private_pay_via_securionpay(){
            price = <?php echo (int)$config->lock_private_photo_fee;?>;
            $.post(window.ajax + 'securionpay/token', {type:'unlock_private_photo',price:price}, function(data, textStatus, xhr) {
                if (data.status == 200) {
                    SecurionpayCheckout.open({
                        checkoutRequest: data.token,
                        name: 'unlock private photo',
                        description: '<?php echo __( "Unlock Private photo feature");?>'
                    });
                }
            }).fail(function(data) {
                M.toast({html: data.responseJSON.message});
            });
        }
    </script>
    <?php } ?>
    <?php if ($config->authorize_payment === "yes") { ?>
        
        <script type="text/javascript">
            function unlock_photo_private_pay_via_authorize(){
                $('#authorize_btn').attr('onclick', 'AuthorizePhotoRequest()');
                $('#authorize_modal').modal('open');
            }
            function AuthorizePhotoRequest() {
                $('#authorize_btn').html("<?php echo __('please_wait');?>");
                $('#authorize_btn').attr('disabled','true');
                authorize_number = $('#authorize_number').val();
                authorize_month = $('#authorize_month').val();
                authorize_year = $('#authorize_year').val();
                authorize_cvc = $('#authorize_cvc').val();
                price = <?php echo (int)$config->lock_private_photo_fee;?>;
                $.post(window.ajax + 'authorize/pay', {type:'unlock_private_photo',card_number:authorize_number,card_month:authorize_month,card_year:authorize_year,card_cvc:authorize_cvc,price:price}, function(data) {
                    if (data.status == 200) {
                        window.location.href = data.url;
                    } else {
                        $('#authorize_alert').html("<div class='alert alert-danger'>"+data.message+"</div>");
                        setTimeout(function () {
                            $('#authorize_alert').html("");
                        },3000);
                    }
                    $('#authorize_btn').html("<?php echo __( 'pay' );?>");
                    $('#authorize_btn').removeAttr('disabled');
                }).fail(function(data) {
                    $('#authorize_alert').html("<div class='alert alert-danger'>"+data.responseJSON.message+"</div>");
                    setTimeout(function () {
                        $('#authorize_alert').html("");
                    },3000);
                    $('#authorize_btn').html("<?php echo __( 'pay' );?>");
                    $('#authorize_btn').removeAttr('disabled');
                });
            }
        </script>
    <?php } ?>
    <?php if ($config->paystack_payment === "yes") { ?>
        
        <script>
            function unlock_photo_private_pay_via_paystack(){
                $('#paystack_btn').attr('onclick', 'InitializePhotoPaystack()');
                $('#paystack_wallet_modal').modal('open');
            }
            function InitializePhotoPaystack() {
                $('#paystack_btn').html("<?php echo __('please_wait');?>");
                $('#paystack_btn').attr('disabled','true');
                email = $('#paystack_wallet_email').val();
                price = <?php echo (int)$config->lock_private_photo_fee;?>;
                $.post(window.ajax + 'paystack/initialize', {type:'unlock_private_photo',email:email,price:price}, function(data) {
                    if (data.status == 200) {
                        window.location.href = data.url;
                    } else {
                        $('#paystack_wallet_alert').html("<div class='alert alert-danger'>"+data.message+"</div>");
                        setTimeout(function () {
                            $('#paystack_wallet_alert').html("");
                        },3000);
                    }
                    $('#paystack_btn').html("<?php echo __( 'Confirm' );?>");
                    $('#paystack_btn').removeAttr('disabled');
                });
            }
        </script>
    <?php } ?>
<?php } ?>
</body>
</html>