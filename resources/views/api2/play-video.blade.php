<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
  <meta http-equiv="Cache-control" content="no-cache">
  <meta http-equiv="Expires" content="-1">
  <title>Wao Collection</title>
</head>
<body>
  <div style="height:100%;width:100%;">
    <video id="videoId" style="top:0px;left:0px;height:calc(100% - 0px);width:100%;object-fit:fill;position:absolute;"
      src="{{ request()->video }}" poster="{{ request()->poster }}"
      loop autoplay controls muted>
      Your browser does not support the video tag.
    </video>
    <div id="videoPlaySound" style="position:fixed; display:none; height:100%;width:100%;z-index:10;">
      <div style="position:fixed; margin:20px; width:calc(100% - 55px); background:rgba(0, 0, 0, 0.5); color:white; border-radius:10px; text-align:center; font-weight:bold; font-size:18px">
        <div style="padding:10px 20px;">آواز کے لیے یہاں کلک کریں۔</div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-latest.min.js"></script>
  <script>
    function toggleUnmute(){
      $("#videoPlaySound").css('display', 'block')
    }

    $(document).ready(function(){
        setTimeout(toggleUnmute,1000);
    })

    $("#videoPlaySound").on('click',function(){
      video = document.getElementById("videoId");
      video.muted = false;
      $("#videoPlaySound").hide();
    });

</script>
</body>
</html>