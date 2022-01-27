<?php include('db_connect.php');
$cat = $conn->query("SELECT * FROM room_categories");
$cat_arr = array();
while($row = $cat->fetch_assoc()){
	$cat_arr[$row['id']] = $row;
}
$room = $conn->query("SELECT * FROM rooms");
$room_arr = array();
while($row = $room->fetch_assoc()){
	$room_arr[$row['id']] = $row;
}
?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row mt-3">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body">
						<table class="table table-bordered">
							<thead>
								<th>#</th>
								<th>Category</th>
								<th>Reference</th>
								<th>Status</th>
								<th>Payment Slip</th>
								<th>Action</th>
							</thead>
							<tbody>
								<?php
								$i = 1;
								//variabel untuk menampilkan list kamar yang telah dibooking
								$checked = $conn->query("SELECT * FROM checked where status = 0 order by status desc, id asc");
								while($row=$checked->fetch_assoc()):
								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td class="text-center"><?php echo $cat_arr[$row['booked_cid']]['name'] ?></td>
									<td class=""><?php echo $row['ref_no'] ?></td>
									<td class="text-center"><span class="badge badge-warning">Booked</span></td>
									<td class=""><img src="<?php echo isset($row['cover_img']) ? '../assets/img/'.$row['cover_img'] :'' ?>" alt="" id="cimg"></td>
									<td class="text-center">
											<button class="btn btn-sm btn-primary check_out" type="button" data-id="<?php echo $row['id'] ?>">View</button>
									</td>
								</tr>
							<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
img#cimg{
	max-height: 10vh;
	max-width: 6vw;
}
</style>
<script>
	$('table').dataTable()
	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	$('.check_out').click(function(){
		uni_modal("Check Out","manage_check_out.php?checkout=1&id="+$(this).attr("data-id"))
	})
	$('#filter').submit(function(e){
		e.preventDefault()
		location.replace('index.php?page=check_in&category_id='+$(this).find('[name="category_id"]').val()+'&status='+$(this).find('[name="status"]').val())
	})
</script>