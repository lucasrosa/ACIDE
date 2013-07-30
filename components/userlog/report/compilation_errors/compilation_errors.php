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
		<link rel="stylesheet" href="styles/layout.css" type="text/css">
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
				font-size: 1.4em;
				height: 18px;
			}
		</style>
		<script>
			var students = new Array();
			var assignments = new Array();
			var group_by = 0;

			function set_chart_data(students, assignments, group_by) {
				//var form = $('#chart_options_form');
				var data_array = new Array();
				if (students.length == 0) {
					students = null;
				}
				if (assignments.length == 0) {
					assignments = null;
				}
				// [0] => students
				data_array[0] = students;
				// [1] => assignments
				data_array[1] = assignments;
				// [2] => group_by
				data_array[2] = group_by;

				$.ajax({
					type : "POST",
					url : 'controller.php?action=get_data_for_chart',
					data : {
						data_array : data_array
					}, //form.serialize(),
					dataType : 'json',
					success : function(response) {
						//console.log("asd");
						//console.log(response.status);
						//console.log(response.outputted_errors);
						data = response.outputted_errors;
						setChart(data, group_by);
						/*
						 if (response.status == 'success') {
						 codiad.modal.unload();
						 codiad.message.success(i18n('Project updated'));
						 } else if (response.status == 'error_user_maximum_reached') {
						 codiad.message.error(i18n('Maximum limit of users reached.'));
						 } else if (response.status == 'error_database') {
						 codiad.message.error(i18n('Changes couldn\'t be saved on database.'));
						 }
						 */
					},
					error : function(response) {
						//codiad.modal.unload();
						//codiad.message.error(i18n('An unexpected error ocurred. Please try again.'));
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

				$("#assignments_selectable").selectable({
					stop : function() {
						var all = false;
						assignments = new Array();
						$(".ui-selected", this).each(function() {
							var index = $(this).index();
							if (index == 0) {
								all = true;
							} else {
								var id = $(this).attr('id');
								assignments.push(id);
							}
						});

						if (all) {
							$("#assignments_selectable li").first().addClass("ui-selected").siblings().removeClass("ui-selected");
							assignments = new Array();
						}

						set_chart_data(students, assignments, group_by);

					}
				});

				$("#group_selectable li").click(function() {
					$(this).addClass("ui-selected").siblings().removeClass("ui-selected");
					var id = $(this).attr('id');
					group_by = id;
					set_chart_data(students, assignments, group_by);
				});

			});
		</script>

	</head>
	<body style="background-color: black;">
		<div id="container" style="width: 960px; height: 360px; margin: 0 auto">

		</div>
		<!-- content -->
		<div class="wrapper row2">

			<div id="container" class="clear">

				<!-- main content -->
				<div id="homepage" style="background-color: black;">
					<!-- Services -->
					<section id="assignments_section" class="clear">
						<article class="one_third">
							<figure>
								<figcaption>
									<h2>Assignments</h2>
								</figcaption>
								<div id="assignments" style='background-color:#404040; min-height: 300px; width:290px;height:100%'>
									<ol id="assignments_selectable">
										<li id="all" class="ui-widget-content  ui-selected">
											All
										</li>
										<?
										//////////////////////////////////////////////////////////////////
										// LF: List all assignments which this user is the owner
										//////////////////////////////////////////////////////////////////
										$Project = new Project();
										//$assignments = $Project->GetAssignments();
										$current_user = $_SESSION['user'];
										$assignments = $Project->GetAssignmentsInTheSameCoursesOfUser($current_user);

										for ($k = 0; $k < count($assignments); $k++) {
										//$Course->id = $assignments[$k]['course'];

										?>
										<li id="<?=$assignments[$k]['name'] ?>" class="ui-widget-content">
											<?=$assignments[$k]['name'] ?>
										</li>
										<? } ?>
									</ol>
								</div>
							</figure>
						</article>
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
						<article class="one_third lastbox">
							<figure>
								<figcaption>
									<h2>Groups</h2>
								</figcaption>
								<div id="group_by" style='background-color:#404040; min-height: 300px; width:290px;height:100%'>
									<ol id="group_selectable">
										<li id="0" class="ui-widget-content ui-selected">
											Don't group
										</li>
										<li id="1" class="ui-widget-content">
											Group by students
										</li>
										<li id="2" class="ui-widget-content">
											Group by assignment
										</li>
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
		//console.log(JSON.stringify(data));
		
		var data_series = new Array();
		var x_axis = {
			categories : ['Errors']
		};

		var plot_options = {
			column : {
				pointPadding : 0.2,
				borderWidth : 0
			}
		};

		//console.log(JSON.stringify(data));
		if (group_by == 0) {
			for (var i = 0; i < data.length; i++) {
				var serie = {
					name : '' + data[i]['error'],
					data : [data[i]['count']]
				}
				data_series.push(serie);
			}
		} else if (group_by == 1 && data.length > 0) {
			var students_series = new Array();
			var x_categories = new Array();
			//console.log(JSON.stringify(data));
			for (var i = 0; i < data.length; i++) {
				x_categories.push(data[i]['error']);
			}
			
			for (var i = 0; i < data[0]['users'].length; i++) {
				var student = new Array();
				student['username'] = data[0]['users'][i]['username'];
				students_series.push(student);
			}
			
			
			for (var x = 0; x < students_series.length; x++) {
				var counters = new Array();

				for (var o = 0; o < data.length; o++) {
					for (var m = 0; m < data[o]['users'].length; m++) {
						if (data[o]['users'][m]['username'] == students_series[x]['username']) {
							counters.push(data[o]['users'][m]['count']);
						}
					}
				}

				var this_student = new Array();
				//console.log(JSON.stringify(students_series[x]['username']));
				//console.log(JSON.stringify(counters));
				this_student[0] = students_series[x]['username'];
				this_student[1] = counters;
				//console.log(JSON.stringify(this_student));
				students_series[x] = this_student;
				//console.log("Student "+ x + ": "+students_series[x]['username']);
			}
			// console.log(JSON.stringify(data[0]['users']));
			//console.log(JSON.stringify(students_series));

			for (var x = 0; x < students_series.length; x++) {
				var serie = {
					name : '' + students_series[x][0],
					data : students_series[x][1]
				}
				data_series.push(serie);
			}

			// Set the plot options :
			plot_options = {
				column : {
					stacking : 'normal',
					dataLabels : {
						enabled : true,
						color : (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
					}
				}
			};
			// set the x axis
			x_axis = {
				categories : x_categories
			};
		}

		$('#container').highcharts({
			chart : {
				type : 'column'
			},
			title : {
				text : 'Compilation errors of all students'
			},
			/*
			 subtitle : {
			 text : '...'
			 },
			 */

			xAxis : x_axis,

			yAxis : {
				min : 0,
				title : {
					text : 'Number of occurrences'
				}
			},
			tooltip : {
				headerFormat : '<span style="font-size:10px">{point.key}</span><table>',
				pointFormat : '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' + '<td style="padding:0"><b>{point.y} </b></td></tr>',
				footerFormat : '</table>',
				//shared: true,
				useHTML : true
			},
			plotOptions : plot_options,
			series : data_series
		});
	}

	//setChart('asd', 12);
</script>
<?
}
?>