        <footer>
            <div class="row">
                <div class="small-12 columns">
                    <div class="footer-logo text-center">
                        <img src="<?php echo get_template_directory_uri(); ?>/images<?php echo $_SESSION["imagefolder"] ?>/small-logo.png" alt="Logo" />
                    </div>
                </div>
                <div class="row">
                    <div class="large-12 columns text-center">
                        <p>
                            ©2014 LifeEmpowerment, Store Kongensgade 75b, 2.tv, 1264, København K. Tel 33 31 10 70.
                        </p>
                        <p>
                            <a href="#"><img src="<?php echo get_template_directory_uri(); ?>/images<?php echo $_SESSION["imagefolder"] ?>/linkedin.png" alt="LinkedIn"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <a href="#"><img src="<?php echo get_template_directory_uri(); ?>/images<?php echo $_SESSION["imagefolder"] ?>/facebook.png" alt="LinkedIn"></a>
                        </p>
                    </div>
                    
                  
                </div>
                
            </div>
        </footer>
        <script src="<?php echo get_template_directory_uri(); ?>/bower_components/jquery/dist/jquery.min.js"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/bower_components/foundation/js/foundation.min.js"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/js/app.js"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.slicknav.min.js"></script>
        <?php wp_footer(); ?>
    </body>
</html>
