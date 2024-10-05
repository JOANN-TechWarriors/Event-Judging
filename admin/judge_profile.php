<?php 
include('session.php');


$judge_code=$_POST['judge_code'];

	$query = $conn->query("SELECT * FROM judges WHERE code='$judge_code'");
			$row = $query->fetch();
			$num_row = $query->rowcount();
            
            
            
            
		if( $num_row > 0 ) { 
		  
          
$judge_ctr=$row['judge_ctr'];
$subevent_id=$row['subevent_id'];

?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

<script>
<?php if (/* your condition here */) { ?>
	swal({
		title: "Success!",
		text: "Redirecting to judge panel...",
		type: "success",
		timer: 2000,
		showConfirmButton: false
	}, function(){
		window.location.href = "judge_panel.php?judge_ctr=<?php echo $judge_ctr; ?>&subevent_id=<?php echo $subevent_id; ?>";
	});
<?php } else { ?>
	swal({
		title: "Error!",
		text: "Wrong code",
		type: "error",
		confirmButtonText: "OK"
	}, function(){
		window.location = 'welcome.php';
	});
<?php } ?>
</script>
    
<?php }
?>


                                                