<?php 
	/*
    *  Copyright (c) UPEI sbateman@upei.ca lrosa@upei.ca
    */
    
	require_once('../../../../common.php');
    require_once('../../../user/class.user.php');
	require_once('../../../project/class.project.php');
//	require_once('class.assignment.php');
	require_once('../../../permission/class.permission.php');
	require_once('../../../course/class.course.php');
	require_once('../../class.userlog.php');
	
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
		//$User->users = getJSON('users.php');
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
			$(function() {

				$("#students_selectable").selectable({
					stop : function() {
						//var result = $("#select-result").empty();
						$(".ui-selected", this).each(function() {
							var index = $(this).attr('id');
							//result.append("<br> " + index);
							set_chart_data();
						});
					}
				});

				$("#assignments_selectable").selectable({
					stop : function() {
						//var result = $("#assignments_select-result").empty();
						$(".ui-selected", this).each(function() {
							var index = $(this).attr('id');
							//result.append("<br> " + index);
						});
					}
				});

				$("#group_selectable li").click(function() {
					$(this).addClass("ui-selected").siblings().removeClass("ui-selected");
					//var result = $("#assignments_select-result").empty();
					//var index = $(this).attr('id');
					//result.append("<br> " + index);
				});

			});
		</script>

	</head>
	<body>
		<div id="container" style="width: 960px; height: 360px; margin: 0 auto">

		</div>
		<!-- content -->
		<div class="wrapper row2">
			<div id="container" class="clear">

				<!-- main content -->
				<div id="homepage">
					<!-- Services -->
					<section id="services" class="clear">
						<article class="one_third">
							<figure>
								<figcaption>
									<h2>Assignments</h2>
								</figcaption>
								<div id="assignments" style='background-color:#404040; min-height: 300px; width:290px;height:100%'>
									<ol id="assignments_selectable">
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
										<li id="0" class="ui-widget-content">
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
	function setChart(janName) {
		$('#container').highcharts({
			chart : {
				type : 'column'
			},
			title : {
				text : 'Monthly Average Rainfall'
			},
			subtitle : {
				text : 'Source: WorldClimate.com'
			},
			xAxis : {
				categories : [janName, 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
			},
			yAxis : {
				min : 0,
				title : {
					text : 'Rainfall (mm)'
				}
			},
			tooltip : {
				headerFormat : '<span style="font-size:10px">{point.key}</span><table>',
				pointFormat : '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' + '<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>',
				footerFormat : '</table>',
				shared : true,
				useHTML : true
			},
			plotOptions : {
				column : {
					pointPadding : 0.2,
					borderWidth : 0
				}
			},
			series : [{
				name : 'Tokyo',
				data : [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4]

			}, {
				name : 'New York',
				data : [83.6, 78.8, 98.5, 93.4, 106.0, 84.5, 105.0, 104.3, 91.2, 83.5, 106.6, 92.3]

			}, {
				name : 'London',
				data : [48.9, 38.8, 39.3, 41.4, 47.0, 48.3, 59.0, 59.6, 52.4, 65.2, 59.3, 51.2]

			}, {
				name : 'Berlin',
				data : [42.4, 33.2, 34.5, 39.7, 52.6, 75.5, 57.4, 60.4, 47.6, 39.1, 46.8, 51.1]

			}]
		});
	}

	setChart('asd'); 
</script>
<script>
	function set_chart_data () {
		//var form = $('#chart_options_form');
		$.ajax({
			type : "POST",
			url : 'components/userlog/report/compilation_errors/controller.php?action=get_data_for_chart',
			data : form.serialize(),
			dataType : 'json',
			success : function(response) {
				console.log("asd");
				console.log(response.status);
				console.log(response.robert);
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
</script>
<?
}
?>