<form action="{{route('bulksend')}}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="exampleInputEmail1">Title</label>
            <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter Notification Title" name="title" required>
        </div>

        <br>

        <div class="form-group">
            <label for="exampleInputEmail1">Message</label>
            <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter Notification Description" name="message" required>
        </div>

        <br>

        <div class="form-group">
            <label for="exampleInputEmail1">Topic</label>
            <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter Notification Description" name="topic" >
        </div>

        <br>

        <div class="form-group">
            <label for="exampleInputEmail1">Page id</label>
            <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter Notification Description" name="pageid" >
        </div>

<br>
        <div class="form-group">
            <label for="exampleInputEmail1">Page Name</label>
            <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter Notification Description" name="pagename" >
        </div>
        <!-- <div class="form-group">
            <label for="exampleInputEmail1">Image Url</label>
            <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter image link" name="img">
        </div> -->
        <br>
        <button type="submit" class="btn btn-primary">Send Notification</button>
    </form>
    <!-- <script>
        function loadPhoto(event) {
            var reader = new FileReader();
            reader.onload = function () {
                var output = document.getElementById('photo');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script> -->