<?php 
	/*
    *  Copyright (c) UPEI sbateman@upei.ca lrosa@upei.ca
    */
    // Gets the root folder
	$root_folder = substr(substr($_SERVER["REQUEST_URI"],1), 0, strpos(substr($_SERVER["REQUEST_URI"],1), "/"));
	// Sets the include path
	set_include_path("/var/www/");
	// Include the  require files
	require_once($root_folder . '/common.php');
    require_once($root_folder . '/components/user/class.user.php');
	require_once($root_folder . '/components/project/class.project.php');
	require_once($root_folder . '/components/permission/class.permission.php');
	require_once($root_folder . '/components/course/class.course.php');
	require_once($root_folder . '/components/userlog/class.userlog.php');
	
	//////////////////////////////////////////////////////////////////
    // This page offers an interface to manage assignments
    //////////////////////////////////////////////////////////////////
	
	//////////////////////////////////////////////////////////////////
    // Verify Session or Key
    //////////////////////////////////////////////////////////////////
	
    checkSession();
	
	//////////////////////////////////////////////////////////////////
    // Defining the user permission
    //////////////////////////////////////////////////////////////////
	$Permission = new Permission($_SESSION['user']);
	
	if (FALSE) {//!($Permission->GetPermissionToSeeAssignments())) {
		echo "Permission Denied";
	} else {
		
		$MainUserlog = new Userlog();
		$MainUserlog -> CloseAllOpenSectionsThatReachedTimeout();
		
		$User = new User();
		// Connect
		$mongo_client = new MongoClient();
		// select the database
		$database = $mongo_client -> codiad_database;
		// Select the collection
		$collection = $database -> users;
		
		$users = $User -> GetUsersInTheSameCoursesOfUser($_SESSION['user']);
		
		$user_types = $User -> GetUsersTypes();
		$student_user_type = $user_types[0];
		
		$compilation_errors = array();
		
		
		$error_to_log = "";
		
		$outputted_errors = array();
		$single_error = array();
		
		
		// Project
		$Project = new Project();
		$Assignment = array();
		
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<title>Compilation Errors</title>
		<meta charset="iso-8859-1">
		<!--[if lt IE 9]><script src="scripts/html5shiv.js"></script><![endif]-->
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<link href="../../../../themes/default/jquery-ui/jquery-ui-1.10.3.custom.vader/css/vader/jquery-ui-1.10.3.custom.css" rel="stylesheet">
		<script src="../../../../themes/default/jquery-ui/jquery-ui-1.10.3.custom.vader/js/jquery-ui-1.10.3.custom.js"></script>
		<script src="../../../highcharts/js/highcharts.js"></script>
		<script src="../../../highcharts/js/modules/exporting.js"></script>
		<script src="../../../highcharts/js/themes/gray.js"></script>
		<link rel="stylesheet" href="../styles/layout.css" type="text/css">
		<style>
			#feedback, #assignments_feedback {
				font-size: 1.4em;
			}
			#group_selectable  .ui-selecting, #assignments_selectable  .ui-selecting, #students_selectable .ui-selecting {
				background: #888888;
			}
			#group_selectable .ui-selected, #assignments_selectable .ui-selected, #students_selectable .ui-selected {
				background: #888888;
				color: white;
			}
			#group_selectable, #assignments_selectable, #students_selectable {
				list-style-type: none;
				margin: 0;
				padding: 0;
				width: 100%;
			}
			#group_selectable li, #assignments_selectable li, #students_selectable li {
				margin: 3px;
				padding: 0.4em;
				font-size: 1.0em;
				height: 14px;
			}
			h2 {
				font-size: 20px;
			}
		</style>
		<script>
			var students = new Array();
			var assignments = new Array();
			var group_by = 0;

			function set_chart_data(students, assignments, group_by) {
				var data_array = new Array();
				if (students.length == 0) {
					students = null;
				}
				if (assignments.length == 0) {
					assignments = null;
				}
				// [0] => students
				data_array[0] = students;
				

				$.ajax({
					type : "POST",
					url : 'controller.php?action=get_data_for_chart',
					data : {
						data_array : data_array
					}, 
					dataType : 'json',
					success : function(response) {
						data = response.students_with_counters;

						setChart(data, group_by);
					},
					error : function(response) {
						// <!---->
					}
				});
			}


			$(document).ready(function() {
				set_chart_data(students, assignments, group_by);
			});

			$(function() {

				$("#students_selectable").selectable({
					stop : function() {
						var all = false;
						students = new Array();
						$(".ui-selected", this).each(function() {
							var index = $(this).index();
							if (index == 0) {
								all = true;
							} else {
								var id = $(this).attr('id');
								students.push(id);
							}

						});

						if (all) {
							$("#students_selectable li").first().addClass("ui-selected").siblings().removeClass("ui-selected");
							students = new Array();
						}

						set_chart_data(students, assignments, group_by);

					}
				});
			});
		</script>

	</head>
	<body style="background-color: black;">
		<div id="container" style="width: 960px; height: 480px; margin: 0 auto">
		</div>
		<!-- content -->
		<div class="wrapper row2">

			<div id="container" class="clear">

				<!-- main content -->
				<div id="homepage" style="background-color: black; margin-left:auto; margin-right:auto; width:33%;">
					<!-- Services -->
					<section id="assignments_section" class="clear">
						<article class="one_third">
							<figure>
								<figcaption>
									<h2>Students</h2>
								</figcaption>
								<div id="students" style='background-color:#404040; min-height: 300px; width:290px;height:100%'>
									<ol id="students_selectable">
										<li id="all" class="ui-widget-content  ui-selected">
											All
										</li>
										<?
										foreach ($users as $user) {
											if ($user['type'] == $student_user_type) {
											?>

											<li id="<?=$user['username'] ?>" class="ui-widget-content">
												<?=$user['username'] ?>
											</li>

											<?
											}
										}
										?>
									</ol>
								</div>
							</figure>
						</article>
					</section>
					<!-- / Services -->
				</div>
				<!-- / content body -->
			</div>
		</div>
	</body>
</html>
<script type="text/javascript">
	function setChart(data, group_by) {
		console.log(JSON.stringify(data));

		var data_series = new Array();
		var x_axis = {
			categories : ['Students']
		};

		var plot_options = {
			column : {
				pointPadding : 0.2,
				borderWidth : 0
			}
		};

		for (var i = 0; i < data.length; i++) {
			var serie = {
				name : '' + data[i]['username'],
				data : [data[i]['count']]
			}
			data_series.push(serie);
		}
		

		$('#container').highcharts({
			chart : {
				type : 'column'
			},
			title : {
				text : 'Time spent in the system'
			},
			xAxis : x_axis,

			yAxis : {
				min : 0,
				title : {
					text : 'Hours'
				}
			},
			tooltip : {
				headerFormat : '<span style="font-size:10px">{point.key}</span><table>',
				pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.2f} minutes</b></td></tr>',
				footerFormat : '</table>',
				//shared: true,
				useHTML : true
			},
			plotOptions : plot_options,
			series : data_series
		});
	}
</script>
<?
}
?>