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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            title: 'Success!',
            text: 'Redirecting...',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            window.location.href = "judge_panel.php?judge_ctr=<?php echo $judge_ctr; ?>&subevent_id=<?php echo $subevent_id; ?>";
        });
    </script>
<?php
} else {
?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            title: 'Error!',
            text: 'Wrong code',
            icon: 'error',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location = 'welcome.php';
        });
    </script>
<?php
}
?>


                                                