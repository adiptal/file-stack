<div class="main-box">
    <div class="overlayer"></div>
    <div class="intro-bg">
        <a href="#our-community" class="btn scroll"><i class="fas fa-chevron-down fa-3x"></i></a>
    </div>

    <div class="container-fluid" id="our-community">
        <div class="container mt-5">
            <div class="row">

                <div class="col-12">
                    <div class="section-header"><h2>Our Community</h2></div>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card-box">
                        <div class="card-bg bg-3"></div>
                        <div class="card-header">Apps</div>
                        <button class="btn btn-link text-danger float-right"><i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card-box">
                        <div class="card-bg bg-2"></div>
                        <div class="card-header">Contribution</div>
                        <button class="btn btn-link text-danger float-right"><i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card-box">
                        <div class="card-bg bg-1"></div>
                        <div class="card-header">Events</div>
                        <button class="btn btn-link text-danger float-right"><i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    $('.scroll').click(function(event){
        event.preventDefault();
        var section = $(this).attr('href');
        $('html , body').animate({
            scrollTop: $(section).offset().top - 69
        } , 1250);
    });
</script>