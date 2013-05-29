<? $home = true; ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Cappd.me link shortener</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="cappd.me is a link shortener that expires">
    <meta name="author" content="Yu Chen Hou">

    <!-- Le styles -->
    <link href="/templates/css/bootstrap.css" rel="stylesheet">
	<link href="/templates/css/datepicker.css" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,500,300' rel='stylesheet' type='text/css'>
    <style type="text/css">
      body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
		font-family: 'Roboto', sans-serif;
      }

      .container {
        max-width: 60%;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
      }
      .form-url .form-url-heading,
      .form-url .checkbox {
        margin-bottom: 10px;
      }
	  .form-url-sub,{
		 margin-bottom: 5px;
	  }
      .form-url input[type="text"],
      .form-url input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
      }
	  #show_more,#expand,#hide{
		padding-left:15px;
		padding-bottom:10px;
	  }

    </style>
    <link href="css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="/templates/js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
                    <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">
                                   <link rel="shortcut icon" href="../assets/ico/favicon.png">
  </head>

  <body>

    <div class="container">
	  <?include 'header.php' ?>
      <form id="sub_form" class="form-url" action="/api/create" method="post">
        <input type="text" id="url" name="url" class="input-block-level" placeholder="Enter URL here">
		<div id="error_container" class="alert alert-error" style="display: none;"><strong>Warning! </strong><span id="error"></span></div>
		<div id="expand">
			<i class="icon-wrench"></i> 
			<a href="#">show options</a>
		</div>
		<div id="hide" style="display: none;">
			<i class="icon-wrench"></i> 
			<a href="#">hide options</a>
		</div>
		<div id="show_more" style="display: none;">
			<h5 class="form-url-sub"><abbr title="when this link stops working">expiration date</abbr></h2>
			<input type="datetime" id="expire_time" name="expire_time" data-date-format="mm/dd/yy" class="input-block-level datepicker" placeholder="leave empty to disable">
			<!-- <h5 class="form-url-sub"><abbr title="how many clicks per day">daily quota</abbr></h2>
			<input type="number" id="daily_cap" name="daily_cap" min="0" class="input-block-level" placeholder="leave empty to disable">-->
			<h5 class="form-url-sub"><abbr title="how many total clicks before the link expires">total quota</abbr></h2>
			<input type="number" id="total_cap" name="total_cap" min="0" class="input-block-level" placeholder="leave empty to disable">
		</div>
		<button class="btn btn-primary" type="submit">create</button>
		<div id="spinner"></div>

      </form>
	  <div id="spinner"></div>
	  <div id="result" style="display: none;">
	       <h5 class="form-url-sub">your generated link</h2>
	       <input type="text" id="posted_url" readonly="readonly" class="input-block-level">
		   <button class="btn btn-primary" id="restart">create another link</button>
	</div>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="/templates/js/jquery-2.0.1.min.js"></script>
    <script src="/templates/js/bootstrap.min.js"></script>
	<script src="/templates/js/bootstrap-datepicker.js"></script>
	<script src="/templates/js/spin.min.js"></script>
	<script>
	var nowTemp = new Date();
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	 
	var checkin = $('#expire_time').datepicker({
	  onRender: function(date) {
		return date.valueOf() < now.valueOf() ? 'disabled' : '';
	  }
	});
	var opts = {
	  lines: 17, // The number of lines to draw
	  length: 16, // The length of each line
	  width: 10, // The line thickness
	  radius: 30, // The radius of the inner circle
	  corners: 1, // Corner roundness (0..1)
	  rotate: 0, // The rotation offset
	  direction: 1, // 1: clockwise, -1: counterclockwise
	  color: '#000', // #rgb or #rrggbb
	  speed: 1, // Rounds per second
	  trail: 60, // Afterglow percentage
	  shadow: false, // Whether to render a shadow
	  hwaccel: false, // Whether to use hardware acceleration
	  className: 'spinner', // The CSS class to assign to the spinner
	  zIndex: 2e9, // The z-index (defaults to 2000000000)
	  top: 'auto', // Top position relative to parent in px
	  left: 'auto' // Left position relative to parent in px
	};
	var target = document.getElementById('spinner');
	var spinner = new Spinner(opts);
	
    $("#hide").hide();
    $("#expand").click(function ( event ) {
      event.preventDefault();
      $(this).hide();
	  $("#hide").show();
	  $("#show_more").show("slow");
    });
	$("#hide").click(function ( event ) {
      event.preventDefault();
      $(this).hide();
	  $("#expand").show();
	  $("#show_more").hide("slow");
    });
	$("#restart").click(function ( event ) {
      event.preventDefault();
	  $("#result").hide();
	  $("#sub_form").show("slow");
    });
	/* attach a submit handler to the form */
	$("#sub_form").submit(function(event) {
	event.preventDefault();
		var data = {
			url: $("#url").val(),
			expire_time: $("#expire_time").val(),
			//daily_cap: $("#daily_cap").val(),
			total_cap: $("#total_cap").val(),
        }

		spinner.spin(target);
		var posting  = $.post("/api/create",data);
		
		 /* Put the results in a div */
		posting.done(function( data ) {
		spinner.stop();
		
		if(data.error != null){
			$( "#error" ).empty().append(data.error);
		    $("#error_container").show("slow");
		return false;
		}else{
			$( "#error" ).empty();
		    $("#error_container").hide("slow");
		}
		
		$("#result").show("slow");
		$("#sub_form").hide();
		
		$( "#posted_url" ).val("http://cappd.me/" + data.permalink );
		});
		return false;
		});
</script>

  </body>
</html>