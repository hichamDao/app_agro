<style type="text/css">
@import url("https://foodmax-group.com/css/login.css");
	</style>



<div class="modal video-modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModal" aria-hidden="true" style="display: none;">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header">
					Sign In &amp; Sign Up
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>						
				</div>
				<section>
					<div class="modal-body">
						<div class="w3_login_module">
							<div class="module form-module">
							  <div class="toggle"><i class="fa fa-times fa-pencil"></i>
								<div class="tooltip">Register</div>
							  </div>
							  <div  class="form" style="display: block;">
								<h3>Login to your account</h3>
								<form  action="" method="post" id="userform">
								  <input type="text" name="login" placeholder="Username" required="">
								  <input type="password" name="pass" placeholder="Password" required="">
								  <input type="submit" value="Login">
								</form>
                <div id="loader8" class="loading8 hidden8">&nbsp;</div>
                 
							  </div>
							  <div action="" class="form" style="display: none;" >
								<h3>Create an account</h3>
								<form  action="#" method="post" id="userform2">
								  <input type="text" name="username" placeholder="Username" required="">
								  <input type="password" name="password" placeholder="Password" required="">
								  <input type="email" name="email" placeholder="Email Address" required="">
								  <input type="text" name="phone" placeholder="Phone Number" required="">
                  <input type="hidden" name="date" value="<?php echo date("j-n-Y"); ?>">
								  <input type="submit" value="Register">
								</form>
                <div id="loader10" class="loading8 hidden8">&nbsp;</div>
                
							  </div>
							  <!-- <div class="cta"><a href="#">Forgot your password?</a></div> -->
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>
	</div>



	  <script type="text/javascript" src="../js/jquery.min.js"></script>
 
	  <script type="text/javascript" src="../js/bootstrap.min.js"></script>
		<script src="../js/jquery.magnific-popup.js" type="text/javascript"></script>
		<script>
		$('.toggle').click(function(){
		  // Switches the Icon
		  $(this).children('i').toggleClass('fa-pencil');
		  // Switches the forms  
		  $('.form').animate({
			height: "toggle",
			'padding-top': 'toggle',
			'padding-bottom': 'toggle',
			opacity: "toggle"
		  }, "slow");
		});
	</script>
  <script type="text/javascript">
$( document ).ready(function() {

$("#userform2").submit(function(e){ 

    e.preventDefault(); 

    var donnees = $(this).serialize(); 
   
            $.ajax({

       url : '../admin/login_control.php',
       type : 'POST', 
       data : donnees,
       dataType : 'html',
      
     success: function (result) {
     $("#loader10").removeClass('hidden8');
     
            }
            });
     setTimeout(function() {
    //$("#url_div").load("https://www.foodmax-group.com/home");
    document.location.href="https://foodmax-group.com/";
    $("#loader10").addClass('hidden8');
  }, 800);
        
        return false;

    });




});

</script>
 <script type="text/javascript">
$( document ).ready(function() {

$("#userform").submit(function(e){ 

    e.preventDefault(); 
   
    var donnees = $(this).serialize(); 
   //$(this).slideUp();
            $.ajax({

       url : '../admin/login_control.php',
       type : 'POST', 
       data : donnees,
       dataType : 'html',
      
    success: function (result) {
    $("#loader8").removeClass('hidden8');
    $('#resultat8').html(result);  
            }
                
            });
    setTimeout(function() {
    //$("#url_div").load("https://foodmax-group.com/home");
    document.location.href="https://foodmax-group.com/";
    $("#loader8").addClass('hidden8');
  }, 800);
        
        return false;

    });




});

</script>
</body>
</html>