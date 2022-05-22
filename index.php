<html>
  <head>
    <title>Crop Before Uploading Image using Cropper JS & PHP
    </title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>
    <link href="https://unpkg.com/cropperjs/dist/cropper.css" rel="stylesheet" type="text/css"/>
    <style>
      .image_area {
        position: relative;
        width: 425px;
      }
      .preview {
        overflow: hidden;
        width: 160px;
        height: 160px;
        margin: 10px;
        border: 1px solid red;
      }
      .modal-lg {
        max-width: 1000px !important;
      }
      .overlay {
        position: absolute;
        top: 121px;
        max-height: 188px;
        left: 133px;
        background-color: rgba(255, 255, 255, 0.5);
        overflow: hidden;
        height:188px;
        transition: .5s ease;
        width: 183.33px;
        z-index: 1;
        opacity:0;
        border-radius: 50%;
      }
      .image_area:hover .overlay {
        cursor: pointer;
        width: 200.33px;
        top: 123px;
        max-height: 197px;
        left: 121px;
        border-radius: 50%;
        opacity: 1;
      }
      .text {
        color: #333;
        font-size: 14px;
        position: absolute;
        top: 40%;
        left: 50%;
        -webkit-transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
        text-align: center;
      }
      }
      img {
        display: block;
        max-width: 100%;
      }
      .preview {
        overflow: hidden;
        width: 160px;
        height: 160px;
        margin: 10px;
        border: 1px solid red;
      }
      .default-image{
      display: block;
      max-width: 196.67px;
      position: relative;    
      top: 123px;
      left: 123px;
      z-index: -1;
      border-radius: 50%;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <h5>Upload Image
      </h5>
        <div  class="image_area">
          <form method="post">
            <label for="upload_image">
              <div class="overlay">
                <p class="text">Click to change Image
                </p>
              </div>
              <div >
                <img class="default-image"  src="profile.jpg" id="uploaded_image" class="img-responsive img-circle" alt="User profile picture"/> 
              </div> 
              <input type="file" name="image" class="image" id="upload_image" accept="image/png,image/jpeg,image/jpg,image/jpg,image/gif,image/jfif,image/jpeg,image/svg,image/webp,image/bmp" style="display:none" />
            </label>
          </form>
        </div>
    </div>
    
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalLabel">Crop image
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×
              </span>
            </button>
          </div>
          <div class="modal-body">
            <div class="img-container">
              <div class="row">
                <div style="max-height:80vh !important"  class="col-md-8">  
                  <!--  default image where we will set the src via jquery-->
                  <img id="image">
                </div>
                <div class="col-md-4">
                  <div class="preview">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel
            </button>
            <button type="button" class="btn btn-primary" id="crop">Crop
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js">
    </script>
    <script src="https://unpkg.com/cropperjs" type="text/javascript">
    </script>
<script>
	var bs_modal = $('#modal');
var image = document.getElementById('image');
var cropper, reader, file;
$("body").on("change", ".image", function(e) {
	var files = e.target.files;
	var done = function(url) {
		image.src = url;
		bs_modal.modal('show');
	};
	if(files && files.length > 0) {
		file = files[0];
		if(URL) {
			done(URL.createObjectURL(file));
		} else if(FileReader) {
			reader = new FileReader();
			reader.onload = function(e) {
				done(reader.result);
			};
			reader.readAsDataURL(file);
		}
	}
});
bs_modal.on('shown.bs.modal', function() {
	cropper = new Cropper(image, {
		aspectRatio: 1,
		viewMode: 3,
		preview: '.preview'
	});
}).on('hidden.bs.modal', function() {
	cropper.destroy();
	cropper = null;
});
$("#crop").click(function() {
	canvas = cropper.getCroppedCanvas({
		width: 320,
		height: 320,
	});
	canvas.toBlob(function(blob) {
		url = URL.createObjectURL(blob);
		var reader = new FileReader();
		reader.readAsDataURL(blob);
		reader.onloadend = function() {
			var base64data = reader.result;
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "upload.php",
				data: {
					image: base64data
				},
				success: function(data) {
					bs_modal.modal('hide');
					alert("success upload image");
					$('#spinner').html('');
					$('#uploaded_image').attr('src', data);
				}
			});
		};
	});
});
$("#upload_image").change(function() {
	var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/jfif', 'image/jpeg', 'image/svg', 'image/webp', 'image/bmp'];
	var file = this.files[0];
	var fileType = file.type;
	var fileSize = file.size;
	if(!allowedTypes.includes(fileType)) {
		toastr.error('Please select an image file');
		$("#file").val('');
		return false;
	}
	if(fileSize > 60000000) {
		toastr.error('The file attached is larger than the maximum filesize allowed. Sorry!');
		$("#file").val('');
		return false;
	}
}); </script>
  </body>
</html>