<?php
session_start();
include "conn.php";

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Query the database to get the display name and profile picture path based on the username
    $query = "SELECT display_name, profile_picture FROM users WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($display_name, $profile_picture_path);
    $stmt->fetch();
    $stmt->close();  // Close the initial query statement

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (isset($_POST['update_profile'])) {
            $newDisplayName = $_POST['new_display_name'];

            // Update the display name in the database
            $updateQuery = "UPDATE users SET display_name = ? WHERE username = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bind_param("ss", $newDisplayName, $username);
            $updateStmt->execute();
            $updateStmt->close();  // Close the update statement

            $display_name = $newDisplayName;
        }
    }
} else {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" type="text/css" href="styles.css">

    
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>        
		<link rel="stylesheet" href="https://unpkg.com/dropzone/dist/dropzone.css" />
		<link href="https://unpkg.com/cropperjs/dist/cropper.css" rel="stylesheet"/>
		<script src="https://unpkg.com/dropzone"></script>
		<script src="https://unpkg.com/cropperjs"></script>


</head>
<body>
    <div class="myHeader">
        <h1>Edit Your Profile</h1>
        <img id="headerImage" src="<?php echo $profile_picture_path; ?>" alt="Profile Picture" width="150">
    </div>

    <div class="profile-content">
        <h2>Edit Display Name</h2>
        <form action="editProfile.php" method="post" enctype="multipart/form-data">
            <label for="new_display_name">New Display Name:</label>
            <input type="text" id="new_display_name" name="new_display_name" value="<?php echo $display_name; ?>">
            <label for="profile_picture">Upload Profile Picture:</label>
            <input type="file" id="profile_picture" name="profile_picture" required>

            <!-- Display the cropped image preview -->
            <div class="image_area">
                <img id="image-preview" src="" alt="Image Preview" width="150" style="display: none;">
            </div>

            <button id="done" type="submit" name="update_profile">Update Profile</button>
        </form>
        <a href="dashboard.php"><button>Go to Dashboard</button></a>
    </div>

    <!-- Add this modal element to your HTML code -->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
			  	<div class="modal-dialog modal-lg" role="document">
			    	<div class="modal-content">
			      		<div class="modal-header">
			        		<h5 class="modal-title">Crop Image Before Upload</h5>
			        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          			<span aria-hidden="true">×</span>
			        		</button>
			      		</div>
			      		<div class="modal-body">
			        		<div class="img-container">
			            		<div class="row">
			                		<div class="col-md-8">
			                    		<img src="" id="sample_image" />
			                		</div>
			                		<div class="col-md-4">
			                    		<div class="preview"></div>
			                		</div>
			            		</div>
			        		</div>
			      		</div>
			      		<div class="modal-footer">
			      			<button type="button" id="crop" class="btn btn-primary">Crop</button>
			        		<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			      		</div>
			    	</div>
			  	</div>
			</div>			


    <script>
        var blob2;
        $(document).ready(function(){
            var $modal = $('#modal'); // Define $modal as a jQuery object for the modal element.
            
	        var image = document.getElementById('sample_image');
            var cropper;

            var pictureInput = document.getElementById("profile_picture");
            
            function handleEvent(event) {
                var files = event.target.files;

                var done = function(url){
                    image.src = url;
                    $modal.modal('show');
                };

                if(files && files.length > 0) {
                    reader = new FileReader();
                    reader.onload = function(event) {
                        done(reader.result);
                    };
                    reader.readAsDataURL(files[0]);
                }
            }

            var listenerObject = pictureInput.addEventListener("change", handleEvent);

            
            $modal.on('shown.bs.modal', function() {
                cropper = new Cropper(image, {
                    aspectRatio: 1,
                    viewMode: 3,
                    preview: '.preview'
                });
            }).on('hidden.bs.modal', function(){
                cropper.destroy();
                cropper = null;
            });


            
            $('#crop').click(function () {
            cropper.getCroppedCanvas({
                width: 400,
                height: 400,
            }).toBlob(function (blob) {
                blob2 = blob;
                const preview = document.getElementById("image-preview");
                var url = URL.createObjectURL(blob);
                $('#image-preview').attr('src', url);
                preview.style.display = "block";
                $modal.modal('hide');
            });
        });



        $('#done').click(function () {
            if(blob2 != null){
                var formData = new FormData();
                formData.append('croppedImage', blob2, 'cropped_image.jpg');
                $.ajax({
                    url: 'upload.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        console.log(response); // Successful response
                        window.alert(response);
                    }
                    ,error: function (jqXHR, textStatus, errorThrown) {
                        console.log("HERE");
                        window.alert("error");
                    }
                });
            }
        });





        });


    </script>
</body>
</html>