<div class="row">
            <div class="col-md-4 col-sm-5">
                <div class="box-login">
                    <div class="tit-login"><?php echo $this->translate("Login") ?></div>
                    <div class="login-fb">
                <a class="btn btn-md btn-social btn-facebook" href="<?php echo $this->url("frontend_user", array("action" => "facebook-auth")) ?>">
                            <span class="fa fa-facebook"></span> <?php echo $this->translate("Login with Facebook") ?>
                        </a>
            </div>
                    <div class="text-or"><span>Or</span><p></p></div>
                    <!-- BEGIN LOGIN FORM -->
                        <form class="login-form" action="<?php echo $this->url("frontend_user", array("action" => "login")) ?>" method="post">
                        <div class="alert alert-danger display-hide">
                            <button class="close" data-close="alert"></button>
                            <span> <?php echo $this->translate("Enter any email and password") ?> </span>
                        </div>
                        <div class="alert alert-success display-hide">
                            <button class="close" data-close="alert"></button>
                            <span> <?php echo $this->translate("Successfully logged") ?> </span>
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?php echo $this->translate("Email") ?></label>
                            <input class="form-control" type="email" autocomplete="off" name="identity" />
                        </div>
                        <div class="form-group">
                            <label class="control-label"><?php echo $this->translate("Password") ?></label>
                            <input class="form-control" type="password" autocomplete="off" name="credential" />
                        </div>
                        <div class="form-group">
                            <div class="md-checkbox-list">
                                <div class="md-checkbox">
                                    <input type="checkbox" name="remember" id="checkbox1" class="md-check" value="1">
                                    <label for="checkbox1">
                                        <span></span>
                                        <span class="check"></span>
                                        <span class="box"></span> <?php echo $this->translate("Remember me") ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="redirect_url" value="<?php echo $this->redirect_url; ?>" />
                            <button type="submit" class="btn btn-lg green-custom btn-block margin-top-53"><?php echo $this->translate("Login") ?></button>
                        </div>
                    </form>
                    <!-- END LOGIN FORM -->
                </div>
            </div>
            <div class="col-md-8 col-sm-7">
                    <div class="box-register">
                        <div class="box-reg">
                            <div class="tit-login"><?php echo $this->translate("Sign up account") ?></div>
                            <div class="login-fb">
                        <a class="btn btn-sm btn-social btn-facebook" href="<?php echo $this->url("frontend_user", array("action" => "facebook-auth")) ?>">
                                    <span class="fa fa-facebook"></span> <?php echo $this->translate("Sign up with Facebook") ?>
                                </a>
                    </div>
                            <div class="text-or"><span>Or</span><p></p></div>
                        </div>
                        <div class="row">
                            <!-- BEGIN REGISTER FORM-->
                            <form class="register-form" action="<?php echo $this->url("frontend_user", array("action" => "register")) ?>" method="post">
                                <div class="alert alert-danger display-hide">
                                    <button class="close" data-close="alert"></button>
                                    <span> <?php echo $this->translate("(*) All field are required") ?> </span>
                                </div>
                                <div class="alert alert-success display-hide">
                                    <button class="close" data-close="alert"></button>
                                    <span> <?php echo $this->translate("Successfully register") ?> </span>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label"><?php echo $this->translate("First name") ?> <span class="required">*</span></label>
                                        <input type="text" name="firstname" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label"><?php echo $this->translate("Last name") ?> <span class="required">*</span></label>
                                        <input type="text" name="lastname" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label"><?php echo $this->translate("Email") ?> <span class="required">*</span></label>
                                        <input type="text" name="email" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label"><?php echo $this->translate("Password") ?> <span class="required">*</span></label>
                                        <input type="password" name="password" class="form-control" id="password">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label"><?php echo $this->translate("Password Verify") ?> <span class="required">*</span></label>
                                        <input type="password" name="passwordVerify" class="form-control" id="passwordVerify">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label"><?php echo $this->translate("Phone") ?> <span class="required">*</span></label>
                                        <input type="text" name="phone" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="md-checkbox-list">
                                            <div class="md-checkbox">
                                                <input type="checkbox" name="newsletter" id="checkbox2" class="md-check" value="1">
                                                <label for="checkbox2">
                                                    <span></span>
                                                    <span class="check"></span>
                                                    <span class="box"></span> <?php echo $this->translate("Signup Newsletter") ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group text-center">
                                        <input type="hidden" name="auto_login" value="yes" />
                                        <input type="hidden" name="redirect_url" value="<?php echo $this->redirect_url; ?>" />
                                        <button type="submit" class="btn btn-lg green-custom btn-block "><?php echo $this->translate("Register") ?></button>
                                    </div>
                            </div>
                        </form>
                        <!-- END FORM-->
                    </div>
                </div>
            </div>
        </div>
<?php
$offStyle = 12;
$offScript = 10;
$this->headLink()->offsetSetStylesheet($offStyle++, $this->basePath('assets/css/auth.css'));
$this->headLink()->offsetSetStylesheet($offStyle++, $this->basePath('assets/css/bootstrap-social.css'));
$this->inlineScript()->offsetSetFile($offScript++, $this->basePath('assets/plugins/jquery-validation/js/jquery.validate.min.js'));
$this->inlineScript()->offsetSetFile($offScript++, $this->basePath('assets/plugins/jquery-validation/js/additional-methods.min.js'));
$this->inlineScript()->offsetSetFile($offScript++, $this->basePath('assets/js/user_login.js'));
$this->inlineScript()->offsetSetFile($offScript++, $this->basePath('assets/js/user_register.js'));
$this->inlineScript()->offsetSetFile(100, $this->basePath('assets/js/custom3.js'));
$this->inlineScript()->captureStart();
echo <<<JS

JS;
$this->inlineScript()->captureEnd();
?>