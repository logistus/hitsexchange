<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css">
  <link href="{{ url('css/app.css') }}" rel="stylesheet">
  <title>Surfing @ {{ config("app.name") }}</title>
</head>

<body class="vh-100">
  <div class="d-flex flex-column h-100">
    <div style="height: 110px;" class="bg-light d-flex align-items-center justify-content-between">
      <div class="ms-3 invisible" id="website-owner">
        <div class="d-flex flex-column align-items-center justify-content-center">
          <span id="owner"></span>
          <img src="" id="gravatar" alt="" width="48" height="48" />
        </div>
      </div>
      <div>
        <div id="status"></div>
        <form method="POST" id="validate_view" class="d-none d-flex align-items-center">
          @csrf
          <div class="px-1 py-2 icons border bg-white" style="min-width: 320px;"></div>
        </form>
      </div>
      <div class="me-2 d-flex flex-column">
        <a href="{{ url('banners/click', session('selected_banner_id')) }}" id="banner_target" target="_blank" rel="noopener noreferrer">
          <img src="{{ session('selected_banner_image') }}" id="banner_image" width="468" height="60" />
        </a>
        <a href="{{ url('texts/click', session('selected_text_id')) }}" class="p-1 text-center my-1 text" target="_blank" rel="noopener noreferrer" style="
          text-decoration: none;
          color: {{ session('selected_text_color') }};
          background-color: {{ session('selected_bg_color') }};
          font-weight: {{ session('selected_text_bold') ? 'bold' : 'normal' }};
        ">{{ session('selected_text_body') }}</a>
      </div>
    </div>
    <div style="height: 10px;" id="timer_wrapper" class="bg-transparent w-100">
      <div id="progress-bar" class="bg-success h-100" style="width: 0%;"></div>
    </div>
    <div style="height: 40px;" class="bg-dark p-2 fs-6 d-flex justify-content-between">
      <div id="url-viewing" class="invisible">
        <span class="text-white">Viewing:</span>
        <a href="{{ session('selected_website_url') }}" class="link-light" id="url" target="_blank" rel="noopener noreferrer">{{ session('selected_website_url') }}</a> <i class="bi bi-box-arrow-up-right text-white" style="font-size: .8rem;"></i>
      </div>
      <div><a href="{{ url('/') }}" class="link-light" target="_top">Member's Area</a></div>
    </div>
    <iframe style="height: calc(100% - 210px); overflow-x:hidden;" frameborder="0" src="{{ session('selected_website_url') }}" id="surf_window"></iframe>
    <div id="bottom_bar" class="bg-primary text-white p-4 d-flex">
      <div class="me-3">Surfed This Session: <strong id="surfed_session">{{ session('surfed_session') }}</strong></div>
      <div class="me-3">Surfed Today: <strong id="surfed_today">{{ Auth::user()->surfed_today }}</strong></div>
      <div class="me-3">Credits: <strong id="credits">{{ Auth::user()->credits }}</strong></div>
    </div>
  </div>
</body>
<script src="{{ asset('js/jquery-3.6.0.js') }}"></script>

<script>
  let cid;

  function checkScreenSize() {
    // do not allow surf on small screens (below 800x600)
    if (window.innerWidth < 800 || window.innerHeight < 600) {
      $("body").empty();
      $("body").html("<div class='h-100 d-flex justify-content-center align-items-center'><p class='text-center'>Surfing on small screens is not possible at this moment.<br><a href='/'>Click here</a> to back to dashboard.</p></div>");
    }
  }

  function validateClick(id) {
    checkScreenSize();
    cid = id;
    $("#validate_view").submit();
  }

  $(function() {
    checkScreenSize();

    var timer = "{{ Auth::user()->type->surf_timer }}";
    var app_url = "{{ config('app.url') }}";
    var start_url = "{{ session('selected_website_url') }}";
    var surfed_session = "{{ session('surfed_session') }}";

    $("#status").text("Viewing start page");

    if (start_url.startsWith(app_url) || surfed_session == "0") {
      $("#url-viewing").removeClass("visible").addClass("invisible");
      $("#website-owner").removeClass("visible").addClass("invisible");
    } else {
      $("#url-viewing").removeClass("invisible").addClass("visible");
      $("#website-owner").removeClass("invisible").addClass("visible");
    }

    function startProgressBar() {
      $(".icons").html('<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
      $("#validate_view").addClass("d-none");
      $("#progress-bar").attr("style", "width: 0%");
      $("#progress-bar").animate({
        width: "100%"
      }, {
        duration: (timer * 1) * 1000
        , easing: "linear"
        , complete: function() {
          $("#validate_view").removeClass("d-none");
          $("#status").hide();
          $(".icons").load('/view_surf_icons');
        }
      });
    }

    startProgressBar();

    $("#validate_view").submit(function(e) {
      e.preventDefault();

      $.post("/validate_click/" + cid, {
        _token: $("[name='_token']").val()
      , }, function(response) {
        if (response.status == "ec") {
          location.href = "/";
        } else {
          //console.log(response);
          $("#status").show().html(response.status);
          $("#surf_window").attr("src", response.url);
          $("#url").text(response.url);
          $("#url").attr("href", response.url);
          $("#owner").text(response.website_owner_username);
          $("#gravatar").prop("src", response.website_owner_gravatar).prop("alt", response.website_owner_username);

          $("#banner_image").attr("src", response.banner_image);
          $("#banner_target").attr("href", "/banners/click/" + response.banner_id);

          $(".text").text(response.text_body);
          $(".text").attr("href", "/texts/click/" + response.text_id);
          $(".text").css("color", response.text_color);
          $(".text").css("background-color", response.text_bg_color);
          if (response.text_bold) {
            $(".text").css("font-weight", "bold");
          } else {
            $(".text").css("font-weight", "normal");
          }

          $("#surfed_session").text(parseInt($("#surfed_session").text()) + 1);
          $("#surfed_today").text(response.surfed_today);
          credits = response.credits * 1;
          $("#credits").text(credits.toFixed(2));
          if (response.url.startsWith(app_url)) {
            $("#url-viewing").removeClass("visible").addClass("invisible");
            $("#website-owner").removeClass("visible").addClass("invisible");
          } else {
            $("#url-viewing").removeClass("invisible").addClass("visible");
            $("#website-owner").removeClass("invisible").addClass("visible");
          }

          startProgressBar();
        }
      });

    });
  });

</script>

</html>
